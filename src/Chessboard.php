<?php
namespace Pjio\Chessboard;

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
