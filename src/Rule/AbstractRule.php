<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Helper\PathHelper;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;

abstract class AbstractRule implements MoveValidatorInterface
{
    protected PathHelper $pathHelper;

    public function __construct()
    {
        $this->pathHelper = new PathHelper();
    }

    abstract protected function pieceRule(Move $move, Chessboard $chessboard): bool;

    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        if ($this->isDifferentPieceType($move, $chessboard)
            || $this->isDifferentPlayer($move, $chessboard)
            || $this->isBlockedByOwnPiece($move, $chessboard)
        ) {
            return false;
        }

        if (!$this->pieceRule($move, $chessboard)) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }

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

    protected function isOwnKingCheckedAfterMove(Move $move, Chessboard $chessboard): bool
    {
        // Todo: implement
        return false;
    }
}
