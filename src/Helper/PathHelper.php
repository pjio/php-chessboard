<?php
namespace Pjio\Chessboard\Helper;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;

class PathHelper
{
    /**
     * Relies on being called with a diagonal path
     * Assumes that a piece on "$to" (if any) could be captured
     */
    public function isFreePathDiagonal(Square $from, Square $to, Chessboard $chessboard): bool
    {
        $moveFile = $from->getFile() < $to->getFile() ? 1 : -1;
        $moveRank = $from->getRank() < $to->getRank() ? 1 : -1;

        $file = $from->getFile() + $moveFile;
        $rank = $from->getRank() + $moveRank;
        do {
            $current = new Square($file, $rank);
            if ($current == $to) {
                return true;
            }

            if (null !== $chessboard->getPieceBySquare($current)) {
                return false;
            }

            $file += $moveFile;
            $rank += $moveRank;
        } while (true);
    }

    /**
     * Relies on being called with a linear path
     * Assumes that a piece on "$to" (if any) could be captured
     */
    public function isFreePathLinear(Square $from, Square $to, Chessboard $chessboard)
    {
        if ($from->getFile() === $to->getFile()) {
            $moveFile = 0;
        } else {
            $moveFile = $from->getFile() < $to->getFile() ? 1 : -1;
        }

        if ($from->getRank() === $to->getRank()) {
            $moveRank = 0;
        } else {
            $moveRank = $from->getRank() < $to->getRank() ? 1 : -1;
        }

        $file = $from->getFile() + $moveFile;
        $rank = $from->getRank() + $moveRank;
        do {
            $current = new Square($file, $rank);
            if ($current == $to) {
                return true;
            }

            if (null !== $chessboard->getPieceBySquare($current)) {
                return false;
            }

            $file += $moveFile;
            $rank += $moveRank;
        } while (true);
    }
}
