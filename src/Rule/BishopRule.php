<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\Bishop;

class BishopRule extends AbstractRule
{
    protected const PIECE_TYPE = Bishop::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
        $from = $move->getFrom();
        $to   = $move->getTo();

        $fileDist = abs($from->getFile() - $to->getFile());
        $rankDist = abs($from->getRank() - $to->getRank());

        // $from and $to must be diagonal to each other
        if ($fileDist !== $rankDist) {
            return false;
        }

        if (!$this->ruleHelper->isFreePathDiagonal($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        return true;
    }
}
