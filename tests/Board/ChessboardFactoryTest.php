<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\ChessboardFactory;

class ChessboardFactoryTest extends TestCase
{
    private ChessboardFactory $chessboardFactory;

    public function setUp(): void
    {
        $this->chessboardFactory = new ChessboardFactory();
    }

    public function testCreateNewChessboard()
    {
        $chessboard = $this->chessboardFactory->createNewChessboard(
            new White(),
            new Black()
        );

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

        $actual = (new ChessboardSerializer())->serialize($chessboard);

        $this->assertEquals($expected, $actual);
    }
}
