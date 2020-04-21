<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\RookRule;
use Pjio\Chessboard\Helper\ValidMovesParser;

class RookRuleTest extends TestCase
{
    private RookRule $rookRule;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->rookRule = new RookRule();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @dataProvider provideMoves
     */
    public function testIsValidMove(string $board, Move $move, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actual = $this->rookRule->isValidMove($move, $chessboard);

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
 5 |      wr        | 5
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
 8 |      wr        | 8
 7 |      wr        | 7
 6 |      wr        | 6
 5 |wrwrwr  wrwrwrwr| 5
 4 |      wr        | 4
 3 |      wr        | 3
 2 |      wr        | 2
 1 |      wr        | 1
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
 6 |      wp        | 6
 5 |    wpwrwp      | 5
 4 |      wp        | 4
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
 7 |      bp        | 7
 6 |                | 6
 5 |  bp  wr  bp    | 5
 4 |                | 4
 3 |      bp        | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |      wr        | 7
 6 |      wr        | 6
 5 |  wrwr  wrwr    | 5
 4 |      wr        | 4
 3 |      wr        | 3
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
 8 |bpbpbp  bpbpbpbp| 8
 7 |bpbpbp  bpbpbpbp| 7
 6 |bpbpbp  bpbpbpbp| 6
 5 |      wr        | 5
 4 |bpbpbp  bpbpbpbp| 4
 3 |bpbpbp  bpbpbpbp| 3
 2 |bpbpbp  bpbpbpbp| 2
 1 |bpbpbp  bpbpbpbp| 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |      wr        | 8
 7 |      wr        | 7
 6 |      wr        | 6
 5 |wrwrwr  wrwrwrwr| 5
 4 |      wr        | 4
 3 |      wr        | 3
 2 |      wr        | 2
 1 |      wr        | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
