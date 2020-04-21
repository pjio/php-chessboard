<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Pieces\Bishop;

class BishopRule extends BaseRule implements MoveValidatorInterface
{
    protected const PIECE_TYPE = Bishop::class;

    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        if ($this->isDifferentPieceType($move, $chessboard)
            || $this->isDifferentPlayer($move, $chessboard)
            || $this->isBlockedByOwnPiece($move, $chessboard)
        ) {
            return false;
        }

        $from = $move->getFrom();
        $to   = $move->getTo();

        $fileDist = abs($from->getFile() - $to->getFile());
        $rankDist = abs($from->getRank() - $to->getRank());

        // $from and $to must be diagonal to each other
        if ($fileDist !== $rankDist) {
            return false;
        }

        if (!$this->isFreePath($move->getFrom(), $move->getTo(), $chessboard)) {
            return false;
        }

        return !$this->isOwnKingCheckedAfterMove($move, $chessboard);
    }

    private function isFreePath(Square $from, Square $to, Chessboard $chessboard): bool
    {
        $moveFile = $from->getFile() < $to->getFile() ? 1 : -1;
        $moveRank = $from->getRank() < $to->getRank() ? 1 : -1;
        $startFile = $from->getFile() + $moveFile;
        $startRank = $from->getRank() + $moveRank;
        $exceedFile = $to->getFile() + $moveFile;
        $exceedRank = $to->getRank() + $moveRank;

        for ($file = $startFile; $file != $exceedFile; $file += $moveFile) {
            for ($rank = $startRank; $rank != $exceedRank; $rank += $moveRank) {
                $current = new Square($file, $rank);
                if ($current == $to) {
                    return true;
                }

                if (null !== $chessboard->getPieceBySquare($current)) {
                    return false;
                }
            }
        }

        return false;
    }
}
