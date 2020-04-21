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
     * @dataProvider provideScenario1
     */
    public function testScenario1(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->kingRule->isValidMove($move, $chessboard);

        $this->assertSame($expected, $actual, 'Move allowed');
    }

    public function provideScenario1(): array
    {
        $moveList = [];

        $white = new White();
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $fromBoard = <<< EOF
                       
                       
                       
         wK            
                       
                       
                       
                       
EOF;
        $toBoard = <<< EOF
                       
                       
      wK wK wK         
      wK    wK         
      wK wK wK         
                       
                       
                       
EOF;
        $moveHelper = new MoveHelper();
        list($allowedMoves, $forbiddenMoves) = $moveHelper->getMoves($fromSquare, $fromBoard, $toBoard);

        /** @var Move $move */
        foreach ($allowedMoves as $move) {
            $name = sprintf('move_allowed_%s_to_%s', $move->getFrom(), $move->getTo());
            $moveList[$name] = [
                'board'    => $fromBoard,
                'move'     => $move,
                'expected' => true,
            ];
        }

        /** @var Move $move */
        foreach ($forbiddenMoves as $move) {
            $name = sprintf('move_forbidden_%s_to_%s', $move->getFrom(), $move->getTo());
            $moveList[$name] = [
                'board'    => $fromBoard,
                'move'     => $move,
                'expected' => false,
            ];
        }

        return $moveList;
    }

    /* public function testMovementIfCheckmateIsForbidden() */
    /* { */
    /* } */
}
