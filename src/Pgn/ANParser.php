<?php
namespace Pjio\Chessboard\Pgn;

use Exception;
use Pjio\Chessboard\AbstractPlayer;
use Pjio\Chessboard\Black;
use Pjio\Chessboard\Board\Chessboard;
use Pjio\Chessboard\Board\ChessboardFactory;
use Pjio\Chessboard\Board\ChessboardSerializer;
use Pjio\Chessboard\Board\Square;
use Pjio\Chessboard\Exception\ANParserException;
use Pjio\Chessboard\Exception\InvalidCoordinatesException;
use Pjio\Chessboard\Move as ChessboardMove;
use Pjio\Chessboard\MoveValidator;
use Pjio\Chessboard\Pgn\DecodedPly;
use Pjio\Chessboard\Piece\AbstractPiece;
use Pjio\Chessboard\Piece\Bishop;
use Pjio\Chessboard\Piece\King;
use Pjio\Chessboard\Piece\Knight;
use Pjio\Chessboard\Piece\Pawn;
use Pjio\Chessboard\Piece\Queen;
use Pjio\Chessboard\Piece\Rook;
use Pjio\Chessboard\White;

/**
 * ANParser parses the Algebraic Notation (AN) for chess games
 * Most of it's complexity is due to the fact, that the AN usually depends on the state
 * and the rules of the game to fully determine which piece moves.
 */
class ANParser
{
    private const RX_MOVE = '/(?<move>\d+\.) +(?<white>[^ ]+) +(?:(?<black>[^ ]+) +)?(?<result>1\/2-1\/2|[01]-[01]|\*)?/';

    private const RX_PLY = '/^(?:' .
        '(?<piece>[KQNRB])?' .
        '(?<fromFile>[a-h])??' .
        '(?<fromRank>[1-8])??' .
        '(?<capture>[x:])?' .
        '(?<toFile>[a-h])' .
        '(?<toRank>[1-8])' .
        '(?<promote>=[QNRB])?' .
        '(?<en_passant>e\.p\.)?' .
        '|(?<kingside>[0O]-[0O])' .
        '|(?<queenside>[0O]-[0O]-[0O])' .
        ')' .
        '(?<checked>\+)?' .
        '(?<checkmate>#|\+\+)?' .
        '(?<nag>[?!]{1,2})?' .
        '$/';

    private const PROMOTION_MAP = [
        '=Q' => 'queen',
        '=N' => 'knight',
        '=R' => 'rook',
        '=B' => 'bishop',
    ];

    private ChessboardFactory $chessboardFactory;
    private Chessboard $chessboard;
    private White $white;
    private Black $black;
    private MoveValidator $moveValidator;
    private ChessboardSerializer $chessboardSerializer;

    public function __construct()
    {
        $this->chessboardFactory    = new ChessboardFactory();
        $this->moveValidator        = new MoveValidator();
        $this->chessboardSerializer = new ChessboardSerializer();
    }

    public function parse(string $encodedGame, bool $printSteps = false, bool $asString = false): array
    {
        $this->init();

        list($encodedPlyList, $encodedResult) = $this->splitEncodedGame($encodedGame);
        $decodedResult = new DecodedResult($encodedResult);

        $decodedPlyList = [];
        for ($plyIndex = 0; $plyIndex < count($encodedPlyList); $plyIndex++) {
            $player = $plyIndex % 2 === 0 ? $this->white : $this->black;
            $moveNo = $this->calcMoveNo($player, $plyIndex);

            if ($printSteps) {
                printf('%s move #%d%s', $player, $moveNo, PHP_EOL);
            }

            $decodedPly = $this->parsePly($encodedPlyList[$plyIndex], $player, $moveNo, $printSteps);
            $decodedPlyList[] = $asString ? $decodedPly->readableString() : $decodedPly;
        }

        if ($printSteps) {
            printf('Result: %s%s%s', $decodedResult, PHP_EOL, PHP_EOL);
            $this->printChessboard();
        }

        return [$decodedPlyList, $decodedResult];
    }

