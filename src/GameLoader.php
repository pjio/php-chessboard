<?php
namespace Pjio\Chessboard;

class GameLoader
{
    private ChessboardFactory $chessboardFactory;

    public function __construct(ChessboardFactory $chessboardFactory)
    {
        $this->chessboardFactory = $chessboardFactory;
    }

    public function createNewGame(): Game
    {
        $white = new White();
        $black = new Black();
        $chessboard = $this->chessboardFactory->createNewChessboard($white, $black);

        return new Game($chessboard, $white, $black);
    }
}
