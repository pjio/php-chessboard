<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Pieces;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Board\Chessboard;

class ChessboardPrinter
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

    public function print(Chessboard $chessboard): string
    {
        $flatBoard = $this->flatten($chessboard);
        return $this->stringify($flatBoard);
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
}
