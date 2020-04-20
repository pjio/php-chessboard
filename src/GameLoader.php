<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Board\ChessboardFactory;

/**
 * GameLoader is a helper to create, load and persist games
 */
class GameLoader
{
    public function __construct()
    {
        $this->chessboardFactory = new ChessboardFactory();
    }

    public function createNewGame(): Game
    {
        $white = new White();
        $black = new Black();
        $chessboard = $this->chessboardFactory->createNewChessboard($white, $black);

        return new Game($chessboard, $white, $black);
    }
}
