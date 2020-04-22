<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\Pawn;
use Pjio\Chessboard\White;

class PawnRule extends AbstractRule
{
    protected const PIECE_TYPE = Pawn::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
        $direction = get_class($move->getPlayer()) == White::class ? 1 : -1;
        $startRank = get_class($move->getPlayer()) == Black::class ? Square::RANK_7 : Square::RANK_2;

        $from = $move->getFrom();
        $to   = $move->getTo();

        // The only possible moves are within the same and the neighboring files
        if (abs($from->getFile() - $to->getFile()) > 1) {
            return false;
        }

        $capturedPiece = $chessboard->getPieceBySquare($move->getTo());

        if ($from->getFile() === $to->getFile()) {
            // On forward move no capture is allowed
            if ($capturedPiece !== null) {
                return false;
            }

            $allowedRanks = [$from->getRank() + $direction];

            // If not blocked and as its first move, the pawn may move forward two squares at once
            if ($from->getRank() === $startRank
                && null === $chessboard->getPieceBySquare(new Square($from->getFile(), $from->getRank() + $direction))
            ) {
                $allowedRanks[] = $from->getRank() + $direction * 2;
            }

            if (!in_array($to->getRank(), $allowedRanks)) {
                return false;
            }

        } else {
            // Diagonal move must be excactly one rank far
            if ($from->getRank() + $direction !== $to->getRank()) {
                return false;
            }

            // On diagonal move must capture an opposing piece
            if ($capturedPiece === null || $capturedPiece->getPlayer() == $move->getPlayer()) {
                return false;
            }
        }

        return true;
    }
}
