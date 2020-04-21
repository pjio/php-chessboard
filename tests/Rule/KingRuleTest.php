<?php
namespace Tests;

require_once __DIR__ . '/MoveHelper.php';

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\KingRule;
use Pjio\Chessboard\White;
use Tests\MoveHelper;

class KingRuleTest extends TestCase
{
    private KingRule $kingRule;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->kingRule = new KingRule();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @dataProvider provideMoves
     */
    public function testIsValidMove(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->kingRule->isValidMove($move, $chessboard);

        $this->assertSame($expected, $actual);
    }

    public function provideMoves(): array
    {
        $moveList = [];
        $moveHelper = new MoveHelper();

        $testScenario = 'empty_board';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $fromBoard = <<< EOF
                       
                       
                       
         wK            
                       
                       
                       
                       
EOF;
        $toBoard = <<< EOF
                       
                       
      wK wK wK         
      wK    wK         
      wK wK wK         
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $fromBoard, $toBoard));

        return $moveList;
    }
}
