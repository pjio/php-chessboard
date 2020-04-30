<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\InvalidPromotionException;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\Pawn;
use Pjio\Chessboard\White;

class PawnRule extends AbstractRule
{
    protected const PIECE_TYPE = Pawn::class;
    protected const VALID_PROMOTIONS = ['', 'queen', 'rook', 'bishop', 'knight'];

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
        $direction   = get_class($move->getPlayer()) == White::class ? 1 : -1;
        $startRank   = get_class($move->getPlayer()) == Black::class ? Square::RANK_7 : Square::RANK_2;
        $promoteRank = get_class($move->getPlayer()) == White::class ? Square::RANK_8 : Square::RANK_1;

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
                $move->setMovePassant(true);
            }

            if (!in_array($to->getRank(), $allowedRanks)) {
                return false;
            }
        } else {
            // Diagonal move must be excactly one rank far
            if ($from->getRank() + $direction !== $to->getRank()) {
                return false;
            }

            // Pawns can capture other Pawns en passant
            if ($capturedPiece === null) {
                /** @var Pawn|null $captureEnPassant */
                $captureEnPassant = $chessboard->getPieceBySquare(new Square($to->getFile(), $from->getRank()));

                if ($captureEnPassant !== null && get_class($captureEnPassant) === Pawn::class
                    && $captureEnPassant->getMovePassantPly() === $chessboard->getPlyCount()
                ) {
                    $capturedPiece = $captureEnPassant;
                }
            }

            // On diagonal move must capture an opposing piece
            if ($capturedPiece === null || $capturedPiece->getPlayer() == $move->getPlayer()) {
                return false;
            }
        }

        $promotion = $move->getPromotion();

        if (!empty($promotion) && $to->getRank() !== $promoteRank) {
            throw new InvalidPromotionException('Can\'t promote pawn on this move!');
        } elseif ($to->getRank() === $promoteRank) {
            if (!in_array($promotion, self::VALID_PROMOTIONS)) {
                throw new InvalidPromotionException(sprintf('Invalid Promotion: %s', $promotion));
            }

            if ($promotion === '') {
                $move->setPromotion('queen');
            }
        }

        if (isset($captureEnPassant)) {
            $move->setCaptureEnPassant($captureEnPassant);
        }

        return true;
    }
}
