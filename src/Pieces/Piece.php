<?php
namespace Pjio\Chessboard\Pieces;

use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\AbstractPlayer;

class Piece
{
    private AbstractPlayer $player;
    private Square $square;

    public function __construct(AbstractPlayer $player, Square $square)
    {
        $this->player = $player;
        $this->square = $square;
    }

    public function getPlayer(): AbstractPlayer
    {
        return $this->player;
    }

    public function getSquare(): Square
    {
        return $this->square;
    }
}
