<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Square;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Pieces;
use Pjio\Chessboard\Chessboard;
use Pjio\Chessboard\ChessboardPrinter;

class ChessboardPrinterTest extends TestCase
{
    private ChessboardPrinter $chessboardPrinter;
    private White $white;
    private Black $black;

    public function setUp(): void
    {
        $this->chessboardPrinter = new ChessboardPrinter();
        $this->white = new White();
        $this->black = new Black();
    }

    public function testEmptyBoard()
    {
        $pieces = [];
        $chessboard = new Chessboard($pieces);

        $expected = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;

        $actual = $this->chessboardPrinter->print($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testCorners()
    {
        $pieces = [
            new Pieces\Pawn($this->white, new Square(Square::FILE_A, Square::RANK_8)),
            new Pieces\Rook($this->white, new Square(Square::FILE_H, Square::RANK_8)),
            new Pieces\Bishop($this->white, new Square(Square::FILE_A, Square::RANK_1)),
            new Pieces\Knight($this->white, new Square(Square::FILE_H, Square::RANK_1)),
        ];
        $chessboard = new Chessboard($pieces);

        $expected = <<< EOF
wp                   wr
                       
                       
                       
                       
                       
                       
wb                   wk
EOF;

        $actual = $this->chessboardPrinter->print($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testFullBoard()
    {
        $pieces = [
            new Pieces\Pawn($this->black, new Square(Square::FILE_A, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_B, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_C, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_D, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_E, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_F, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_G, Square::RANK_7)),
            new Pieces\Pawn($this->black, new Square(Square::FILE_H, Square::RANK_7)),

            new Pieces\Rook($this->black, new Square(Square::FILE_A, Square::RANK_8)),
            new Pieces\Knight($this->black, new Square(Square::FILE_B, Square::RANK_8)),
            new Pieces\Bishop($this->black, new Square(Square::FILE_C, Square::RANK_8)),
            new Pieces\Queen($this->black, new Square(Square::FILE_D, Square::RANK_8)),
            new Pieces\King($this->black, new Square(Square::FILE_E, Square::RANK_8)),
            new Pieces\Bishop($this->black, new Square(Square::FILE_F, Square::RANK_8)),
            new Pieces\Knight($this->black, new Square(Square::FILE_G, Square::RANK_8)),
            new Pieces\Rook($this->black, new Square(Square::FILE_H, Square::RANK_8)),

            new Pieces\Pawn($this->white, new Square(Square::FILE_A, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_B, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_C, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_D, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_E, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_F, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_G, Square::RANK_2)),
            new Pieces\Pawn($this->white, new Square(Square::FILE_H, Square::RANK_2)),

            new Pieces\Rook($this->white, new Square(Square::FILE_A, Square::RANK_1)),
            new Pieces\Knight($this->white, new Square(Square::FILE_B, Square::RANK_1)),
            new Pieces\Bishop($this->white, new Square(Square::FILE_C, Square::RANK_1)),
            new Pieces\Queen($this->white, new Square(Square::FILE_D, Square::RANK_1)),
            new Pieces\King($this->white, new Square(Square::FILE_E, Square::RANK_1)),
            new Pieces\Bishop($this->white, new Square(Square::FILE_F, Square::RANK_1)),
            new Pieces\Knight($this->white, new Square(Square::FILE_G, Square::RANK_1)),
            new Pieces\Rook($this->white, new Square(Square::FILE_H, Square::RANK_1)),
        ];
        $chessboard = new Chessboard($pieces);

        $expected = <<< EOF
br bk bb bQ bK bb bk br
bp bp bp bp bp bp bp bp
                       
                       
                       
                       
wp wp wp wp wp wp wp wp
wr wk wb wQ wK wb wk wr
EOF;

        $actual = $this->chessboardPrinter->print($chessboard);

        $this->assertEquals($expected, $actual);
    }
}
