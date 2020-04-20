<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Pieces\Piece;

/**
 * Chessboard is the model for the board and all the pieces
 */
class Chessboard
{
    private array $pieces;

    public function __construct(array $pieces)
    {
        $this->pieces = $pieces;
    }

    public function getPiecesIterator(): iterable
    {
        return $this->pieces;
    }

    public function getPieceBySquare(Square $square): ?Piece
    {
        /** @var Piece $piece */
        foreach ($this->pieces as $piece) {
            if ($piece->getSquare() == $square) {
                return $piece;
            }
        }

        return null;
    }
}
