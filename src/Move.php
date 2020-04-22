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
    private Square $from;
    private Square $to;

    public function __construct(AbstractPlayer $player, Square $from, Square $to)
    {
        $this->player = $player;
        $this->from = $from;
        $this->to = $to;
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
}
