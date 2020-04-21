<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Pieces\King;

class KingRule extends BaseRule implements MoveValidatorInterface
{
    protected const PIECE_TYPE = King::class;

    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        if ($this->isDifferentPieceType($move, $chessboard)
            || $this->isDifferentPlayer($move, $chessboard)
            || $this->isBlockedByOwnPiece($move, $chessboard)
        ) {
            return false;
        }

        $diffFile = abs($move->getFrom()->getFile() - $move->getTo()->getFile());
        $diffRank = abs($move->getFrom()->getRank() - $move->getTo()->getRank());

        if (($diffFile === 0 && $diffRank === 0) || $diffFile > 1 || $diffRank > 1) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }
}
