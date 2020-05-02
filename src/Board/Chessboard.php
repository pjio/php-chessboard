<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\AbstractPlayer;
use Pjio\Chessboard\Exception\ChessboardException;
use Pjio\Chessboard\Exception\InvalidMoveException;
use Pjio\Chessboard\Exception\InvalidPromotionException;
use Pjio\Chessboard\Exception\MultiplePiecesOnSquareException;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Piece\AbstractPiece;
use Pjio\Chessboard\Piece\Bishop;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\Piece\Knight;
use Pjio\Chessboard\Piece\Pawn;
use Pjio\Chessboard\Piece\Queen;
use Pjio\Chessboard\Piece\Rook;
use Pjio\Chessboard\Rule\KingRule;

/**
 * Chessboard is the model for the board and all the pieces
 *
 * Attention: New properties with objects/references must be handled in __clone()
 */
class Chessboard
{
    private const PROMOTION_FQCN = [
        'queen'  => Queen::class,
        'rook'   => Rook::class,
        'bishop' => Bishop::class,
        'knight' => Knight::class,
    ];

    private array $pieces;
    private int $plyCount;
    private bool $useIndex;
    private array $pieceIndex;

    public function __construct(array $pieces, int $plyCount = 0, bool $useIndex = true)
    {
        /** @var AbstractPiece $piece */
        foreach ($pieces as $piece) {
            $piece->setChessboard($this);
        }

        $this->pieces   = $pieces;
        $this->plyCount = $plyCount;
        $this->useIndex = $useIndex;

        if ($this->useIndex) {
            $this->buildIndex();
        } else {
            $this->ensureMaxOnePiecePerSquare();
        }
    }

    public function getPiecesIterator(): iterable
    {
        return $this->pieces;
    }

    public function getPieceBySquare(Square $square): ?AbstractPiece
    {
        if ($this->useIndex) {
            return $this->pieceIndex[$square->__toString()] ?? null;
        }

        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            if ($piece->getSquare() == $square) {
                return $piece;
            }
        }

