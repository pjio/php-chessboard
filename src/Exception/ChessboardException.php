<?php
namespace Pjio\Chessboard\Exception;

use RuntimeException;

class ChessboardException extends RuntimeException
{
    public function __construct(string $message, $extra = null)
    {
        if ($extra !== null) {
            $message = sprintf('%s%s%s', $message, PHP_EOL, json_encode($extra));
        }

        parent::__construct($message);
    }
}
