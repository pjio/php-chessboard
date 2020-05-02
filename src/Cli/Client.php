<?php
namespace Pjio\Chessboard\Cli;

use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Exception\GameAbortException;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Exception\InvalidPromotionException;
use Pjio\Chessboard\Game;
use Pjio\Chessboard\GameLoader;

/**
 * Client offers an cli interface for one game
 */
class Client
{
    private Game $game;
    private GameLoader $gameLoader;
    private ChessboardSerializer $chessboardPrinter;
    private InputReader $inputReader;

    public function __construct()
    {
        $this->gameLoader        = new GameLoader();
        $this->chessboardPrinter = new ChessboardSerializer();
    }

    public function run(): void
    {
        try {
            $this->game        = $this->gameLoader->createNewGame();
            $this->inputReader = new InputReader($this->game);
            $this->gameLoop();
        } catch (GameAbortException $e) {
            printf('Game abort: %s%s', $e->getMessage(), PHP_EOL);
        }
    }

    private function gameLoop(): void
    {
        do {
            $this->printChessboard();
            $this->handlePly();
        } while (!$this->game->isFinished());

        printf("Game finished!%s", PHP_EOL);
    }

    private function handlePly(): void
    {
        $moved = false;
        do {
            $move = $this->inputReader->interact();

            try {
                $this->game->move($move);
                $moved = true;
            } catch (InvalidMoveException|InvalidPromotionException $e) {
                printf('ERROR: %s%s', $e->getMessage(), PHP_EOL);
            }
        } while (!$moved);
    }

    private function printChessboard()
    {
        printf('%s%s%s', PHP_EOL, $this->chessboardPrinter->serialize($this->game->getChessboard(), true), PHP_EOL);
    }
}
