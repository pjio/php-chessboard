<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Piece\AbstractPiece;

class Pawn extends AbstractPiece
{
    public function getName(): string
    {
        return 'Pawn';
    }
}
