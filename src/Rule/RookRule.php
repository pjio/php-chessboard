<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Pieces\Rook;

class RookRule extends BaseRule implements MoveValidatorInterface
{
    protected const PIECE_TYPE = Rook::class;

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

        $fileDist = abs($from->getFile() - $to->getFile());
        $rankDist = abs($from->getRank() - $to->getRank());

        // $from and $to must be linear to each other
        if ($fileDist !== 0 && $rankDist !== 0) {
            return false;
        }

        if (!$this->pathHelper->isFreePathLinear($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }
}
