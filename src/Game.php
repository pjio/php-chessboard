<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Exception\MoveAfterGameFinishedException;
use Pjio\Chessboard\Piece\AbstractPiece;

/**
 * Game is the model for a single game between two players
 */
class Game
{
    private Chessboard $chessboard;
    private White $white;
    private Black $black;
    private AbstractPlayer $activePlayer;
    private MoveValidator $moveValidator;

    public function __construct(Chessboard $chessboard, White $white, Black $black)
    {
        $this->chessboard = $chessboard;
        $this->white = $white;
        $this->black = $black;
        $this->activePlayer = $white;

        $this->moveValidator = new MoveValidator();
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
        if ($this->isFinished()) {
            throw new MoveAfterGameFinishedException('Move not allowed after game is finished!');
        }

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

        if (!$this->moveValidator->isValidMove($move, $this->chessboard)) {
            throw new InvalidMoveException(
                sprintf('Invalid move for %s', $piece)
            );
        }

        $this->chessboard->move($move);
        $this->switchActivePlayer();
    }

    public function getActivePlayer(): AbstractPlayer
    {
        return $this->activePlayer;
    }

    public function isFinished(): bool
    {
        $whiteKing = $this->chessboard->getKing($this->white);
        $blackKing = $this->chessboard->getKing($this->black);

        return $whiteKing === null || $whiteKing->isRemoved()
            || $blackKing === null || $blackKing->isRemoved();
    }

    private function switchActivePlayer(): void
    {
        if ($this->activePlayer === $this->black) {
            $this->activePlayer = $this->white;
        } else {
            $this->activePlayer = $this->black;
        }
    }
}
