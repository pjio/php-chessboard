<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Pieces\Piece;
use Pjio\Chessboard\Board\Chessboard;

class ChessboardTest extends TestCase
{
    private Chessboard $chessboard;

    public function testGetPieceBySquare()
    {
        $squareA = new Square(Square::FILE_E, Square::RANK_7);
        $squareB = new Square(Square::FILE_A, Square::RANK_1);
        $squareC = new Square(Square::FILE_D, Square::RANK_5);
        $squareD = new Square(Square::FILE_H, Square::RANK_3);

        $pieceA = new Piece(new Black(), $squareA);
        $pieceB = new Piece(new Black(), $squareB);
        $pieceC = new Piece(new Black(), $squareC);
        $pieceD = new Piece(new Black(), $squareD);

        $chessboard = new Chessboard([$pieceA, $pieceB, $pieceC, $pieceD]);

        $squareSearch = new Square(Square::FILE_D, Square::RANK_5);
        $pieceFound = $chessboard->getPieceBySquare($squareSearch);

        $this->assertSame($pieceC, $pieceFound);
    }

    public function testGetPieceBySquareNotFound()
    {
        $squareA = new Square(Square::FILE_E, Square::RANK_7);
        $squareB = new Square(Square::FILE_A, Square::RANK_1);
        $squareC = new Square(Square::FILE_D, Square::RANK_5);
        $squareD = new Square(Square::FILE_H, Square::RANK_3);

        $pieceA = new Piece(new Black(), $squareA);
        $pieceB = new Piece(new Black(), $squareB);
        $pieceC = new Piece(new Black(), $squareC);
        $pieceD = new Piece(new Black(), $squareD);

        $chessboard = new Chessboard([$pieceA, $pieceB, $pieceC, $pieceD]);

        $squareSearch = new Square(Square::FILE_B, Square::RANK_5);
        $pieceFound = $chessboard->getPieceBySquare($squareSearch);

        $this->assertNull($pieceFound);
    }
}
