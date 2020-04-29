<?php
namespace Pjio\Chessboard\Cli;

use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\GameAbortException;
use Pjio\Chessboard\Exception\InvalidCoordinatesException;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Exception\InvalidPromotionException;
use Pjio\Chessboard\Game;
use Pjio\Chessboard\GameLoader;
use Pjio\Chessboard\Move;

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
        try {
            $this->game = $this->gameLoader->createNewGame();
            $this->gameLoop();
        } catch (GameAbortException $e) {
            printf('Game abort: %s%s', $e->getMessage(), PHP_EOL);
        }
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

        $moved = false;
        do {
            do {
                $fromStr = readline('Move from: ');
                list($fromSquare, $omit) = $this->parseSquare($fromStr);
            } while ($fromSquare === null);

            do {
                $toStr = readline('Move to: ');
                list($toSquare, $promotion) = $this->parseSquare($toStr, true);
            } while ($toSquare === null);

            $move = new Move($this->game->getActivePlayer(), $fromSquare, $toSquare, false, $promotion ?? '');
            try {
                $this->game->move($move);
                $moved = true;
            } catch (InvalidMoveException|InvalidPromotionException $e) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            }
        } while (!$moved);
    }

    private function parseSquare(string $squareStr, bool $allowPromotion = false): ?array
    {
        if (strtolower($squareStr) === 'exit') {
            throw new GameAbortException('Abort by user');
        }

        if ($allowPromotion) {
            $split = explode(' ', $squareStr);

            if (count($split) > 2) {
                echo 'ERROR: To promote a pawn enter: a1 queen, h8 knight, etc.' . PHP_EOL;
                return [null, null];
            }

            $squareStr = $split[0];
            $promotion = $split[1] ?? null;
        }

        if (strlen($squareStr) !== 2) {
            echo 'ERROR: Positions must be two characters (e.g. a1, h8, ...)' . PHP_EOL;
            return [null, null];
        }

        $file = ord(strtolower($squareStr[0])) - 97;
        $rank = ((int) $squareStr[1]) - 1;

        try {
            $square = new Square($file, $rank);
            return [$square, $promotion ?? null];
        } catch (InvalidCoordinatesException $e) {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            return [null, null];
        }
    }
}
