<?php
namespace Pjio\Chessboard\Pgn;

use Pjio\Chessboard\Exception\PgnParserException;
use Pjio\Chessboard\Pgn\DecodedPly;
use Generator;

/**
 * PgnParser is a (limited) parser for PGN files:
 * https://en.wikipedia.org/wiki/Portable_Game_Notation
 */
class PgnParser
{
    private const RX_HEADER = '/^ *\[(?<key>[a-zA-Z]+) +"(?<value>[^"]*)" *\] *$/';
    private const RX_STRIP_COMMENTS = '/\{[^}]*\}|\[[^\]]*\]/';
    private const RX_STRIP_BLACK_MOVE_PREFACE = '/[0-9]+\.\.\./';
    private const RX_ABANDONED_GAME = '/^ *[01]-[01] *$/';

    private ANParser $algebraicNotationParser;

    public function __construct(ANParser $algebraicNotationParser)
    {
        $this->algebraicNotationParser = $algebraicNotationParser;
    }

    public function parse(iterable $file, bool $printSteps = false, bool $asString = false): Generator
    {
        $header   = [];
        $gameList = [];

        foreach ($file as $line) {
            $matches = [];
            if (empty(trim($line))) {
                continue;
            } elseif (preg_match(self::RX_HEADER, $line, $matches)) {
                $header[$matches['key']] = $matches['value'];
                continue;
            } elseif (preg_match(self::RX_ABANDONED_GAME, $line)) {
                $header = [];
                continue;
            } elseif (substr($line, 0, 3) !== '1. ') {
                throw new PgnParserException('Unable to parse line: ' . $line);
            }

            $title = sprintf(
                'Game: %s, %s vs. %s (%s %s)',
                $header['Event']   ?? '%Event%',
                $header['White']   ?? '%White%',
                $header['Black']   ?? '%Black%',
                $header['UTCDate'] ?? '%UTCDate%',
                $header['UTCTime'] ?? '%UTCTime%'
            );

            if ($printSteps) {
                printf('Game: %s', $title);
            }

            $encodedGame = $this->simplify($line);

            list ($decodedPlyList, $decodedResult) = $this->algebraicNotationParser->parse($encodedGame, $printSteps, $asString);

            if ($asString) {
                $decodedPlyList = array_map(fn(DecodedPly $ply): string => $ply->__toString(), $decodedPlyList);
            }

            yield [
                'title'  => $title,
                'header' => $header,
                'plies'  => $decodedPlyList,
                'result' => $decodedResult,
            ];

            $header = [];
        }
    }

    /**
     * The PGN / Algebraic Notation specification is a lot more complex than what this parser covers at the moment.
     * (See: http://www.saremba.de/chessgml/standards/pgn/pgn-complete.htm)
     * For now some (not essential) parts get removed before parsing.
     */
    private function simplify(string $encodedGame): string
    {
        // Strip comments
        $encodedGame = preg_replace(self::RX_STRIP_COMMENTS, '', $encodedGame);

        // Remove explicit black move indicators
        return preg_replace(self::RX_STRIP_BLACK_MOVE_PREFACE, '', $encodedGame);
    }
}
