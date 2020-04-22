<?php
namespace Pjio\Chessboard\Helper;

use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\MoveValidator;
use Pjio\Chessboard\Piece\AbstractPiece;
use Pjio\Chessboard\Piece\King;

class CheckedHelper
{
    private MoveValidator $moveValidator;

    public function __construct()
    {
        $this->moveValidator = new MoveValidator();
    }

    public function isKingChecked(King $king, Chessboard $chessboard): bool
    {
        $opposingPieces = $this->getOpposingPieces($king, $chessboard);

        /** @var AbstractPiece $attacker */
        foreach ($opposingPieces as $attacker) {
            if ($attacker->isRemoved()) {
                continue;
            }

            $move = new Move($attacker->getPlayer(), $attacker->getSquare(), $king->getSquare());
            if ($this->moveValidator->isValidMove($move, $chessboard)) {
                return true;
            }
        }

        return false;
    }

    private function getOpposingPieces(King $king, Chessboard $chessboard): array
    {
        $opposingPieces = [];

        /** @var AbstractPiece $piece */
        foreach ($chessboard->getPiecesIterator() as $piece) {
            if ($piece->getPlayer() != $king->getPlayer()) {
                $opposingPieces[] = $piece;
            }
        }

        return $opposingPieces;
    }
}
