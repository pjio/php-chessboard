<?php
namespace Tests;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Pgn\ANParser;
use Pjio\Chessboard\Pgn\PgnParser;
use Pjio\Chessboard\White;

class PgnParserTest extends TestCase
{
    private PgnParser $pgnParser;
    private White $white;
    private Black $black;

    public function setUp(): void
    {
        $stub = $this->createMock(ANParser::class);
        $stub->method('parse')->willReturn([[], []]);

        $this->pgnParser = new PgnParser($stub);
        $this->white = new White();
        $this->black = new Black();
    }

    public function testParseOpening()
    {
        $pgn = <<<EOF
[Event "EventVal"]
[Site "SiteVal"]
[White "WhiteVal"]
[Black "BlackVal"]
[Result "1-0"]
[UTCDate "2020.01.01"]
[UTCTime "00:00:01"]
[WhiteElo "123"]
[BlackElo "456"]
[WhiteRatingDiff "+78"]
[BlackRatingDiff "-90"]
[ECO "B10"]
[Opening "OpeningVal"]
[TimeControl "300+8"]
[Termination "Normal"]

1. AN not part of this test...
EOF;

        $lines = explode("\n", $pgn);
        $actual = iterator_to_array($this->pgnParser->parse($lines, false, true));

        $expected = [
            [
                'header' => [
                    'Event'           => 'EventVal',
                    'Site'            => 'SiteVal',
                    'White'           => 'WhiteVal',
                    'Black'           => 'BlackVal',
                    'Result'          => '1-0',
                    'UTCDate'         => '2020.01.01',
                    'UTCTime'         => '00:00:01',
                    'WhiteElo'        => '123',
                    'BlackElo'        => '456',
                    'WhiteRatingDiff' => '+78',
                    'BlackRatingDiff' => '-90',
                    'ECO'             => 'B10',
                    'Opening'         => 'OpeningVal',
                    'TimeControl'     => '300+8',
                    'Termination'     => 'Normal',
                ],
                'title' => 'Game: EventVal, WhiteVal vs. BlackVal (2020.01.01 00:00:01)',
                'plies' => [],
                'result' => [],
            ]
        ];


        $this->assertEquals($expected, $actual);
    }
}
