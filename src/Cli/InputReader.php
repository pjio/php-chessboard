<?php
namespace Pjio\Chessboard\Cli;

use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\GameAbortException;
use Pjio\Chessboard\Exception\InvalidCoordinatesException;
use Pjio\Chessboard\Game;
use Pjio\Chessboard\Move;

/**
 * InputReader interacts with the user to get an intended move
 */
class InputReader
{
    private Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function interact(): Move
    {
        printf('Players turn: %s%s', $this->game->getActivePlayer()->getName(), PHP_EOL);

        do {
            $fromStr = readline('Move from: ');
            list($fromSquare, $omit) = $this->parseSquare($fromStr);
        } while ($fromSquare === null);

        do {
            $toStr = readline('Move to: ');
            list($toSquare, $promotion) = $this->parseSquare($toStr, true);
        } while ($toSquare === null);

        return new Move($this->game->getActivePlayer(), $fromSquare, $toSquare, false, $promotion ?? '');
    }

    private function parseSquare(string $squareStr, bool $allowPromotion = false): ?array
    {
        if (strtolower($squareStr) === 'exit') {
            throw new GameAbortException('Abort by user');
        }

        if ($allowPromotion) {
            $split = explode(' ', $squareStr);

            if (count($split) > 2) {
                printf('ERROR: To promote a pawn enter: a1 queen, h8 knight, etc.%s', PHP_EOL);
                return [null, null];
            }

            $squareStr = $split[0];
            $promotion = $split[1] ?? null;
        }

        if (strlen($squareStr) !== 2) {
            printf('ERROR: Positions must be two characters (e.g. a1, h8, ...)%s', PHP_EOL);
            return [null, null];
        }

        $file = ord(strtolower($squareStr[0])) - 97;
        $rank = ((int) $squareStr[1]) - 1;

        try {
            $square = new Square($file, $rank);
            return [$square, $promotion ?? null];
        } catch (InvalidCoordinatesException $e) {
            printf('ERROR: %s%s', $e->getMessage(), PHP_EOL);
            return [null, null];
        }
    }
}
