<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Pieces;
use Pjio\Chessboard\Pieces\AbstractPiece;
use Pjio\Chessboard\Exception\UnserializeException;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Board\Chessboard;

class ChessboardSerializer
{
    private const PLAYER_STR = [
        White::class => 'w',
        Black::class => 'b',
    ];

    private const PIECE_STR = [
        Pieces\Bishop::class => 'b',
        Pieces\King::class   => 'K',
        Pieces\Knight::class => 'k',
        Pieces\Pawn::class   => 'p',
        Pieces\Queen::class  => 'Q',
        Pieces\Rook::class   => 'r',
    ];

    private array $players;
    private array $pieceFQCN;

    public function serialize(Chessboard $chessboard): string
    {
        $flatBoard = $this->flatten($chessboard);
        return $this->stringify($flatBoard);
    }

    public function unserialize(string $str): Chessboard
    {
        $rows = explode("\n", $str);

        if (count($rows) !== 8) {
            throw new UnserializeException(sprintf('Invalid count of rows! (%d)', count($rows)));
        }

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

        return new Chessboard($pieces);
    }

    private function flatten(Chessboard $chessboard): array
    {
        $flatBoard = array_fill(0, 64, null);

        /** @var Pieces\AbstractPiece $piece */
        foreach ($chessboard->getPiecesIterator() as $piece) {
            $square = $piece->getSquare();
            $index = $this->calcIndex($square->getFile(), $square->getRank());
            $flatBoard[$index] = $piece;
        }

        return $flatBoard;
    }

    private function stringify(array $flatBoard): string
    {
        $rows = [];

        for ($rank = Square::RANK_8; $rank >= Square::RANK_1; $rank--) {
            $row = [];
            for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
                $index = $this->calcIndex($file, $rank);
                $piece = $flatBoard[$index];

                if ($piece instanceof Pieces\AbstractPiece) {
                    $str = sprintf(
                        '%s%s',
                        self::PLAYER_STR[get_class($piece->getPlayer())],
                        self::PIECE_STR[get_class($piece)]
                    );
                } else {
                    $str = '  ';
                }

                $row[] = $str;
            }

            $rows[] = implode(' ', $row);
        }

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
            $pieceStr = substr($row, $file * 3, 2);
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
