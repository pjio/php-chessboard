<?php
namespace Pjio\Chessboard\Board;

use Pjio\Chessboard\Exception\InvalidCoordinatesException;

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

    private const READABLE_RANK = [
        0 => '1',
        1 => '2',
        2 => '3',
        3 => '4',
        4 => '5',
        5 => '6',
        6 => '7',
        7 => '8',
    ];

    private const READABLE_FILE = [
        0 => 'A',
        1 => 'B',
        2 => 'C',
        3 => 'D',
        4 => 'E',
        5 => 'F',
        6 => 'G',
        7 => 'H',
    ];

    private int $file;
    private int $rank;

    public function __construct(int $file, int $rank)
    {
        if ($file < 0 || $file > 7 || $rank < 0 || $rank > 7) {
            throw new InvalidCoordinatesException(
                sprintf('Invalid Coordinates: %d, %d', $file, $rank)
            );
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

    public function __toString()
    {
        return sprintf('%s%s', self::READABLE_FILE[$this->file], self::READABLE_RANK[$this->rank]);
    }
}
