<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\Knight;

class KnightRule extends AbstractRule
{
    protected const PIECE_TYPE = Knight::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
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

        return true;
    }
}
