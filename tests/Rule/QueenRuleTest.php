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
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |      bQ        | 5
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
 8 |bQ    bQ    bQ  | 8
 7 |  bQ  bQ  bQ    | 7
 6 |    bQbQbQ      | 6
 5 |bQbQbQ  bQbQbQbQ| 5
 4 |    bQbQbQ      | 4
 3 |  bQ  bQ  bQ    | 3
 2 |bQ    bQ    bQ  | 2
 1 |      bQ      bQ| 1
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
 6 |    bpbpbp      | 6
 5 |    bpbQbp      | 5
 4 |    bpbpbp      | 4
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
 7 |  wp  wp  wp    | 7
 6 |                | 6
 5 |  wp  bQ  wp    | 5
 4 |                | 4
 3 |  wp  wp  wp    | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |  bQ  bQ  bQ    | 7
 6 |    bQbQbQ      | 6
 5 |  bQbQ  bQbQ    | 5
 4 |    bQbQbQ      | 4
 3 |  bQ  bQ  bQ    | 3
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
 8 |  bpbp  bpbp  bp| 8
 7 |bp  bp  bp  bpbp| 7
 6 |bpbp      bpbpbp| 6
 5 |      bQ        | 5
 4 |bpbp      bpbpbp| 4
 3 |bp  bp  bp  bpbp| 3
 2 |  bpbp  bpbp  bp| 2
 1 |bpbpbp  bpbpbp  | 1
   \----------------/
     A B C D E F G H
EOF;
        $validMoves = <<< EOF
    A B C D E F G H
   /----------------\
 8 |bQ    bQ    bQ  | 8
 7 |  bQ  bQ  bQ    | 7
 6 |    bQbQbQ      | 6
 5 |bQbQbQ  bQbQbQbQ| 5
 4 |    bQbQbQ      | 4
 3 |  bQ  bQ  bQ    | 3
 2 |bQ    bQ    bQ  | 2
 1 |      bQ      bQ| 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }
}
