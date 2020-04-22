<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Helper\CheckedHelper;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\White;

class CheckedHelperTest extends TestCase
{
    private CheckedHelper $checkedHelper;
    private ChessboardSerializer $chessboardSerializer;

    public function setUp(): void
    {
        $this->chessboardSerializer = new ChessboardSerializer();
        $this->checkedHelper = new CheckedHelper();
    }

    /**
     * @dataProvider provideScenarios
     */
    public function testIsKingChecked(King $king, string $board, bool $expected)
    {
        /** @var Chessboard $chessboard */
        $chessboard = $this->chessboardSerializer->unserialize($board);

        $this->assertEquals($expected, $this->checkedHelper->isKingChecked($king, $chessboard));
    }

    public function provideScenarios()
    {
        $black = new Black();
        $white = new White();

        return [
            'check_pawn' => [
                'king' => new King($white, new Square(Square::FILE_F, Square::RANK_3)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |                | 8\n" .
                " 7 |                | 7\n" .
                " 6 |                | 6\n" .
                " 5 |                | 5\n" .
                " 4 |        bp      | 4\n" .
                " 3 |          wK    | 3\n" .
                " 2 |                | 2\n" .
                " 1 |                | 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => true
            ],
            'check_bishop' => [
                'king' => new King($white, new Square(Square::FILE_F, Square::RANK_3)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |bbwpwpwpwpwpwpwp| 8\n" .
                " 7 |wp  wpwpwpwpwpwp| 7\n" .
                " 6 |wpwp  wpwpwpwpwp| 6\n" .
                " 5 |wpwpwp  wpwpwpwp| 5\n" .
                " 4 |wpwpwpwp  wpwpwp| 4\n" .
                " 3 |wpwpwpwpwpwKwpwp| 3\n" .
                " 2 |wpwpwpwpwpwpwpwp| 2\n" .
                " 1 |wpwpwpwpwpwpwpwp| 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => true
            ],
            'check_rook' => [
                'king' => new King($white, new Square(Square::FILE_F, Square::RANK_3)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |wpwpwpwpwpbrwpwp| 8\n" .
                " 7 |wpwpwpwpwp  wpwp| 7\n" .
                " 6 |wpwpwpwpwp  wpwp| 6\n" .
                " 5 |wpwpwpwpwp  wpwp| 5\n" .
                " 4 |wpwpwpwpwp  wpwp| 4\n" .
                " 3 |wpwpwpwpwpwKwpwp| 3\n" .
                " 2 |wpwpwpwpwpwpwpwp| 2\n" .
                " 1 |wpwpwpwpwpwpwpwp| 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => true
            ],
            'check_knight' => [
                'king' => new King($white, new Square(Square::FILE_F, Square::RANK_3)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |                | 8\n" .
                " 7 |                | 7\n" .
                " 6 |                | 6\n" .
                " 5 |                | 5\n" .
                " 4 |      bkwpwp    | 4\n" .
                " 3 |      wpwpwK    | 3\n" .
                " 2 |                | 2\n" .
                " 1 |                | 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => true
            ],
            'check_surrounded' => [
                'king' => new King($white, new Square(Square::FILE_E, Square::RANK_5)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |                | 8\n" .
                " 7 |    bb  br  bQ  | 7\n" .
                " 6 |                | 6\n" .
                " 5 |    br  wK  br  | 5\n" .
                " 4 |                | 4\n" .
                " 3 |    bQ  br  bb  | 3\n" .
                " 2 |                | 2\n" .
                " 1 |                | 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => true
            ],
            'nocheck_blocked' => [
                'king' => new King($white, new Square(Square::FILE_E, Square::RANK_5)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |                | 8\n" .
                " 7 |    bb  br  bQ  | 7\n" .
                " 6 |      wpwpwp    | 6\n" .
                " 5 |    brwpwKwpbr  | 5\n" .
                " 4 |      wpwpwp    | 4\n" .
                " 3 |    bQ  br  bb  | 3\n" .
                " 2 |                | 2\n" .
                " 1 |                | 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => false
            ],
            'nocheck_placement' => [
                'king' => new King($white, new Square(Square::FILE_E, Square::RANK_5)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |                | 8\n" .
                " 7 |    brbQbbbQbr  | 7\n" .
                " 6 |    bQ      bQ  | 6\n" .
                " 5 |    bb  wK  bb  | 5\n" .
                " 4 |    bQ      bQ  | 4\n" .
                " 3 |    brbQbbbQbr  | 3\n" .
                " 2 |                | 2\n" .
                " 1 |                | 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => false
            ],
            'nocheck_pawn' => [
                'king' => new King($white, new Square(Square::FILE_E, Square::RANK_5)),
                'board' =>
                "    A B C D E F G H\n" .
                "   /----------------\\\n" .
                " 8 |                | 8\n" .
                " 7 |                | 7\n" .
                " 6 |        bp      | 6\n" .
                " 5 |        wK      | 5\n" .
                " 4 |                | 4\n" .
                " 3 |                | 3\n" .
                " 2 |                | 2\n" .
                " 1 |                | 1\n" .
                "   \----------------/\n" .
                "     A B C D E F G H",
                'expected' => false
            ],
        ];
    }
}
