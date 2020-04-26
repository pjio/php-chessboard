<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\AbstractPlayer;
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

    public function __construct(array $pieces, int $plyCount = 0)
    {
        /** @var AbstractPiece $piece */
        foreach ($pieces as $piece) {
            $piece->setChessboard($this);
        }

        $this->pieces   = $pieces;
        $this->plyCount = $plyCount;

        $this->ensureMaxOnePiecePerSquare();
    }

    public function getPiecesIterator(): iterable
    {
        return $this->pieces;
    }

    public function getPieceBySquare(Square $square): ?AbstractPiece
    {
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
    }

    public function move(Move $move): void
    {
        /** @var AbstractPiece $piece */
        $piece = $this->getPieceBySquare($move->getFrom());

        /** @var AbstractPiece $capturePiece */
        $capturePiece = $this->getPieceBySquare($move->getTo());

        if ($capturePiece === null) {
            $capturePiece = $move->getCaptureEnPassant();
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
    }
}
