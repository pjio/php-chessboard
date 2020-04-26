<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Piece;
use Pjio\Chessboard\Piece\AbstractPiece;
use Pjio\Chessboard\Exception\UnserializeException;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Black;

class ChessboardSerializer
{
    private const ANSI_BG_BLACK = "\033[48;5;242m";
    private const ANSI_BG_WHITE = "\033[48;5;248m";
    private const ANSI_RESET    = "\033[0m";

    private const ANSI_FG = [
        White::class => "\033[38;5;255m\033[1m",
        Black::class => "\033[38;5;232m\033[1m",
    ];

    private const PLAYER_STR = [
        White::class => 'w',
        Black::class => 'b',
    ];

    private const PIECE_STR = [
        Piece\Bishop::class => 'b',
        Piece\King::class   => 'K',
        Piece\Knight::class => 'k',
        Piece\Pawn::class   => 'p',
        Piece\Queen::class  => 'Q',
        Piece\Rook::class   => 'r',
    ];

    private const DESERIALIZE_OFFSET_TOP = 2;
    private const DESERIALIZE_OFFSET_LEFT = 4;

    private array $players;
    private array $pieceFQCN;

    public function serialize(Chessboard $chessboard, bool $colors = false): string
    {
        $flatBoard = $this->flatten($chessboard);
        return $this->stringify($flatBoard, $colors);
    }

    public function unserialize(string $str, int $plyCount = 0): Chessboard
    {
        $rows = explode("\n", $str);

        if (count($rows) !== 12) {
            throw new UnserializeException(sprintf('Invalid count of rows! (%d)', count($rows)));
        }

        $rows = array_slice($rows, self::DESERIALIZE_OFFSET_TOP, 8);

        $this->pieceFQCN = array_flip(self::PIECE_STR);
        $this->players = [];
        foreach (self::PLAYER_STR as $fqcn => $identifier) {
            $this->players[$identifier] = new $fqcn();
        }

        $pieces = [];
        for ($rank = Square::RANK_1; $rank <= Square::RANK_8; $rank++) {
            $index = 7 - $rank;
            $pieces = array_merge($pieces, $this->parseRow($rows[$index], $rank));
        }

        return new Chessboard($pieces, $plyCount);
    }

    private function flatten(Chessboard $chessboard): array
    {
        $flatBoard = array_fill(0, 64, null);

        /** @var Pieces\AbstractPiece $piece */
        foreach ($chessboard->getPiecesIterator() as $piece) {
            $square = $piece->getSquare();
            if ($square !== null) {
                $index = $this->calcIndex($square->getFile(), $square->getRank());
                $flatBoard[$index] = $piece;
            }
        }

        return $flatBoard;
    }

    private function stringify(array $flatBoard, bool $colors): string
    {
        $header1 = '    A B C D E F G H';
        $header2 = '   /----------------\\';
        $footer1 = '   \----------------/';
        $footer2 = '     A B C D E F G H';

        $rows = [$header1, $header2];

        $fgColor = '';
        $bgColor = '';
        $ansiReset = $colors ? self::ANSI_RESET : '';

        for ($rank = Square::RANK_8; $rank >= Square::RANK_1; $rank--) {
            $row = [];
            for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
                $index = $this->calcIndex($file, $rank);
                $piece = $flatBoard[$index];

                if ($colors) {
                    $blackSquare = ($file + $rank) % 2 === 0;
                    $bgColor = $blackSquare ? self::ANSI_BG_BLACK : self::ANSI_BG_WHITE;
                }

                if ($piece instanceof Piece\AbstractPiece) {
                    if ($colors) {
                        $fgColor = self::ANSI_FG[get_class($piece->getPlayer())];
                    }

                    $str = sprintf(
                        '%s%s%s%s',
                        $fgColor,
                        $bgColor,
                        self::PLAYER_STR[get_class($piece->getPlayer())],
                        self::PIECE_STR[get_class($piece)]
                    );
                } else {
                    $str = sprintf('%s  ', $bgColor);
                }

                $row[] = $str;
            }

            $rows[] = sprintf(' %d |%s%s| %d', $rank + 1, implode('', $row), $ansiReset, $rank + 1);
        }

        $rows[] = $footer1;
        $rows[] = $footer2;

        return implode("\n", $rows);
    }

    private function calcIndex(int $file, int $rank): int
    {
        return $rank * 8 + $file;
    }

    private function parseRow(string $row, int $rank): array
    {
        if (strlen($row) !== 23) {
            throw new UnserializeException(sprintf('Invalid length of row! (%d, "%s")', strlen($row), $row));
        }

        $pieces = [];
        for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
            $pieceStr = substr($row, self::DESERIALIZE_OFFSET_LEFT + ($file * 2), 2);
            if (trim($pieceStr) !== '') {
                $pieces[] = $this->parsePieceString($pieceStr, $file, $rank);
            }
        }

        return $pieces;
    }

    private function parsePieceString(string $pieceStr, int $file, int $rank): AbstractPiece
    {
        if (strlen($pieceStr) !== 2) {
            throw new UnserializeException(sprintf('Invalid length of pieceStr! (%d, "%s")', strlen($pieceStr), $row));
        }

        if (!isset($this->players[$pieceStr[0]])) {
            throw new UnserializeException(sprintf('Invalid player identifier "%s")', $pieceStr[0]));
        }

        $player = $this->players[$pieceStr[0]];
        $square = new Square($file, $rank);

        if (!isset($this->pieceFQCN[$pieceStr[1]])) {
            throw new UnserializeException(sprintf('Invalid piece identifier "%s")', $pieceStr[1]));
        }

        $fqcn = $this->pieceFQCN[$pieceStr[1]];

        return new $fqcn($player, $square);
    }
}
