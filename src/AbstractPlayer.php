<?php
namespace Pjio\Chessboard;

/**
 * Player is the base class for White and Black
 * White and Black represent the players for one set of pieces
 */
abstract class AbstractPlayer
{
    abstract public function getName(): string;

    public function __toString()
    {
        return $this->getName();
    }
}
