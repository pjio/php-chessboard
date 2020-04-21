<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Pieces\King;

class KingRule extends AbstractRule
{
    protected const PIECE_TYPE = King::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
        $diffFile = abs($move->getFrom()->getFile() - $move->getTo()->getFile());
        $diffRank = abs($move->getFrom()->getRank() - $move->getTo()->getRank());

        if (($diffFile === 0 && $diffRank === 0) || $diffFile > 1 || $diffRank > 1) {
            return false;
        }

        return true;
    }
}
