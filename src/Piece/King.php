<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Piece\AbstractPiece;

class King extends AbstractPiece
{
    public function getName(): string
    {
        return 'King';
    }
}
