<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Board\Chessboard;

class Game
{
    private Chessboard $chessboard;
    private White $white;
    private Black $black;

    public function __construct(Chessboard $chessboard, White $white, Black $black)
    {
        $this->chessboard = $chessboard;
        $this->white = $white;
        $this->black = $black;
    }
}
