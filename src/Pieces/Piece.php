<?php
namespace Pjio\Chessboard\Pieces;

use Pjio\Chessboard\Square;
use Pjio\Chessboard\Player;

class Piece
{
    private Player $player;
    private Square $square;

    public function __construct(Player $player, Square $square)
    {
        $this->player = $player;
        $this->square = $square;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getSquare(): Square
    {
        return $this->square;
    }
}
