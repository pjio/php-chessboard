<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\White;
use Pjio\Chessboard\Board\ChessboardPrinter;
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

        $actual = (new ChessboardPrinter())->print($chessboard);

        $this->assertEquals($expected, $actual);

    }
}
