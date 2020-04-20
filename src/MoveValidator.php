<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\MoveValidatorInterface;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Move;
use Pjio\Chessboard\Pieces;
use Pjio\Chessboard\Rule;
use RuntimeException;

class MoveValidator implements MoveValidatorInterface
{
    private const RULES = [
        Pieces\Bishop::class => Rule\BishopRule::class,
        Pieces\King::class   => Rule\KingRule::class,
        Pieces\Knight::class => Rule\KnightRule::class,
        Pieces\Pawn::class   => Rule\PawnRule::class,
        Pieces\Queen::class  => Rule\QueenRule::class,
        Pieces\Rook::class   => Rule\RookRule::class,
    ];

    public function isValidMove(Move $move, Chessboard $chessboard): bool
    {
        $piece = $chessboard->getPieceBySquare($move->getFrom());

        if ($piece === null) {
            throw new RuntimeException('No piece at "from" position: %s', $move->getFrom());
        }

        $pieceFQCN = get_class($piece);

        if (!isset(self::RULES[$pieceFQCN])) {
            throw new RuntimeException(
                sprintf('No rule for piece: %s', $piece)
            );
        }

        $ruleFQCN = self::RULES[$pieceFQCN];

        /** @var MoveValidatorInterface $rule */
        $rule = new $ruleFQCN();

        return $rule->isValidMove($move, $chessboard);
    }
}
