<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Piece\AbstractPiece;

class King extends AbstractPiece
{
    private bool $moved = false;

    public function getName(): string
    {
        return 'King';
    }

    public function setSquare(Square $square): void
    {
        parent::setSquare($square);
        $this->moved = true;
    }

    public function isMoved(): bool
    {
        return $this->moved;
    }
}
