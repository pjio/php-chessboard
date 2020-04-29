<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Helper\CheckedHelper;
use Pjio\Chessboard\Helper\RuleHelper;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Rule\KingRule;
use Pjio\Chessboard\Rule\PawnRule;
use Pjio\Chessboard\White;

abstract class AbstractRule
{
    protected RuleHelper $ruleHelper;
    protected CheckedHelper $checkedHelper;
    protected White $white;
    protected Black $black;

    public function __construct()
    {
        $this->ruleHelper = new RuleHelper();
        $this->checkedHelper = new CheckedHelper();
        $this->white = new White();
        $this->black = new Black();
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

        if (!empty($move->getPromotion()) && get_class($this) !== PawnRule::class) {
            throw new InvalidMoveException('Only Pawns can be promoted!');
        }

        if ($move->isCastling() && get_class($this) !== KingRule::class) {
            throw new InvalidMoveException('Only Kings can initiate a castling!');
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

        if ($piece === null || !$move->getPlayer()->isPlayer($piece->getPlayer())) {
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
        $copy         = clone $chessboard;
        $opponent     = $move->getPlayer() == $this->white ? $this->black : $this->white;
        $kingOpponent = $copy->getKing($opponent);

        $copy->move($move);

        if ($kingOpponent !== null) {
            // If the opposing king was captured, the game is won
            if ($copy->getKing($opponent) === null) {
                return false;
            }
        }

        $kingPlayer = $copy->getKing($move->getPlayer());

        // Having a board without a king happens only in unittests
        if ($kingPlayer === null) {
            return false;
        }

        return $this->checkedHelper->isKingChecked($kingPlayer, $copy);
    }
}
