<?php
namespace Pjio\Chessboard\Board;

class Chessboard
{
    private array $pieces;

    public function __construct(array $pieces)
    {
        $this->pieces = $pieces;
    }

    public function getPiecesIterator(): iterable
    {
        return $this->pieces;
    }
}
