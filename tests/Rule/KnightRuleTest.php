<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\KnightRule;
use Pjio\Chessboard\Helper\ValidMovesParser;

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
        $validMovesParser = new ValidMovesParser();

        $testScenario = 'empty_board';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
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
 6 |    bk  bk      | 6
 5 |  bk      bk    | 5
 4 |                | 4
 3 |  bk      bk    | 3
 2 |    bk  bk      | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'jump_over';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |    bpwpwp      | 5
 4 |    bpbkwp      | 4
 3 |    bpwpwp      | 3
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
 6 |    bk  bk      | 6
 5 |  bk      bk    | 5
 4 |                | 4
 3 |  bk      bk    | 3
 2 |    bk  bk      | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    wQ  wQ      | 6
 5 |  wQ      wQ    | 5
 4 |      bk        | 4
 3 |  wQ      wQ    | 3
 2 |    wQ  wQ      | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    bk  bk      | 6
 5 |  bk      bk    | 5
 4 |                | 4
 3 |  bk      bk    | 3
 2 |    bk  bk      | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_4);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    br  br      | 6
 5 |  br      br    | 5
 4 |      bk        | 4
 3 |  br      br    | 3
 2 |    br  br      | 2
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

        $testScenario = 'protect_king';
        $fromSquare = new Square(Square::FILE_F, Square::RANK_6);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |      bK  bk    | 6
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
 5 |      bk        | 5
 4 |      wr        | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        // This tests the special case in AbstractRule::isOwnKingCheckedAfterMove() for all pieces.
        $testScenario = 'capture_king_is_allowed_if_last_ply';
        $fromSquare = new Square(Square::FILE_F, Square::RANK_3);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |          bK    | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |          bk    | 3
 2 |                | 2
 1 |        wKwr    | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |          bK    | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |        bkwr    | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
