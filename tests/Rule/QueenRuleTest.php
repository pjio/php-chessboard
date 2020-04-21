<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\QueenRule;
use Pjio\Chessboard\Helper\ValidMovesParser;

class QueenRuleTest extends TestCase
{
    private QueenRule $queenRule;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->queenRule = new QueenRule();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @dataProvider provideMoves
     */
    public function testIsValidMove(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->queenRule->isValidMove($move, $chessboard);

        $this->assertSame($expected, $actual);
    }

    public function provideMoves(): array
    {
        $moveList = [];
        $validMovesParser = new ValidMovesParser();

        $testScenario = 'empty_board';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
                       
                       
         bQ            
                       
                       
                       
                       
EOF;
        $validMoves = <<< EOF
bQ       bQ       bQ   
   bQ    bQ    bQ      
      bQ bQ bQ         
bQ bQ bQ    bQ bQ bQ bQ
      bQ bQ bQ         
   bQ    bQ    bQ      
bQ       bQ       bQ   
         bQ          bQ
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
                       
      bp bp bp         
      bp bQ bp         
      bp bp bp         
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
   wp    wp    wp      
                       
   wp    bQ    wp      
                       
   wp    wp    wp      
                       
                       
EOF;
        $validMoves = <<< EOF
                       
   bQ    bQ    bQ      
      bQ bQ bQ         
   bQ bQ    bQ bQ      
      bQ bQ bQ         
   bQ    bQ    bQ      
                       
                       
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'tunnel';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
   bp bp    bp bp    bp
bp    bp    bp    bp bp
bp bp          bp bp bp
         bQ            
bp bp          bp bp bp
bp    bp    bp    bp bp
   bp bp    bp bp    bp
bp bp bp    bp bp bp   
EOF;
        $validMoves = <<< EOF
bQ       bQ       bQ   
   bQ    bQ    bQ      
      bQ bQ bQ         
bQ bQ bQ    bQ bQ bQ bQ
      bQ bQ bQ         
   bQ    bQ    bQ      
bQ       bQ       bQ   
         bQ          bQ
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}