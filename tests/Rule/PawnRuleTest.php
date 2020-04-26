<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\InvalidPromotionException;
use Pjio\Chessboard\Helper\ValidMovesParser;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\PawnRule;
use Pjio\Chessboard\White;

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
        $validMovesParser = new ValidMovesParser();

        $testScenario = 'white_opening';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_2);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |                | 3
 2 |      wp        | 2
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
 4 |      wp        | 4
 3 |      wp        | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'white_blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_2);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |      bpwb      | 3
 2 |      wp        | 2
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

        $testScenario = 'white_capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_3);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |    bQwbbb      | 4
 3 |      wp        | 3
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
 4 |    wp  wp      | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'white_forward';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_3);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |                | 5
 4 |                | 4
 3 |      wp        | 3
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
 4 |      wp        | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_opening';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_7);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |      bp        | 7
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
 8 |                | 8
 7 |                | 7
 6 |      bp        | 6
 5 |      bp        | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_blocked';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_7);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |      bp        | 7
 6 |      wpbb      | 6
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

        $testScenario = 'black_capture';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_6);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |      bp        | 6
 5 |    wQbbwb      | 5
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
 6 |                | 6
 5 |    bp  bp      | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'black_forward';
        $fromSquare = new Square(Square::FILE_D, Square::RANK_6);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |      bp        | 6
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
 8 |                | 8
 7 |                | 7
 6 |                | 6
 5 |      bp        | 5
 4 |                | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        $testScenario = 'protect_king';
        $fromSquare = new Square(Square::FILE_C, Square::RANK_6);
        $board = <<< EOF
    A B C D E F G H
   /----------------\
 8 |                | 8
 7 |                | 7
 6 |    bpbK        | 6
 5 |  wr  wb        | 5
 4 |  wb            | 4
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
 5 |  wrbpwb        | 5
 4 |  wb            | 4
 3 |                | 3
 2 |                | 2
 1 |                | 1
   \----------------/
     A B C D E F G H
EOF;
        $moveList = array_merge($moveList, $validMovesParser->parse($testScenario, $fromSquare, $board, $validMoves));

        return $moveList;
    }

    /**
     * @dataProvider providePromotionMoves
     */
    public function testPromotion(
        string $board,
        Move $move,
        bool $expectedValid,
        ?string $expectedPiece
    ) {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $actualValid = $this->pawnRule->isValidMove($move, $chessboard);

        $this->assertSame($expectedValid, $actualValid);

        $chessboard->move($move);
        $promotedPiece = $chessboard->getPieceBySquare($move->getTo());
        $this->assertSame($expectedPiece, $promotedPiece === null ? null : $promotedPiece->getName(), 'Promoted piece at target position');
    }

    public function providePromotionMoves(): array
    {
        $white = new White();
        $black = new Black();

        return [
            'white_promotion_default' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |      wp        | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($white, new Square(Square::FILE_D, Square::RANK_7), new Square(Square::FILE_D, Square::RANK_8)),
                'expectedValid' => true,
                'expectedPiece' => 'Queen',
            ],
            'white_promotion_queen' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |      wp        | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($white, new Square(Square::FILE_D, Square::RANK_7), new Square(Square::FILE_D, Square::RANK_8), false, 'queen'),
                'expectedValid' => true,
                'expectedPiece' => 'Queen',
            ],
            'white_promotion_rook' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |      wp        | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($white, new Square(Square::FILE_D, Square::RANK_7), new Square(Square::FILE_D, Square::RANK_8), false, 'ROOK'),
                'expectedValid' => true,
                'expectedPiece' => 'Rook',
            ],
            'white_promotion_bishop' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |      wp        | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($white, new Square(Square::FILE_D, Square::RANK_7), new Square(Square::FILE_D, Square::RANK_8), false, 'bIshOp'),
                'expectedValid' => true,
                'expectedPiece' => 'Bishop',
            ],
            'white_promotion_knight' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |      wp        | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($white, new Square(Square::FILE_D, Square::RANK_7), new Square(Square::FILE_D, Square::RANK_8), false, 'knighT'),
                'expectedValid' => true,
                'expectedPiece' => 'Knight',
            ],
            'white_promotion_on_capture' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |        br      | 8\n" .
                    " 7 |      wp        | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($white, new Square(Square::FILE_D, Square::RANK_7), new Square(Square::FILE_E, Square::RANK_8), false, 'KNIGHT'),
                'expectedValid' => true,
                'expectedPiece' => 'Knight',
            ],
            'black_promotion_default' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |        bp      | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($black, new Square(Square::FILE_E, Square::RANK_2), new Square(Square::FILE_E, Square::RANK_1)),
                'expectedValid' => true,
                'expectedPiece' => 'Queen',
            ],
            'black_promotion_queen' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |        bp      | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($black, new Square(Square::FILE_E, Square::RANK_2), new Square(Square::FILE_E, Square::RANK_1), false, 'QUEEN'),
                'expectedValid' => true,
                'expectedPiece' => 'Queen',
            ],
            'black_promotion_rook' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |        bp      | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($black, new Square(Square::FILE_E, Square::RANK_2), new Square(Square::FILE_E, Square::RANK_1), false, 'rOOk'),
                'expectedValid' => true,
                'expectedPiece' => 'Rook',
            ],
            'black_promotion_bishop' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |        bp      | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($black, new Square(Square::FILE_E, Square::RANK_2), new Square(Square::FILE_E, Square::RANK_1), false, 'bIshop'),
                'expectedValid' => true,
                'expectedPiece' => 'Bishop',
            ],
            'black_promotion_knight' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |        bp      | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($black, new Square(Square::FILE_E, Square::RANK_2), new Square(Square::FILE_E, Square::RANK_1), false, 'knight'),
                'expectedValid' => true,
                'expectedPiece' => 'Knight',
            ],
            'black_promotion_on_capture' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |        bp      | 2\n" .
                    " 1 |          wr    | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move'          => new Move($black, new Square(Square::FILE_E, Square::RANK_2), new Square(Square::FILE_F, Square::RANK_1), false, 'knight'),
                'expectedValid' => true,
                'expectedPiece' => 'Knight',
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidPromotions
     */
    public function testInvalidPromotions(string $board, Move $move)
    {
        $this->expectException(InvalidPromotionException::class);

        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $this->pawnRule->isValidMove($move, $chessboard);
    }

    public function provideInvalidPromotions(): array
    {
        $white = new White();
        $black = new Black();

        return [
            'white_wrong_rank' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |      wp        | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |                | 4\n" .
                    " 3 |                | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move' => new Move($white, new Square(Square::FILE_D, Square::RANK_6), new Square(Square::FILE_D, Square::RANK_7), false, 'queen'),
            ],
            'black_wrong_rank' => [
                'board' =>
                    "    A B C D E F G H\n" .
                    "   /----------------\\\n" .
                    " 8 |                | 8\n" .
                    " 7 |                | 7\n" .
                    " 6 |                | 6\n" .
                    " 5 |                | 5\n" .
                    " 4 |          bp    | 4\n" .
                    " 3 |        wb      | 3\n" .
                    " 2 |                | 2\n" .
                    " 1 |                | 1\n" .
                    "   \----------------/\n" .
                    "     A B C D E F G H",
                'move' => new Move($black, new Square(Square::FILE_F, Square::RANK_4), new Square(Square::FILE_E, Square::RANK_3), false, 'rook'),
            ],
        ];
    }
}
