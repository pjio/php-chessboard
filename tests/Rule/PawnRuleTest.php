<?php
namespace Tests;

require_once __DIR__ . '/MoveHelper.php';

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\PawnRule;
use Tests\MoveHelper;

class PawnRuleTest extends TestCase
{
    private PawnRule $pawnRule;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->pawnRule = new PawnRule();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @dataProvider provideMoves
     */
    public function testIsValidMove(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->pawnRule->isValidMove($move, $chessboard);

        $this->assertSame($expected, $actual);
    }

    public function provideMoves(): array
    {
        $moveList = [];
        $moveHelper = new MoveHelper();

        $testScenario = 'white_opening';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_2);
        $board = <<< EOF
                       
                       
                       
                       
                       
                       
         wp            
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
         wp            
         wp            
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'white_blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_2);
        $board = <<< EOF
                       
                       
                       
                       
                       
         bp wb         
         wp            
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'white_capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_3);
        $board = <<< EOF
                       
                       
                       
                       
      bQ wb bb         
         wp            
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
      wp    wp         
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'white_forward';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_3);
        $board = <<< EOF
                       
                       
                       
                       
                       
         wp            
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
         wp            
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_opening';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_7);
        $board = <<< EOF
                       
         bp            
                       
                       
                       
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
         bp            
         bp            
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_7);
        $board = <<< EOF
                       
         bp            
         wp bb         
                       
                       
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_6);
        $board = <<< EOF
                       
                       
         bp            
      wQ bb wb         
                       
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
      bp    bp         
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_forward';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_6);
        $board = <<< EOF
                       
                       
         bp            
                       
                       
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
         bp            
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
