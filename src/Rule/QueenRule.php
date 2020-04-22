<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\Queen;

class QueenRule extends AbstractRule
{
    protected const PIECE_TYPE = Queen::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
        $from = $move->getFrom();
        $to   = $move->getTo();

        $fileDist = abs($from->getFile() - $to->getFile());
        $rankDist = abs($from->getRank() - $to->getRank());

        $isLinear   = $fileDist === 0 || $rankDist === 0;
        $isDiagonal = $fileDist === $rankDist;

        if (!$isLinear && !$isDiagonal) {
            return false;
        }

        if ($isLinear && !$this->ruleHelper->isFreePathLinear($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        if ($isDiagonal && !$this->ruleHelper->isFreePathDiagonal($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        return true;
    }
}
