<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Exception\UnserializeException;
use Pjio\Chessboard\Piece\AbstractPiece;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Piece;
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
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;

        $actual = $this->chessboardSerializer->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testSerializeCorners()
    {
        $pieces = [
            new Piece\Pawn($this->white, new Square(Square::FILE_A, Square::RANK_8)),
            new Piece\Rook($this->white, new Square(Square::FILE_H, Square::RANK_8)),
            new Piece\Bishop($this->white, new Square(Square::FILE_A, Square::RANK_1)),
            new Piece\Knight($this->white, new Square(Square::FILE_H, Square::RANK_1)),
        ];
        $chessboard = new Chessboard($pieces);

        $expected = <<< EOF
    A B C D E F G H
   /----------------\
 8 |wp            wr| 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |wb            wk| 1
   \----------------/
     A B C D E F G H
EOF;

        $actual = $this->chessboardSerializer->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testSerializeFullBoard()
    {
        $pieces = [
            new Piece\Pawn($this->black, new Square(Square::FILE_A, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_B, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_C, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_D, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_E, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_F, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_G, Square::RANK_7)),
            new Piece\Pawn($this->black, new Square(Square::FILE_H, Square::RANK_7)),

            new Piece\Rook($this->black, new Square(Square::FILE_A, Square::RANK_8)),
            new Piece\Knight($this->black, new Square(Square::FILE_B, Square::RANK_8)),
            new Piece\Bishop($this->black, new Square(Square::FILE_C, Square::RANK_8)),
            new Piece\Queen($this->black, new Square(Square::FILE_D, Square::RANK_8)),
            new Piece\King($this->black, new Square(Square::FILE_E, Square::RANK_8)),
            new Piece\Bishop($this->black, new Square(Square::FILE_F, Square::RANK_8)),
            new Piece\Knight($this->black, new Square(Square::FILE_G, Square::RANK_8)),
            new Piece\Rook($this->black, new Square(Square::FILE_H, Square::RANK_8)),

            new Piece\Pawn($this->white, new Square(Square::FILE_A, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_B, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_C, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_D, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_E, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_F, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_G, Square::RANK_2)),
            new Piece\Pawn($this->white, new Square(Square::FILE_H, Square::RANK_2)),

            new Piece\Rook($this->white, new Square(Square::FILE_A, Square::RANK_1)),
            new Piece\Knight($this->white, new Square(Square::FILE_B, Square::RANK_1)),
            new Piece\Bishop($this->white, new Square(Square::FILE_C, Square::RANK_1)),
            new Piece\Queen($this->white, new Square(Square::FILE_D, Square::RANK_1)),
            new Piece\King($this->white, new Square(Square::FILE_E, Square::RANK_1)),
            new Piece\Bishop($this->white, new Square(Square::FILE_F, Square::RANK_1)),
            new Piece\Knight($this->white, new Square(Square::FILE_G, Square::RANK_1)),
            new Piece\Rook($this->white, new Square(Square::FILE_H, Square::RANK_1)),
        ];
        $chessboard = new Chessboard($pieces);

        $expected = <<< EOF
    A B C D E F G H
   /----------------\
 8 |brbkbbbQbKbbbkbr| 8
 7 |bpbpbpbpbpbpbpbp| 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |wpwpwpwpwpwpwpwp| 2
 1 |wrwkwbwQwKwbwkwr| 1
   \----------------/
     A B C D E F G H
EOF;

        $actual = $this->chessboardSerializer->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }

    public function testUnserializeEmptyBoard()
    {
        $str = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($str);

        $this->assertCount(0, $chessboard->getPiecesIterator());
    }

    public function testUnserializeCorners()
    {
        $str = <<< EOF
    A B C D E F G H
   /----------------\
 8 |wr            bK| 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |bb            wp| 1
   \----------------/
     A B C D E F G H
EOF;

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($str);

        $this->assertCount(4, $chessboard->getPiecesIterator());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_8));
        $this->assertInstanceOf(Piece\Rook::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_8));
        $this->assertInstanceOf(Piece\King::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_1));
        $this->assertInstanceOf(Piece\Bishop::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_1));
        $this->assertInstanceOf(Piece\Pawn::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());
    }

    public function testUnSerializeFullBoard()
    {
        $str = <<< EOF
    A B C D E F G H
   /----------------\
 8 |brbkbbbQbKbbbkbr| 8
 7 |bpbpbpbpbpbpbpbp| 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |wpwpwpwpwpwpwpwp| 2
 1 |wrwkwbwQwKwbwkwr| 1
   \----------------/
     A B C D E F G H
EOF;

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($str);

        $this->assertCount(32, $chessboard->getPiecesIterator());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_8));
        $this->assertInstanceOf(Piece\Rook::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_B, Square::RANK_8));
        $this->assertInstanceOf(Piece\Knight::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_C, Square::RANK_8));
        $this->assertInstanceOf(Piece\Bishop::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_D, Square::RANK_8));
        $this->assertInstanceOf(Piece\Queen::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_E, Square::RANK_8));
        $this->assertInstanceOf(Piece\King::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_F, Square::RANK_8));
        $this->assertInstanceOf(Piece\Bishop::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_G, Square::RANK_8));
        $this->assertInstanceOf(Piece\Knight::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_8));
        $this->assertInstanceOf(Piece\Rook::class, $piece);
        $this->assertInstanceOf(Black::class, $piece->getPlayer());

        for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
            /** @var AbstractPiece $piece */
            $piece = $chessboard->getPieceBySquare(new Square($file, Square::RANK_7));
            $this->assertInstanceOf(Piece\Pawn::class, $piece);
            $this->assertInstanceOf(Black::class, $piece->getPlayer());
        }

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_A, Square::RANK_1));
        $this->assertInstanceOf(Piece\Rook::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_B, Square::RANK_1));
        $this->assertInstanceOf(Piece\Knight::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_C, Square::RANK_1));
        $this->assertInstanceOf(Piece\Bishop::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_D, Square::RANK_1));
        $this->assertInstanceOf(Piece\Queen::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_E, Square::RANK_1));
        $this->assertInstanceOf(Piece\King::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_F, Square::RANK_1));
        $this->assertInstanceOf(Piece\Bishop::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_G, Square::RANK_1));
        $this->assertInstanceOf(Piece\Knight::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        /** @var AbstractPiece $piece */
        $piece = $chessboard->getPieceBySquare(new Square(Square::FILE_H, Square::RANK_1));
        $this->assertInstanceOf(Piece\Rook::class, $piece);
        $this->assertInstanceOf(White::class, $piece->getPlayer());

        for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
            /** @var AbstractPiece $piece */
            $piece = $chessboard->getPieceBySquare(new Square($file, Square::RANK_2));
            $this->assertInstanceOf(Piece\Pawn::class, $piece);
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
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
   \----------------/
     A B C D E F G H
EOF;

        $this->chessboardSerializer->unserialize($str);
    }

    public function testTooShortRows()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |               | 1
   \----------------/
     A B C D E F G H
EOF;

        $this->chessboardSerializer->unserialize($str);
    }

    public function testMalformedplayer()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
    A B C D E F G H
   /----------------\
 8 |Xr              | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;

        $this->chessboardSerializer->unserialize($str);
    }

    public function testMalformedPiece()
    {
        $this->expectException(UnserializeException::class);

        $str = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |              bX| 1
   \----------------/
     A B C D E F G H
EOF;

        $this->chessboardSerializer->unserialize($str);
    }
}
