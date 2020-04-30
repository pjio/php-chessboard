<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Piece\AbstractPiece;

class Rook extends AbstractPiece
{
    private bool $moved = false;

    public function getName(): string
    {
        return 'Rook';
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

    public function getClone(Chessboard $chessboard): self
    {
        $clone = parent::getClone($chessboard);
        $clone->moved = $this->moved;
        return $clone;
    }
}
