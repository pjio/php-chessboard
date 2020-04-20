<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Exception\MultiplePiecesOnSquareException;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Pieces\Piece;
use Pjio\Chessboard\Board\Chessboard;

class ChessboardTest extends TestCase
{
    private Chessboard $chessboard;
    private Square $squareA;
    private Square $squareB;
    private Square $squareC;
    private Square $squareD;

    public function setUp(): void
    {
        $this->squareA = new Square(Square::FILE_E, Square::RANK_7);
        $this->squareB = new Square(Square::FILE_A, Square::RANK_1);
        $this->squareC = new Square(Square::FILE_D, Square::RANK_5);
        $this->squareD = new Square(Square::FILE_H, Square::RANK_3);
    }

    public function testGetPieceBySquare()
    {
        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareB);
        $pieceC = new Piece(new Black(), $this->squareC);
        $pieceD = new Piece(new Black(), $this->squareD);

        $chessboard = new Chessboard([$pieceA, $pieceB, $pieceC, $pieceD]);

        $squareSearch = new Square(Square::FILE_D, Square::RANK_5);
        $pieceFound = $chessboard->getPieceBySquare($squareSearch);

        $this->assertSame($pieceC, $pieceFound);
    }

    public function testGetPieceBySquareNotFound()
    {
        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareB);
        $pieceC = new Piece(new Black(), $this->squareC);
        $pieceD = new Piece(new Black(), $this->squareD);

        $chessboard = new Chessboard([$pieceA, $pieceB, $pieceC, $pieceD]);

        $squareSearch = new Square(Square::FILE_B, Square::RANK_5);
        $pieceFound = $chessboard->getPieceBySquare($squareSearch);

        $this->assertNull($pieceFound);
    }

    public function testCheckSquareIsFreeReturnsTrue()
    {
        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareB);

        $chessboard = new Chessboard([$pieceA, $pieceB]);

        $this->assertTrue($chessboard->checkSquareIsFree($this->squareC));
    }

    public function testCheckSquareIsFreeReturnsFalse()
    {
        $pieceA = new Piece(new Black(), $this->squareA);
        $chessboard = new Chessboard([$pieceA]);
        $this->assertFalse($chessboard->checkSquareIsFree($this->squareA));
    }

    public function testMultiplePiecesOnSquareExceptionIsThrown()
    {
        $this->expectException(MultiplePiecesOnSquareException::class);

        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareA);

        $chessboard = new Chessboard([$pieceA, $pieceB]);
    }
}
