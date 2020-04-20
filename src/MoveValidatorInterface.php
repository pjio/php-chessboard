<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Move;
use Pjio\Chessboard\Board\Chessboard;

interface MoveValidatorInterface
{
    public function isValidMove(Move $move, Chessboard $chessboard): bool;
}
