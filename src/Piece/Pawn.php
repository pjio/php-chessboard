<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Piece\AbstractPiece;

class Pawn extends AbstractPiece
{
    /**
     * The ply in which this pawn moved two squares.
     * This information is necessary for the en passant rule.
     */
    private ?int $movePassantPly = null;

    public function getMovePassantPly(): ?int
    {
        return $this->movePassantPly;
    }

    public function setMovePassantPly(int $movePassantPly): void
    {
        $this->movePassantPly = $movePassantPly;
    }

    public function getName(): string
    {
        return 'Pawn';
    }
}
