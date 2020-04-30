#!/usr/bin/env php
<?php

use Pjio\Chessboard\Pgn\ANParser;
use Pjio\Chessboard\Pgn\DecodedPly;
use Pjio\Chessboard\Pgn\PgnParser;

require dirname(__DIR__) . '/vendor/autoload.php';

if (count($argv) < 2) {
    echo 'Usage: pgn.php ./file' . PHP_EOL;
    exit(1);
}

$filepath = $argv[1];
if (!file_exists($filepath)) {
    echo sprintf('File not found: %s%s', $filepath, PHP_EOL);
    exit(1);
}

$printSteps = (count($argv) > 2 && $argv[2] === '--printSteps');

$fileIterator = (function (string $filepath): Generator {
    $file = fopen($filepath, 'r');

    do {
        $line = fgets($file);
        if (is_string($line)) {
            yield $line;
        }
    } while ($line !== false);

    fclose($file);
})($filepath);

$anParser  = new ANParser();
$pgnParser = new PgnParser($anParser);

try {
    $gameListIterator = $pgnParser->parse($fileIterator, $printSteps);

    foreach ($gameListIterator as $game) {
        echo sprintf('Game:   %s%s', $game['title'], PHP_EOL);
        echo sprintf('Site:   %s%s', $game['header']['Site'] ?? '-', PHP_EOL);
        echo sprintf('Result: %s%s', $game['result'], PHP_EOL);

        /** @var DecodedPly $ply */
        foreach ($game['plies'] as $ply) {
            echo $ply . PHP_EOL;
        }
    }
} catch (Exception $e) {
    fwrite(STDERR, sprintf('ERROR: %s%s', $e->getMessage(), PHP_EOL));
    exit(1);
}
