<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\AbstractPlayer;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Move;

/**
 * Game is the model for a single game between two players
 */
class Game
{
    private Chessboard $chessboard;
    private White $white;
    private Black $black;
    private AbstractPlayer $activePlayer;

    public function __construct(Chessboard $chessboard, White $white, Black $black)
    {
        $this->chessboard = $chessboard;
        $this->white = $white;
        $this->black = $black;
        $this->activePlayer = $white;
    }

    public function getChessboard(): Chessboard
    {
        return $this->chessboard;
    }

    /**
     * @throws InvalidMoveException
     */
    public function move(Move $move): void
    {
        if ($move->getPlayer() !== $this->activePlayer) {
            throw new InvalidMoveException(
                sprintf('%s has to be the active player to make a move!', $move->getPlayer()->getName())
            );
        }

        $piece = $this->chessboard->getPieceBySquare($move->getFrom());

        if ($piece === null) {
            throw new InvalidMoveException(
                sprintf('No piece found at: %s', $move->getFrom())
            );
        }

        if ($piece->getPlayer() != $this->activePlayer) {
            throw new InvalidMoveException(
                sprintf('Only pieces of player "%s" can be moved during this turn', $move->getPlayer())
            );
        }


        throw new InvalidMoveException('not implemented yet');
    }

    public function getActivePlayer(): AbstractPlayer
    {
        return $this->activePlayer;
    }

    public function isFinished(): bool
    {
        return false;
    }
}
