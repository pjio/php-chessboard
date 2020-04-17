<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Pieces\Bishop;
use Pjio\Chessboard\Player;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Pieces\King;
use Pjio\Chessboard\Pieces\Knight;
use Pjio\Chessboard\Pieces\Pawn;
use Pjio\Chessboard\Pieces\Queen;
use Pjio\Chessboard\Pieces\Rook;
use Pjio\Chessboard\Board\Chessboard;

class ChessboardFactory
{
    public function createNewChessboard(White $white, Black $black): Chessboard
    {
        $pieces = [
            new King($white, new Square(Square::FILE_E, Square::RANK_1)),
            new Queen($white, new Square(Square::FILE_D, Square::RANK_1)),
            ...$this->generatePieces(
                Rook::class,
                $white,
                Square::RANK_1,
                [Square::FILE_A, Square::FILE_H]
            ),
            ...$this->generatePieces(
                Knight::class,
                $white,
                Square::RANK_1,
                [Square::FILE_B, Square::FILE_G]
            ),
            ...$this->generatePieces(
                Bishop::class,
                $white,
                Square::RANK_1,
                [Square::FILE_C, Square::FILE_F]
            ),
            ...$this->generatePieces(
                Pawn::class,
                $white,
                Square::RANK_2,
                [Square::FILE_A, Square::FILE_B, Square::FILE_C, Square::FILE_D,
                 Square::FILE_E, Square::FILE_F, Square::FILE_G, Square::FILE_H]
            ),
            new King($black, new Square(Square::FILE_E, Square::RANK_8)),
            new Queen($black, new Square(Square::FILE_D, Square::RANK_8)),
            ...$this->generatePieces(
                Rook::class,
                $black,
                Square::RANK_8,
                [Square::FILE_A, Square::FILE_H]
            ),
            ...$this->generatePieces(
                Knight::class,
                $black,
                Square::RANK_8,
                [Square::FILE_B, Square::FILE_G]
            ),
            ...$this->generatePieces(
                Bishop::class,
                $black,
                Square::RANK_8,
                [Square::FILE_C, Square::FILE_F]
            ),
            ...$this->generatePieces(
                Pawn::class,
                $black,
                Square::RANK_7,
                [Square::FILE_A, Square::FILE_B, Square::FILE_C, Square::FILE_D,
                 Square::FILE_E, Square::FILE_F, Square::FILE_G, Square::FILE_H]
            ),
        ];


        return new Chessboard($pieces);
    }

    private function generatePieces(string $fqcn, Player $player, int $rank, array $files): array
    {
        $pieces = [];

        foreach ($files as $file) {
            $pieces[] = new $fqcn($player, new Square($file, $rank));
        }

        return $pieces;
    }
}