    private function init(): void
    {
        $this->white      = new White();
        $this->black      = new Black();
        $this->chessboard = $this->chessboardFactory->createNewChessboard($this->white, $this->black); # asdf
    }

    private function calcMoveNo(AbstractPlayer $player, int $plyIndex): int
    {
        return ($player === $this->black) ? ($plyIndex - $plyIndex % 2) / 2 + 1 : $plyIndex / 2 + 1;
    }

    private function splitEncodedGame(string $encodedGame): array
    {
        $matches = [];
        if (!preg_match_all(self::RX_MOVE, $encodedGame, $matches)) {
            throw new ANParserException(sprintf('Can\'t parse encodedGame: %s', $encodedGame));
        }

        $encodedPlyList = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $expectedMove = sprintf('%d.', $i + 1);

            $move     = $matches['move'][$i];
            $whitePly = $matches['white'][$i];
            $blackPly = $matches['black'][$i];
            $result   = $matches['result'][$i];

            if ($move !== $expectedMove) {
                throw new ANParserException(sprintf('Invalid move: Expected "%d." but got "%s"', $expectedMove, $move), [$encodedGame, $matches]);
            }

            if (empty($whitePly)) {
                throw new ANParserException('Every move must contain a white ply!', [$encodedGame, $matches]);
            }

            if (!empty($result)) {
                if ($i + 1 !== count($matches[0])) {
                    throw new ANParserException('The result must be the last element and within the last move!', [$encodedGame, $matches]);
                }
 
                $encodedResult = $result;
            } elseif (empty($blackPly)) {
                throw new ANParserException('Every move except for the last (and if white wins) must contain a black ply!', [$encodedGame, $matches]);
            }

            $encodedPlyList[] = $whitePly;

            if (!empty($blackPly)) {
                $encodedPlyList[] = $blackPly;
            }
        }

        if (empty($encodedResult)) {
            throw new ANParserException('No result of game found!', [$encodedGame, $matches]);
        }

