<?php
namespace Pieces;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\SquareIsOccupiedException;
use Pjio\Chessboard\Pieces\Piece;
use Pjio\Chessboard\Black;

class PieceTest extends TestCase
{
    private Square $squareA;
    private Square $squareB;
    private Square $squareC;

    public function setUp(): void
    {
        $this->squareA = new Square(Square::FILE_E, Square::RANK_7);
        $this->squareB = new Square(Square::FILE_A, Square::RANK_1);
        $this->squareC = new Square(Square::FILE_D, Square::RANK_5);
    }

    public function testMovePiece()
    {
        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareB);

        $chessboard = new Chessboard([$pieceA, $pieceB]);

        $pieceA->setSquare($this->squareC);

        $this->assertSame($this->squareC, $pieceA->getSquare(), 'Move to free square failed');
    }

    public function testSquareIsOccupiedExceptionIsThrown()
    {
        $this->expectException(SquareIsOccupiedException::class);

        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareB);

        $chessboard = new Chessboard([$pieceA, $pieceB]);

        $pieceA->setSquare($this->squareB);
    }

    public function testRemoveFromBoard()
    {
        $pieceA = new Piece(new Black(), $this->squareA);
        $pieceB = new Piece(new Black(), $this->squareB);

        $chessboard = new Chessboard([$pieceA, $pieceB]);

        $pieceB->removeFromBoard();

        $pieceA->setSquare($this->squareB);

        $this->assertSame($this->squareB, $pieceA->getSquare(), 'Move to previously occupied square failed');
    }
}
