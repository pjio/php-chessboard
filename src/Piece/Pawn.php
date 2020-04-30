<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Board\Chessboard;
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

    public function getClone(Chessboard $chessboard): AbstractPiece
    {
        $clone = parent::getClone($chessboard);

        if ($this->movePassantPly !== null) {
            $clone->movePassantPly = $this->movePassantPly;
        }

        return $clone;
    }
}
