<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Piece\AbstractPiece;

class Bishop extends AbstractPiece
{
    public function getName(): string
    {
        return 'Bishop';
    }
}
