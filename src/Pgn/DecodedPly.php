<?php
namespace Pjio\Chessboard\Pgn;

use Pjio\Chessboard\AbstractPlayer;
use Pjio\Chessboard\Exception\ANParserException;
use Pjio\Chessboard\Piece\AbstractPiece;
use Pjio\Chessboard\Piece\Bishop;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\Piece\Knight;
use Pjio\Chessboard\Piece\Pawn;
use Pjio\Chessboard\Piece\Queen;
use Pjio\Chessboard\Piece\Rook;

/**
 * DecodedPly represents a single move of a piece by one player.
 */
class DecodedPly
{
    public const PIECE_FQCN = [
        ''  => Pawn::class,
        'K' => King::class,
        'Q' => Queen::class,
        'R' => Rook::class,
        'N' => Knight::class,
        'B' => Bishop::class,
    ];

    private AbstractPlayer $player;

    private string $piece;
    private string $encodedPly;
    private string $moveNo;
    private string $fromFile;
    private string $fromRank;
    private string $capture;
    private string $toFile;
    private string $toRank;
    private string $promote;
    private string $checked;
    private string $checkmate;
    private string $enPassant;
    private string $kingside;
    private string $queenside;
    private string $nag;

    public function __construct(array $matches, string $encodedPly, AbstractPlayer $player, int $moveNo)
    {
        $this->encodedPly = $encodedPly;
        $this->player     = $player;
        $this->moveNo     = $moveNo;

        $this->piece      = $matches['piece'] ?? '';
        $this->fromFile   = $matches['fromFile'] ?? '';
        $this->fromRank   = $matches['fromRank'] ?? '';
        $this->capture    = $matches['capture'] ?? '';
        $this->toFile     = $matches['toFile'] ?? '';
        $this->toRank     = $matches['toRank'] ?? '';
        $this->promote    = $matches['promote'] ?? '';
        $this->checked    = $matches['checked'] ?? '';
        $this->checkmate  = $matches['checkmate'] ?? '';
        $this->enPassant  = $matches['en_passant'] ?? '';
        $this->kingside   = $matches['kingside'] ?? '';
        $this->queenside  = $matches['queenside'] ?? '';
        $this->nag        = $matches['nag'] ?? '';
    }

    public function getPiece(): string
    {
        return $this->piece;
    }

    public function getFromFile(): string
    {
        return $this->fromFile;
    }

    public function setFromFile(string $fromFile)
    {
        $this->fromFile = $fromFile;
    }

    public function getFromRank(): string
    {
        return $this->fromRank;
    }

    public function setFromRank(string $fromRank)
    {
        $this->fromRank = $fromRank;
    }

    public function getCapture(): string
    {
        return $this->capture;
    }

    public function getToFile(): string
    {
        return $this->toFile;
    }

    public function getToRank(): string
    {
        return $this->toRank;
    }

    public function getPromote(): string
    {
        return $this->promote;
    }

    public function getChecked(): string
    {
        return $this->checked;
    }

    public function getCheckmate(): string
    {
        return $this->checkmate;
    }

    public function getEnPassant(): string
    {
        return $this->enPassant;
    }

    public function getEncodedPly(): string
    {
        return $this->encodedPly;
    }

    public function getPlayer(): AbstractPlayer
    {
        return $this->player;
    }

    public function hasTargetPosition(): bool
    {
        return !empty($this->toFile) && !empty($this->toRank);
    }

    public function hasMissingFrom()
    {
        return empty($this->fromFile) || empty($this->fromRank);
    }

    public function getPieceFqcn(): string
    {
        if (!array_key_exists($this->piece, self::PIECE_FQCN)) {
            throw new ANParserException(sprintf('Invalid piece identifier: "%s"', $this->piece));
        }

        return self::PIECE_FQCN[$this->piece];
    }

    public function getPieceName(): string
    {
        return preg_replace('/^.*\\\\/', '', $this->getPieceFqcn());
    }

    public function debugString(): string
    {
        return sprintf(
            '"%s" => pi=%s, ff=%s, fr=%s, ca=%s, tf=%s, tr=%s, pr=%s, ch=%s, ep=%s, ks=%s, qs=%s, ng=%s',
            $this->encodedPly,
            $this->piece,
            $this->fromFile,
            $this->fromRank,
            $this->capture,
            $this->toFile,
            $this->toRank,
            $this->promote,
            $this->checked,
            $this->enPassant,
            $this->kingside,
            $this->queenside,
            $this->nag,
        );
    }

    public function readableString(): string
    {
        $prefix = sprintf('%d. %s', $this->moveNo, $this->player->getName());

        if (!empty($this->kingside)) {
            return sprintf('%s Kingside Castling', $prefix);
        }

        if (!empty($this->queenside)) {
            return sprintf('%s Queenside Castling', $prefix);
        }

        $extra = ''; // tbd

        return sprintf(
            '%s moves %s from %s%s to %s%s%s',
            $prefix,
            $this->getPieceName(),
            strtoupper($this->fromFile),
            $this->fromRank,
            strtoupper($this->toFile),
            $this->toRank,
            $extra
        );
    }

    public function __toString(): string
    {
        return $this->readableString();
    }

    public function isCastlingKingSide(): bool
    {
        return !empty($this->kingside);
    }

    public function isCastlingQueenSide(): bool
    {
        return !empty($this->queenside);
    }

    public function getNag(): string
    {
        return $this->nag;
    }
}
