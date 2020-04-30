<?php
namespace Pjio\Chessboard\Pgn;

use Pjio\Chessboard\Exception\ANParserException;

/**
 * DecodedResult represents the result of a game.
 */
class DecodedResult
{
    public const BLACK_WINS  = '0-1';
    public const WHITE_WINS  = '1-0';
    public const DRAW        = '1/2-1/2';
    public const OTHER       = '*';

    private string $result;

    public function __construct(string $result)
    {
        if (!in_array($result, [self::BLACK_WINS, self::WHITE_WINS, self::DRAW, self::OTHER])) {
            throw new ANParserException(sprintf('Invalid result: "%s"', $result));
        }

        $this->result = $result;
    }

    public function isDraw(): bool
    {
        return $this->result === self::DRAW;
    }

    public function isOther(): bool
    {
        return $this->result === self::OTHER;
    }

    public function blackWins(): bool
    {
        return $this->result === self::BLACK_WINS;
    }

    public function whiteWins(): bool
    {
        return $this->result === self::WHITE_WINS;
    }

    public function __toString(): string
    {
        if ($this->isDraw()) {
            return 'Draw';
        } elseif ($this->blackWins()) {
            return 'Black wins';
        } elseif ($this->whiteWins()) {
            return 'White wins';
        } elseif ($this->isOther()) {
            return 'Other';
        }
    }
}
