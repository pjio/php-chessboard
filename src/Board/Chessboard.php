<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\AbstractPlayer;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Exception\MultiplePiecesOnSquareException;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\AbstractPiece;

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

    public function __clone()
    {
        $clonedPieces = [];

        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            $clonedPieces[] = $piece->getClone($this);
        }

        $this->pieces = $clonedPieces;
    }

    public function move(Move $move): void
    {
        /** @var AbstractPiece $piece */
        $piece = $this->getPieceBySquare($move->getFrom());

        /** @var AbstractPiece $capturePiece */
        $capturePiece = $this->getPieceBySquare($move->getTo());
        if ($capturePiece !== null) {
            if ($capturePiece->getPlayer() == $piece->getPlayer()) {
                throw new InvalidMoveException('Can\'t remove piece of active player');
            }

            $capturePiece->removeFromBoard();
        }

        $piece->setSquare($move->getTo());
    }

    public function getKing(AbstractPlayer $player): ?King
    {
        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            if (get_class($piece) === King::class && $piece->getPlayer() == $player) {
                return $piece;
            }
        }

        return null;
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
