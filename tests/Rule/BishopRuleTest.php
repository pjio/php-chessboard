<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\BishopRule;
use Pjio\Chessboard\Helper\ValidMovesParser;

class BishopRuleTest extends TestCase
{
    private BishopRule $bishopRule;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->bishopRule = new BishopRule();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @dataProvider provideMoves
     */
    public function testIsValidMove(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->bishopRule->isValidMove($move, $chessboard);

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
 5 |      bb        | 5
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
 8 |bb          bb  | 8
 7 |  bb      bb    | 7
 6 |    bb  bb      | 6
 5 |                | 5
 4 |    bb  bb      | 4
 3 |  bb      bb    | 3
 2 |bb          bb  | 2
 1 |              bb| 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    bp  bp      | 6
 5 |      bb        | 5
 4 |    bp  bp      | 4
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

        $testScenario = 'capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |  wp      wp    | 7
 6 |                | 6
 5 |      bb        | 5
 4 |                | 4
 3 |  wp      wp    | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |  bb      bb    | 7
 6 |    bb  bb      | 6
 5 |                | 5
 4 |    bb  bb      | 4
 3 |  bb      bb    | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'tunnel';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_5);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |  wpwpwpwpwp  wp| 8
 7 |wp  wpwpwp  wpwp| 7
 6 |wpwp  wp  wpwpwp| 6
 5 |wpwpwpbbwpwpwpwp| 5
 4 |wpwp  wp  wpwpwp| 4
 3 |wp  wpwpwp  wpwp| 3
 2 |  wpwpwpwpwp  wp| 2
 1 |wpwpwpwpwpwpwp  | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |bb          bb  | 8
 7 |  bb      bb    | 7
 6 |    bb  bb      | 6
 5 |                | 5
 4 |    bb  bb      | 4
 3 |  bb      bb    | 3
 2 |bb          bb  | 2
 1 |              bb| 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'protect_king';
        $fromSquare = new Square(Square::FILE_G, Square::RANK_8);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |            bb  | 8
 7 |                | 7
 6 |      bK        | 6
 5 |                | 5
 4 |      wr        | 4
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
 6 |      bK        | 6
 5 |      bb        | 5
 4 |      wr        | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
