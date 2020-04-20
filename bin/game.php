#!/usr/bin/env php
<?php

use Pjio\Chessboard\Cli\Client;

require dirname(__DIR__) . '/vendor/autoload.php';

$client = new Client();
$client->run();
