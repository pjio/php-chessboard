<?php
namespace Tests;

use Pjio\Chessboard\Pieces\AbstractPiece;

class PieceImpl extends AbstractPiece
{
    public function getName(): string
    {
        return 'Reusable, testable childclass';
    }
}
