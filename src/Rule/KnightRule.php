<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Pieces\Knight;

class KnightRule extends BaseRule implements MoveValidatorInterface
{
    protected const PIECE_TYPE = Knight::class;

    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        if ($this->isDifferentPieceType($move, $chessboard)
            || $this->isDifferentPlayer($move, $chessboard)
            || $this->isBlockedByOwnPiece($move, $chessboard)
        ) {
            return false;
        }

        $from = $move->getFrom();
        $to   = $move->getTo();

        $steps = [
            abs($from->getFile() - $to->getFile()),
            abs($from->getRank() - $to->getRank()),
        ];
        sort($steps);

        if ($steps != [1, 2]) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }
}
