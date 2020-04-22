<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\KingRule;
use Pjio\Chessboard\Helper\ValidMovesParser;

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
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |      wK        | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    wKwKwK      | 6
 5 |    wK  wK      | 5
 4 |    wKwKwK      | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'surrounded_same_color';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    wpwpwp      | 6
 5 |    wpwKwp      | 5
 4 |    wpwpwp      | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
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
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'surrounded_diagonal';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    bk  bk      | 6
 5 |      wK        | 5
 4 |    bk  bk      | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    wK  wK      | 6
 5 |                | 5
 4 |    wK  wK      | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        // This will require the other rules to ensure the king doesn't move into check
        /* $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMovesBoard)); */

        $testScenario = 'surrounded_horizontal';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |      bk        | 6
 5 |    bkwKbk      | 5
 4 |      bk        | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |      wK        | 6
 5 |    wK  wK      | 5
 4 |      wK        | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        // This will require the other rules to ensure the king doesn't move into check
        /* $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMovesBoard)); */

        return $moveList;
    }
}
