<?php
namespace Pjio\Chessboard\Rule;

use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\Piece\Rook;

class KingRule extends AbstractRule
{
    public const CASTLING_KINGSIDE_KING  = Square::FILE_G;
    public const CASTLING_KINGSIDE_ROOK  = Square::FILE_F;
    public const CASTLING_QUEENSIDE_KING = Square::FILE_C;
    public const CASTLING_QUEENSIDE_ROOK = Square::FILE_D;

    protected const PIECE_TYPE = King::class;

    protected function pieceRule(Move $move, Chessboard $chessboard): bool
    {
        $diffFile = abs($move->getFrom()->getFile() - $move->getTo()->getFile());
        $diffRank = abs($move->getFrom()->getRank() - $move->getTo()->getRank());

        if ($diffFile === 2) {
            if ($this->checkCastling($move, $chessboard)) {
                return true;
            }
        }

        if (($diffFile === 0 && $diffRank === 0) || $diffFile > 1 || $diffRank > 1) {
            return false;
        }

        return true;
    }

    private function checkCastling(Move $move, Chessboard $chessboard): bool
    {
        $from = $move->getFrom();
        $to   = $move->getTo();

        /** @var King $king */
        $king = $chessboard->getPieceBySquare($from);

        if (!$king instanceof King
            || $king->isMoved()
            || !$move->getPlayer()->isPlayer($king->getPlayer())
        ) {
            return false;
        }

        if ($from->getRank() !== $to->getRank()) {
            return false;
        }

        if (!in_array($to->getFile(), [self::CASTLING_KINGSIDE_KING, self::CASTLING_QUEENSIDE_KING])) {
            return false;
        }

        $isKingside = $to->getFile() === self::CASTLING_KINGSIDE_KING;
        $rookFile   = $isKingside ? Square::FILE_H : Square::FILE_A;
        $rank       = $king->getSquare()->getRank();

        /** @var Rook $rook */
        $rook = $chessboard->getPieceBySquare(new Square($rookFile, $rank));

        if (!$rook instanceof Rook
            || $rook->isMoved()
            || !$move->getPlayer()->isPlayer($rook->getPlayer())
        ) {
            return false;
        }

        // Check squares between are emtpy
        $fileRange = [$king->getSquare()->getFile(), $rook->getSquare()->getFile()];
        sort($fileRange);
        for ($file = $fileRange[0] + 1; $file < $fileRange[1]; $file++) {
            $piece = $chessboard->getPieceBySquare(new Square($file, $rank));
            if ($piece !== null) {
                return false;
            }
        }

        if ($this->checkedHelper->isKingChecked($king, $chessboard)) {
            return false;
        }

        $copyChessboard = clone $chessboard;
        $copyKing       = $copyChessboard->getPieceBySquare($king->getSquare());
        $copyKing->setSquare(new Square($isKingside ? Square::FILE_F : Square::FILE_D, $rank));

        if ($this->checkedHelper->isKingChecked($copyKing, $copyChessboard)) {
            return false;
        }

        $move->setCastling(true);
        return true;
    }
}
