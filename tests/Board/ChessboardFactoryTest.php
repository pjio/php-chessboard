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
br bk bb bQ bK bb bk br
bp bp bp bp bp bp bp bp
                       
                       
                       
                       
wp wp wp wp wp wp wp wp
wr wk wb wQ wK wb wk wr
EOF;

        $actual = (new ChessboardSerializer())->serialize($chessboard);

        $this->assertEquals($expected, $actual);

    }
}
