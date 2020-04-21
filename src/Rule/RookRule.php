<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Pieces\Rook;

class RookRule extends AbstractRule
{
    protected const PIECE_TYPE = Rook::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
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

        return true;
    }
}
