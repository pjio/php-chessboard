<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Board\Square;

/**
 * Move represents an intended move of a piece by a player
 * It is not guaranteed, that this move is valid!
 */
class Move
{
    private AbstractPlayer $player;

    public function setPlayer(AbstractPlayer $player): void
    {
        $this->player = $player;
    }
    private Square $from;
    private Square $to;
    private bool $castling;
    private string $promotion;

    public function __construct(
        AbstractPlayer $player,
        Square $from,
        Square $to,
        bool $isCastling = false,
        string $promotion = ''
    ) {
        $this->player    = $player;
        $this->from      = $from;
        $this->to        = $to;
        $this->castling  = $isCastling;

        $this->setPromotion($promotion);
    }

    public function getPlayer(): AbstractPlayer
    {
        return $this->player;
    }

    public function getFrom(): Square
    {
        return $this->from;
    }

    public function getTo(): Square
    {
        return $this->to;
    }

    public function isCastling(): bool
    {
        return $this->castling;
    }

    public function setCastling(bool $castling): void
    {
        $this->castling = $castling;
    }

    public function getPromotion(): string
    {
        return $this->promotion;
    }

    public function setPromotion(string $promotion): void
    {
        $this->promotion = strtolower($promotion);
    }

    public function __toString(): string
    {
        return sprintf('%s: from %s to %s', $this->player, $this->from, $this->to);
    }
}
