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
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

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
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'protect_king';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_6);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
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
 7 |    bK  bK      | 7
 6 |    bK  bK      | 6
 5 |    bK  bK      | 5
 4 |      wr        | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'castling_black';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_8);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |br      bK    br| 8
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
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |    bKbK  bKbK  | 8
 7 |      bKbKbK    | 7
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

        $testScenario = 'castling_white';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_1);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |wr      wK    wr| 1
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
 2 |      wKwKwK    | 2
 1 |    wKwK  wKwK  | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'castling_queenside';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_1);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |  br            | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |wr      wK      | 1
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
 2 |      wKwKwK    | 2
 1 |    wKwK  wK    | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'castling_queenside';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_8);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |br      bK      | 8
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
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |    bKbK  bK    | 8
 7 |      bKbKbK    | 7
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

        $testScenario = 'castling_bocked1';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_8);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |brbb    bK      | 8
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
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |      bK  bK    | 8
 7 |      bKbKbK    | 7
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

        $testScenario = 'castling_bocked2';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_8);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |br  bb  bK      | 8
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
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |      bK  bK    | 8
 7 |      bKbKbK    | 7
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

        $testScenario = 'castling_bocked3';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_8);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |br    bbbK      | 8
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
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |          bK    | 8
 7 |      bKbKbK    | 7
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

        $testScenario = 'castling_blocked4';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_1);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |        wKwb  wr| 1
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
 2 |      wKwKwK    | 2
 1 |      wK        | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'castling_blocked5';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_1);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |        wK  wbwr| 1
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
 2 |      wKwKwK    | 2
 1 |      wK  wK    | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'castling_checked1';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_1);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |        br      | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |        wK    wr| 1
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
 2 |      wK  wK    | 2
 1 |      wK  wK    | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'castling_checked2';
        $fromSquare = new Square(Square::FILE_E, Square::RANK_1);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |          br    | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |        wK    wr| 1
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
 2 |      wKwK      | 2
 1 |      wK        | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
