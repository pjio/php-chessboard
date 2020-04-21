<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\KingRule;
use Pjio\Chessboard\Helper\ValidMovesParser;
use Pjio\Chessboard\White;

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
        $validMovesParser = new ValidMovesParser();

        $testScenario = 'empty_board';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
                       
                       
         wK            
                       
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
      wK wK wK         
      wK    wK         
      wK wK wK         
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'surrounded_same_color';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
                       
      wp wp wp         
      wp wK wp         
      wp wp wp         
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
                       
                       
                       
                       
                       
                       
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'surrounded_diagonal';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
                       
      bk    bk         
         wK            
      bk    bk         
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
      wK    wK         
                       
      wK    wK         
                       
                       
                       
EOF;
        // This will require the other rules to ensure the king doesn't move into check
        /* $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMovesBoard)); */

        $testScenario = 'surrounded_horizontal';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
                       
                       
         bk            
      bk wK bk         
         bk            
                       
                       
                       
EOF;
        $validMoves = <<< EOF
                       
                       
         wK            
      wK    wK         
         wK            
                       
                       
                       
EOF;
        // This will require the other rules to ensure the king doesn't move into check
        /* $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMovesBoard)); */

        return $moveList;
    }
}
