<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;

class KingRule implements MoveValidatorInterface
{
    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        return true;
    }
}
