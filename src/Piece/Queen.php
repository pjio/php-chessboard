<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Piece\AbstractPiece;

class Queen extends AbstractPiece
{
    public function getName(): string
    {
        return 'Queen';
    }
}
