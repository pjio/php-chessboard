<?php
namespace Tests;

use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\White;
use RuntimeException;

class MoveHelper
{
    private White $white;
    private Black $black;
    private ChessboardSerializer $chessboardSerializer;

    public function __construct()
    {
        $this->white = new White();
        $this->black = new Black();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    /**
     * @param  Square $squareFrom  Points to the piece on the $fromBoard which should be moved
     * @param  string $fromBoard   The board on which the move should occur
     * @param  string $toBoard     All squares with the piece on this board indicate a valid move!
     *
     * @return array
     */
    public function getMoves(string $testScenario, Square $squareFrom, string $fromBoard, string $toBoard): array
    {
        $moveList = [];

        /** @var Chessboard $chessboardFrom */
        $chessboardFrom = $this->chessboardSerializer->unserialize($fromBoard);

        /** @var Chessboard $chessboardTo */
        $chessboardTo = $this->chessboardSerializer->unserialize($toBoard);

        $pieceToMove = $chessboardFrom->getPieceBySquare($squareFrom);

        if ($pieceToMove === null) {
            throw new RuntimeException('No piece at $fromSquare found!');
        }

        for ($rank = Square::RANK_1; $rank <= Square::RANK_8; $rank++) {
            for ($file = Square::FILE_A; $file <= Square::FILE_H; $file++) {
                $toSquare = new Square($file, $rank);
                $pieceAtTarget = $chessboardTo->getPieceBySquare($toSquare);
                $move = new Move($pieceToMove->getPlayer(), $pieceToMove->getSquare(), $toSquare);
                $name = sprintf('%s_%s_to_%s', $testScenario, $move->getFrom(), $move->getTo());

                if ($pieceAtTarget !== null && $pieceToMove->isSame($pieceAtTarget)) {
                    $moveList[$name] = [
                        'board'    => $fromBoard,
                        'move'     => $move,
                        'expected' => true,
                    ];
                } else {
                    $moveList[$name] = [
                        'board'    => $fromBoard,
                        'move'     => $move,
                        'expected' => false,
                    ];
                }
            }
        }

        if (count($moveList) !== 64) {
            throw new RuntimeException('MoveHelper: Unable to generate testdata from input!');
        }

        return $moveList;
    }
}