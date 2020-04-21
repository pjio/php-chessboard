<?php
namespace Tests;

require_once __DIR__ . '/MoveHelper.php';

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\KnightRule;
use Tests\MoveHelper;

class KnightRuleTest extends TestCase
{
    private KnightRule $knightRule;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->knightRule = new KnightRule();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @dataProvider provideMoves
     */
    public function testIsValidMove(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->knightRule->isValidMove($move, $chessboard);

        $this->assertSame($expected, $actual);
    }

    public function provideMoves(): array
    {
        $moveList = [];
        $moveHelper = new MoveHelper();

        $testScenario = 'empty_board';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
                       
                       
                       
                       
         bk            
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
      bk    bk         
   bk          bk      
                       
   bk          bk      
      bk    bk         
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'jump_over';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
                       
                       
                       
      bp wp wp         
      bp bk wp         
      bp wp wp         
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
      bk    bk         
   bk          bk      
                       
   bk          bk      
      bk    bk         
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
                       
                       
      wQ    wQ         
   wQ          wQ      
         bk            
   wQ          wQ      
      wQ    wQ         
                       
EOF;
        $validMoves = <<< EOF
                       
                       
      bk    bk         
   bk          bk      
                       
   bk          bk      
      bk    bk         
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
                       
                       
      br    br         
   br          br      
         bk            
   br          br      
      br    br         
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $moveHelper->getMoves($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
