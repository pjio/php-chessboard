<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Pieces\Bishop;

class BishopRule extends BaseRule implements MoveValidatorInterface
{
    protected const PIECE_TYPE = Bishop::class;

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

        // $from and $to must be diagonal to each other
        if ($fileDist !== $rankDist) {
            return false;
        }

        if (!$this->pathHelper->isFreePathDiagonal($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }
}
