<?php
namespace Tests;

require_once __DIR__ . '/../Pieces/PieceImpl.php';

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Exception\MultiplePiecesOnSquareException;
use Pjio\Chessboard\Board\Square;
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
        $pieceA = new PieceImpl(new Black(), $this->squareA);
        $pieceB = new PieceImpl(new Black(), $this->squareB);
        $pieceC = new PieceImpl(new Black(), $this->squareC);
        $pieceD = new PieceImpl(new Black(), $this->squareD);

        $chessboard = new Chessboard([$pieceA, $pieceB, $pieceC, $pieceD]);

        $squareSearch = new Square(Square::FILE_D, Square::RANK_5);
        $pieceFound = $chessboard->getPieceBySquare($squareSearch);

        $this->assertSame($pieceC, $pieceFound);
    }

    public function testGetPieceBySquareNotFound()
    {
        $pieceA = new PieceImpl(new Black(), $this->squareA);
        $pieceB = new PieceImpl(new Black(), $this->squareB);
        $pieceC = new PieceImpl(new Black(), $this->squareC);
        $pieceD = new PieceImpl(new Black(), $this->squareD);

        $chessboard = new Chessboard([$pieceA, $pieceB, $pieceC, $pieceD]);

        $squareSearch = new Square(Square::FILE_B, Square::RANK_5);
        $pieceFound = $chessboard->getPieceBySquare($squareSearch);

        $this->assertNull($pieceFound);
    }

    public function testCheckSquareIsFreeReturnsTrue()
    {
        $pieceA = new PieceImpl(new Black(), $this->squareA);
        $pieceB = new PieceImpl(new Black(), $this->squareB);

        $chessboard = new Chessboard([$pieceA, $pieceB]);

        $this->assertTrue($chessboard->checkSquareIsFree($this->squareC));
    }

    public function testCheckSquareIsFreeReturnsFalse()
    {
        $pieceA = new PieceImpl(new Black(), $this->squareA);
        $chessboard = new Chessboard([$pieceA]);
        $this->assertFalse($chessboard->checkSquareIsFree($this->squareA));
    }

    public function testMultiplePiecesOnSquareExceptionIsThrown()
    {
        $this->expectException(MultiplePiecesOnSquareException::class);

        $pieceA = new PieceImpl(new Black(), $this->squareA);
        $pieceB = new PieceImpl(new Black(), $this->squareA);

        $chessboard = new Chessboard([$pieceA, $pieceB]);
    }

    public function testDeepClone()
    {
        $pieceA = new PieceImpl(new Black(), $this->squareA);
        $pieceB = new PieceImpl(new Black(), $this->squareB);

        $chessboard = new Chessboard([$pieceA, $pieceB]);

        $clonedChessboard = clone $chessboard;

        // Moving a piece on a previously occupied square tests that the cloned pieces
        // share the same Chessboard instance
        $piece1 = $chessboard->getPieceBySquare($this->squareA);
        $piece1->setSquare($this->squareC);
        $piece2 = $chessboard->getPieceBySquare($this->squareB);
        $piece2->setSquare($this->squareA); // would throw an exception if Chessboard wasn't
                                            // excluded from the deep copy

        // The pieces on the cloned board should not be affected
        $clonedPiece1 = $clonedChessboard->getPieceBySquare($this->squareB);
        $clonedPiece2 = $clonedChessboard->getPieceBySquare($this->squareC);

        $this->assertNotNull($clonedPiece1);
        $this->assertNull($clonedPiece2);
    }
}
