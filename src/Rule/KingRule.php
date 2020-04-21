<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;

class KingRule implements MoveValidatorInterface
{
    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        $diffFile = abs($move->getFrom()->getFile() - $move->getTo()->getFile());
        $diffRank = abs($move->getFrom()->getRank() - $move->getTo()->getRank());

        if (($diffFile === 0 && $diffRank === 0) || $diffFile > 1 || $diffRank > 1) {
            return false;
        }

        $pieceAtTarget = $chessboard->getPieceBySquare($move->getTo());

        if ($pieceAtTarget !== null && $pieceAtTarget->getPlayer() == $move->getPlayer()) {
            return false;
        }

        return true;
    }
}
