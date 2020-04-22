<?php
namespace Pjio\Chessboard;

use Pjio\Chessboard\Board\Chessboard;
use RuntimeException;
use Pjio\Chessboard\Piece\Bishop;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\Piece\Knight;
use Pjio\Chessboard\Piece\Pawn;
use Pjio\Chessboard\Piece\Rook;
use Pjio\Chessboard\Piece\Queen;

class MoveValidator implements MoveValidatorInterface
{
    private const RULES = [
        Piece\Bishop::class => Rule\BishopRule::class,
        Piece\King::class   => Rule\KingRule::class,
        Piece\Knight::class => Rule\KnightRule::class,
        Piece\Pawn::class   => Rule\PawnRule::class,
        Piece\Queen::class  => Rule\QueenRule::class,
        Piece\Rook::class   => Rule\RookRule::class,
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
