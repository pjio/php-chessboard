<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Pieces\Queen;

class QueenRule extends BaseRule implements MoveValidatorInterface
{
    protected const PIECE_TYPE = Queen::class;

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

        $isLinear   = $fileDist === 0 || $rankDist === 0;
        $isDiagonal = $fileDist === $rankDist;

        if (!$isLinear && !$isDiagonal) {
            return false;
        }

        if ($isLinear && !$this->pathHelper->isFreePathLinear($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        if ($isDiagonal && !$this->pathHelper->isFreePathDiagonal($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }
}
