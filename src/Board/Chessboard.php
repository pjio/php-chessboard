<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\MultiplePiecesOnSquareException;
use Pjio\Chessboard\Pieces\AbstractPiece;

/**
 * Chessboard is the model for the board and all the pieces
 */
class Chessboard
{
    private array $pieces;

    public function __construct(array $pieces)
    {
        /** @var AbstractPiece $piece */
        foreach ($pieces as $piece) {
            $piece->setChessboard($this);
        }

        $this->pieces = $pieces;

        $this->ensureMaxOnePiecePerSquare();
    }

    public function getPiecesIterator(): iterable
    {
        return $this->pieces;
    }

    public function getPieceBySquare(Square $square): ?AbstractPiece
    {
        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            if ($piece->getSquare() == $square) {
                return $piece;
            }
        }

        return null;
    }

    public function checkSquareIsFree(Square $square): bool
    {
        return 0 === count(array_filter(
            $this->pieces,
            function (AbstractPiece $piece) use ($square) {
                return $piece->getSquare() == $square;
            }
        ));
    }

    private function ensureMaxOnePiecePerSquare(): void
    {
        $squareList = [];

        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            $square = $piece->getSquare();
            if ($square === null) {
                continue;
            }
            $key = $piece->getSquare()->__toString();

            if (isset($squareList[$key])) {
                throw new MultiplePiecesOnSquareException(
                    sprintf('Square is occupied by more than one piece: %s', $key)
                );
            }

            $squareList[$key] = true;
        }
    }
}
