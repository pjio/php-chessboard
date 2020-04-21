<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;

class BaseRule
{
    protected function isDifferentPieceType(Move $move, Chessboard $chessboard): bool
    {
        $piece = $chessboard->getPieceBySquare($move->getFrom());

        if (get_class($piece) !== static::PIECE_TYPE) {
            return true;
        }

        return false;
    }

    protected function isDifferentPlayer(Move $move, Chessboard $chessboard): bool
    {
        $piece = $chessboard->getPieceBySquare($move->getFrom());

        if ($piece === null || $piece->getPlayer() != $move->getPlayer()) {
            return true;
        }

        return false;
    }

    protected function isBlockedByOwnPiece(Move $move, Chessboard $chessboard): bool
    {
        $pieceAtTarget = $chessboard->getPieceBySquare($move->getTo());

        if ($pieceAtTarget !== null && $pieceAtTarget->getPlayer() == $move->getPlayer()) {
            return true;
        }

        return false;
    }

    /**
     * This function is rather expensive. Therefore it should be checked last.
     */
    protected function isOwnKingCheckedAfterMove(Move $move, Chessboard $chessboard): bool
    {
        // Todo: implement
        return false;
    }
}
