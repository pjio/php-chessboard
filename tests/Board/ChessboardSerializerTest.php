<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Exception\UnserializeException;
use Pjio\Chessboard\Pieces\AbstractPiece;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Pieces;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;

class ChessboardSerializerTest extends TestCase
{
    private ChessboardSerializer $chessboardSerializer;
    private White $white;
    private Black $black;

    public function setUp(): void
    {
        $this->chessboardSerializer = new ChessboardSerializer();
        $this->white = new White();
        $this->black = new Black();
    }

    public function testSerializeEmptyBoard()
    {
        $pieces = [];
        $chessboard = new Chessboard($pieces);

        $expected = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;

        $actual = $this->chessboardSerializer->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testSerializeCorners()
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

        $actual = $this->chessboardSerializer->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testSerializeFullBoard()
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

        $actual = $this->chessboardSerializer->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeEmptyBoard()
    {
        $str = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($str);

        $this->assertCount(0, $chessboard->getPiecesIterator());
    }

    public function testUnserializeCorners()
    {
        $str = <<< EOF
wr                   bK
                       
                       
                       
                       
                       
                       
bb                   wp
EOF;

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($str);

        $this->assertCount(4, $chessboard->getPiecesIterator());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Rook::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_8));
        $this->assertInstanceOf(Pieces\King::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Bishop::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Pawn::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());
    }

    public function testUnSerializeFullBoard()
    {
        $str = <<< EOF
br bk bb bQ bK bb bk br
bp bp bp bp bp bp bp bp
                       
                       
                       
                       
wp wp wp wp wp wp wp wp
wr wk wb wQ wK wb wk wr
EOF;

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($str);

        $this->assertCount(32, $chessboard->getPiecesIterator());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Rook::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_B, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Knight::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_C, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Bishop::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_D, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Queen::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_E, Square::RANK_8));
        $this->assertInstanceOf(Pieces\King::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_F, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Bishop::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_G, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Knight::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_8));
        $this->assertInstanceOf(Pieces\Rook::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
            /** @var AbstractPiece $piece */
            $piece = $chessboard->getPieceBySquare(new Square($file, Square::RANK_7));
            $this->assertInstanceOf(Pieces\Pawn::class, $piece);
            $this->assertInstanceOf(Black::class, $piece->getPlayer());
        }

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Rook::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_B, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Knight::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_C, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Bishop::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_D, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Queen::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_E, Square::RANK_1));
        $this->assertInstanceOf(Pieces\King::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_F, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Bishop::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_G, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Knight::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_1));
        $this->assertInstanceOf(Pieces\Rook::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
            /** @var AbstractPiece $piece */
            $piece = $chessboard->getPieceBySquare(new Square($file, Square::RANK_2));
            $this->assertInstanceOf(Pieces\Pawn::class, $piece);
            $this->assertInstanceOf(White::class, $piece->getPlayer());
        }

        for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
            for ($rank = Square::RANK_3; $rank <= Square::RANK_6; $rank++) {
                /** @var AbstractPiece $piece */
                $piece = $chessboard->getPieceBySquare(new Square($file, $rank));
                $this->assertNull($piece);
            }
        }
    }

    public function testTooFewRows()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
                       
                       
                       
                       
                       
                       
                       
EOF;

        $this->chessboardSerializer->unserialize($str);
    }

    public function testTooShortRows()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
                       
                       
                       
                       
                       
                       
                      
                       
EOF;

        $this->chessboardSerializer->unserialize($str);
    }

    public function testMalformedplayer()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
Xr                     
                       
                       
                       
                       
                       
                       
                       
EOF;

        $this->chessboardSerializer->unserialize($str);
    }

    public function testMalformedPiece()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                     bX
EOF;

        $this->chessboardSerializer->unserialize($str);
    }
}