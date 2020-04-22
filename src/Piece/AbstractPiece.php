<?php
namespace Pjio\Chessboard\Piece;

use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\SquareIsOccupiedException;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\AbstractPlayer;
use RuntimeException;

/**
 * AbstractPiece represents a unit on the chessboard.
 */
abstract class AbstractPiece
{
    private AbstractPlayer $player;
    private ?Square $square = null;
    private ?Chessboard $chessboard = null;

    public function __construct(AbstractPlayer $player, Square $square)
    {
        $this->player = $player;
        $this->square = $square;
    }

    abstract public function getName(): string;

    public function getPlayer(): AbstractPlayer
    {
        return $this->player;
    }

    public function getSquare(): ?Square
    {
        return $this->square;
    }

    public function setChessboard(Chessboard $chessboard)
    {
        if ($this->chessboard !== null) {
            throw new RuntimeException('A piece can only be added to one chessboard!');
        }

        $this->chessboard = $chessboard;
    }

    public function setSquare(Square $square)
    {
        if ($this->chessboard === null) {
            throw new RuntimeException('Assign the piece to a chessboard before using it!');
        }

        if (!$this->chessboard->checkSquareIsFree($square)) {
            throw new SquareIsOccupiedException(
                sprintf('The square %s is occupied!', $square)
            );
        }

        $this->square = $square;
    }

    public function removeFromBoard(): void
    {
        $this->square = null;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function isSame(AbstractPiece $piece): bool
    {
        return get_class($this) === get_class($piece) && get_class($this->player) === get_class($piece->getPlayer());
    }
}
