#!/usr/bin/env php
<?php

$composerSuccess;
system('composer dump-autoload --optimize --no-dev 2> /dev/null', $composerSuccess);

if ($composerSuccess !== 0) {
    echo 'composer dump-autoload failed!';
    exit;
}

$alias = 'chess.phar';
$filePattern = '#^./src/.*\.php$|^./vendor/autoload.php$|^./vendor/composer/.*$#';
$outfile = 'build/chess.phar';

$stub = <<<EOF
<?php
require('phar://chess.phar/vendor/autoload.php');
use Pjio\Chessboard\Cli\Client;
\$client = new Client();
\$client->run();
__HALT_COMPILER();
EOF;

if (file_exists($outfile)) {
    unlink($outfile);
}

$phar = new Phar($outfile, 0, $alias);
$phar->startBuffering();
$phar['stub.php'] = $stub;
$phar->setStub("#!/usr/bin/env php\n" . $phar->createDefaultStub('stub.php'));
$phar->buildFromDirectory('.', $filePattern);
$phar->stopBuffering();

chmod($outfile, 0755);