        return null;
    }

    public function checkSquareIsFree(Square $square): bool
    {
        return 0 === count(array_filter(
            $this->pieces,
            function (AbstractPiece $piece) use ($square) {
                return $piece->getSquare() == $square;
            }
        ));
    }

    public function __clone()
    {
        $clonedPieces = [];

        /** @var AbstractPiece $piece */
        foreach ($this->getPiecesOnBoard() as $piece) {
            $clonedPieces[] = $piece->getClone($this);
        }

        $this->pieces = $clonedPieces;

        $this->buildIndex();
    }

    public function move(Move $move): void
    {
        /** @var AbstractPiece $piece */
        $piece = $this->getPieceBySquare($move->getFrom());

        /** @var AbstractPiece $capturePiece */
        $capturePiece = $this->getPieceBySquare($move->getTo());

        if ($capturePiece === null && $move->getCaptureEnPassant() !== null) {
            // Re-fetch the piece in case $move belongs to another Chessboard instance
            // (This might be a clone, see Pjio\Chessboard\Rule\AbstractRule::isOwnKingCheckedAfterMove())
            $capturePiece = $this->getPieceBySquare($move->getCaptureEnPassant()->getSquare());
        }

        if ($capturePiece !== null) {
            if ($capturePiece->getPlayer() == $piece->getPlayer()) {
                throw new InvalidMoveException('Can\'t remove piece of active player');
            }

            $capturePiece->removeFromBoard();
        }

        $piece->setSquare($move->getTo());

        if ($move->isCastling()) {
            $this->handleCastling($move);
        }

        if (!empty($move->getPromotion())) {
            $this->handlePromotion($move, $piece);
        }

        $this->plyCount++;

        if ($move->isMovePassant()) {
            /** @var Pawn $piece */
            $piece->setMovePassantPly($this->plyCount);
        }
    }

    public function getKing(AbstractPlayer $player): ?King
    {
        $list = $this->findPieces($player, King::class);

        return $list[0] ?? null;
    }

    public function findPieces(AbstractPlayer $player, string $fqcn): array
    {
        $pieceList = [];

        /** @var AbstractPiece $piece */
        foreach ($this->getPiecesOnBoard() as $piece) {
            if (get_class($piece) === $fqcn && $piece->getPlayer() == $player) {
                $pieceList[] = $piece;
            }
        }

        return $pieceList;
    }

    public function getPiecesOnBoard(): array
    {
        return array_filter($this->pieces, fn($piece) => !$piece->isRemoved());
    }

    public function getPlyCount(): int
    {
        return $this->plyCount;
    }

    public function notifySquareChanged(Square $oldSquare, ?Square $newSquare): void
    {
        if ($this->useIndex) {
            $this->updateIndex($oldSquare, $newSquare);
        }
    }

    private function buildIndex(): void
    {
        $this->pieceIndex = [];

        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            $square = $piece->getSquare();
            if ($square === null) {
                continue;
            }
            $key = $piece->getSquare()->__toString();

            if (isset($this->pieceIndex[$key])) {
                throw new MultiplePiecesOnSquareException(
                    sprintf('Square is occupied by more than one piece: %s', $key)
                );
            }

            $this->pieceIndex[$key] = $piece;
        }
    }

    private function updateIndex(Square $oldSquare, ?Square $newSquare): void
    {
        $oldKey = $oldSquare->__toString();
        if (!isset($this->pieceIndex[$oldKey])) {
            throw new ChessboardException(sprintf('No piece found at: %s', $oldKey));
        }

        $piece = $this->pieceIndex[$oldKey];
        unset($this->pieceIndex[$oldKey]);

        if ($newSquare === null) {
            return;
        }

        $newKey = $newSquare->__toString();
        if (isset($this->pieceIndex[$newKey])) {
            throw new MultiplePiecesOnSquareException(
                sprintf('Square would be occupied by more than one piece: %s', $newKey)
            );
        }

        $this->pieceIndex[$newKey] = $piece;
    }

    private function ensureMaxOnePiecePerSquare(): void
    {
        $squareList = [];

        /** @var AbstractPiece $piece */
        foreach ($this->pieces as $piece) {
            $square = $piece->getSquare();
            if ($square === null) {
                continue;
            }
            $key = $piece->getSquare()->__toString();

            if (isset($squareList[$key])) {
                throw new MultiplePiecesOnSquareException(
                    sprintf('Square is occupied by more than one piece: %s', $key)
                );
            }

            $squareList[$key] = true;
        }
    }

    /**
     * handleCastling should be calling when the isCastling flag is set to move the rook additionally.
     */
    private function handleCastling(Move $move): void
    {
        $to         = $move->getTo();
        $isKingside = $to->getFile() === KingRule::CASTLING_KINGSIDE_KING;

        $fileFrom = $isKingside ? Square::FILE_H : Square::FILE_A;
        $fileTo   = $isKingside ? KingRule::CASTLING_KINGSIDE_ROOK : KingRule::CASTLING_QUEENSIDE_ROOK;
        $rookFrom = new Square($fileFrom, $to->getRank());
        $rookTo   = new Square($fileTo, $to->getRank());

        $rook = $this->getPieceBySquare($rookFrom);
        $rook->setSquare($rookTo);
    }

    private function handlePromotion(Move $move, AbstractPiece $pawn): void
    {
        $square = $pawn->getSquare();
        $fqcn   = self::PROMOTION_FQCN[$move->getPromotion()];

        /** @var AbstractPiece $promotedPiece */
        $promotedPiece = new $fqcn($pawn->getPlayer(), $square);
        $promotedPiece->setChessboard($this);

        $pawn->removeFromBoard();

        $this->pieces[] = $promotedPiece;

        if ($this->useIndex) {
            $this->pieceIndex[$square->__toString()] = $promotedPiece;
        }
    }
}