        return [$encodedPlyList, $encodedResult];
    }

    private function parsePly(string $encodedPly, AbstractPlayer $player, int $moveNo, bool $printSteps): DecodedPly
    {
        $matches = [];
        if (!preg_match(self::RX_PLY, $encodedPly, $matches)) {
            throw new ANParserException(sprintf('Unable to parse encodedPly: "%s"', $encodedPly));
        }

        if (!array_key_exists($matches['piece'] ?? '', DecodedPly::PIECE_FQCN)) {
            throw new ANParserException(sprintf('Invalid piece identifier: "%s"', $matches['piece']));
        }

        $decodedPly = new DecodedPly($matches, $encodedPly, $player, $moveNo);

        $chessboardPly = $this->applyOnChessboard($decodedPly, $player);

        if ($printSteps) {
            printf('  %s => %s%s%s', $encodedPly, $chessboardPly, PHP_EOL, PHP_EOL);
            $this->printChessboard();
        }

        return $decodedPly;
    }

    private function applyOnChessboard(DecodedPly $decodedPly, AbstractPlayer $player): ChessboardMove
    {
        try {
            if ($decodedPly->isCastlingKingSide() || $decodedPly->isCastlingQueenSide()) {
                $chessboardPly = $this->applyCastling($decodedPly);
            } else {
                $chessboardPly = $this->applyReguarMove($decodedPly);
            }
        } catch (ANParserException|InvalidCoordinatesException $e) {
            throw new ANParserException(sprintf('%s (encodedPly: %s)', $e->getMessage(), $decodedPly->getEncodedPly()));
        }

        if (!$this->moveValidator->isValidMove($chessboardPly, $this->chessboard)) {
            throw new ANParserException(sprintf('Move would be illegal: %s, %s', $chessboardPly, $decodedPly->debugString()));
        }

        try {
            $this->chessboard->move($chessboardPly);
        } catch (Exception $e) {
            throw new ANParserException(sprintf('Parsed move could not be reproduced: %s, %s', $e->getMessage(), $encodedPly));
        }

        return $chessboardPly;
    }

    private function applyCastling(DecodedPly $decodedPly): ChessboardMove
    {
        $rank = $decodedPly->getPlayer() == $this->white ? Square::RANK_1 : Square::RANK_8;

        if ($decodedPly->isCastlingKingSide()) {
            $toFile = Square::FILE_G;
        } elseif ($decodedPly->isCastlingQueenSide()) {
            $toFile = Square::FILE_C;
        }

        $from = new Square(Square::FILE_E, $rank);
        $to   = new Square($toFile, $rank);

        return new ChessboardMove($decodedPly->getPlayer(), $from, $to, true);
    }

    private function applyReguarMove(DecodedPly $decodedPly): ChessboardMove
    {
        if (!$decodedPly->hasTargetPosition()) {
            throw new ANParserException('Target position not found');
        }

        if ($decodedPly->hasMissingFrom()) {
            $this->findMissingFrom($decodedPly);
        }

        $encodedPromotion = $decodedPly->getpromote();
        if (!empty($encodedPromotion) && !array_key_exists($encodedPromotion, self::PROMOTION_MAP)) {
            throw new ANParserException(sprintf('Unknown value for promotion: %s', $encodedPromotion));
        }

        $from      = Square::fromString($decodedPly->getFromFile() . $decodedPly->getFromRank());
        $to        = Square::fromString($decodedPly->getToFile() . $decodedPly->getToRank());
        $promotion = empty($encodedPromotion) ? '' : self::PROMOTION_MAP[$encodedPromotion];

        return new ChessboardMove($decodedPly->getPlayer(), $from, $to, false, $promotion);
    }

    private function findMissingFrom(DecodedPly $decodedPly): void
    {
        $pieceFqcn = $decodedPly->getPieceFqcn();

        /** @var AbstractPiece[] $pieceList */
        $pieceList = $this->chessboard->findPieces($decodedPly->getPlayer(), $pieceFqcn);

        $pieceList = $this->filterByFileOrRank($decodedPly, $pieceList);

        $distinctPiece = null;

        $pieceList = array_values($pieceList);
        if (count($pieceList) === 1) {
            $distinctPiece = $pieceList[0];
        } else {
            $pieceList = $this->filterByValidMove($pieceList, $decodedPly);

            if (count($pieceList) === 1) {
                $distinctPiece = $pieceList[0];
            }
        }

        if ($distinctPiece === null) {
            throw new ANParserException(sprintf('No distinct piece to move found: %s', $decodedPly->debugString()));
        }

        $decodedPly->setFromFile($distinctPiece->getSquare()->getFile(true));
        $decodedPly->setFromRank($distinctPiece->getSquare()->getRank(true));
    }

    private function printChessboard(): void
    {
        printf('%s%s%s', $this->chessboardSerializer->serialize($this->chessboard, true), PHP_EOL, PHP_EOL);
    }

    private function filterByFileOrRank(DecodedPly $decodedPly, array $pieceList): array
    {
        $filterByFile = !empty($decodedPly->getFromFile()) ? strtoupper($decodedPly->getFromFile()) : null;
        $filterByRank = !empty($decodedPly->getFromRank()) ? (int) $decodedPly->getFromRank() : null;

        if ($filterByFile !== null || $filterByRank !== null) {
            $pieceList = array_filter(
                $pieceList,
                fn(AbstractPiece $piece): bool =>
                $filterByRank !== null && $piece->getSquare()->getRank(true) === $filterByRank
                || $filterByFile !== null && $piece->getSquare()->getFile(true) === $filterByFile
            );
        }

        return $pieceList;
    }

    private function filterByValidMove(array $pieceList, DecodedPly $decodedPly): array
    {
        $moveablePieces = [];

        foreach ($pieceList as $piece) {
            $from          = $piece->getSquare();
            $to            = Square::fromString($decodedPly->getToFile() . $decodedPly->getToRank());
            $chessboardPly = new ChessboardMove($decodedPly->getPlayer(), $from, $to);

            if ($this->moveValidator->isValidMove($chessboardPly, $this->chessboard)) {
                $moveablePieces[] = $piece;
            }
        }

        return $moveablePieces;
    }
}
