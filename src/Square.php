<?php
namespace Pjio\Chessboard;

use RuntimeException;

/**
 *       8
 *       7
 *       6
 *       5
 * ranks 4
 *       3
 *       2
 *       1
 *        a b c d e f g h
 *
 *             files
 */
class Square
{
    public const RANK_1 = 0;
    public const RANK_2 = 1;
    public const RANK_3 = 2;
    public const RANK_4 = 3;
    public const RANK_5 = 4;
    public const RANK_6 = 5;
    public const RANK_7 = 6;
    public const RANK_8 = 7;

    public const FILE_A = 0;
    public const FILE_B = 1;
    public const FILE_C = 2;
    public const FILE_D = 3;
    public const FILE_E = 4;
    public const FILE_F = 5;
    public const FILE_G = 6;
    public const FILE_H = 7;

    private int $file;
    private int $rank;

    public function __construct(int $file, int $rank)
    {
        if ($file < 0 || $file > 7 || $rank < 0 || $rank > 7) {
            throw new RuntimeException(sprintf('Invalid Coordinates: %d, %d', $file, $rank));
        }

        $this->file = $file;
        $this->rank = $rank;
    }

    public function getFile(): int
    {
        return $this->file;
    }

    public function getRank(): int
    {
        return $this->rank;
    }
}
