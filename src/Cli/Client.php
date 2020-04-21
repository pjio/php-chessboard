<?php
namespace Pjio\Chessboard\Cli;

use Pjio\Chessboard\GameLoader;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Exception\InvalidCoordinatesException;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Game;

/**
 * Client offers an cli interface for one game
 */
class Client
{
    private Game $game;
    private GameLoader $gameLoader;
    private ChessboardSerializer $chessboardPrinter;

    public function __construct()
    {
        $this->gameLoader = new GameLoader();
        $this->chessboardPrinter = new ChessboardSerializer();
    }

    public function run(): void
    {
        $this->game = $this->gameLoader->createNewGame();
        $this->gameLoop();
    }

    private function gameLoop(): void
    {
        do {
            echo $this->chessboardPrinter->serialize($this->game->getChessboard(), true) . PHP_EOL;
            $this->handleTurn();
        } while (!$this->game->isFinished());

        echo "Game finished!" . PHP_EOL;
    }

    private function handleTurn(): void
    {
        echo sprintf('Players turn: %s%s', $this->game->getActivePlayer()->getName(), PHP_EOL);

        $finished = false;
        do {
            do {
                $fromStr    = readline('Move from: ');
                $fromSquare = $this->parseSquare($fromStr);
            } while ($fromSquare === null);

            do {
                $toStr    = readline('Move to: ');
                $toSquare = $this->parseSquare($toStr);
            } while ($toSquare === null);

            $move = new Move($this->game->getActivePlayer(), $fromSquare, $toSquare);
            try {
                $this->game->move($move);
                $finished = true;
            } catch (InvalidMoveException $e) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            }
        } while (!$finished);
    }

    private function parseSquare(string $squareStr): ?Square
    {
        if (strlen($squareStr) !== 2) {
            echo 'ERROR: Positions must be two characters (e.g. a1, h8, ...)' . PHP_EOL;
            return null;
        }

        $file = ord(strtolower($squareStr[0])) - 97;
        $rank = ((int) $squareStr[1]) - 1;

        try {
            $square = new Square($file, $rank);
            return $square;
        } catch (InvalidCoordinatesException $e) {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            return null;
        }
    }
}
