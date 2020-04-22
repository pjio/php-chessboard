<?php
namespace Tests;

use Pjio\Chessboard\Piece\AbstractPiece;

class PieceImpl extends AbstractPiece
{
    public function getName(): string
    {
        return 'Reusable, testable childclass';
    }
}
