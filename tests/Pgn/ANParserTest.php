<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Pjio\Chessboard\Pgn\ANParser;
use Pjio\Chessboard\Pgn\DecodedResult;

/**
 * To manually test it with every thinkable move, download one of those files
 * from https://database.lichess.org/ which contain decoded ~90GB of chess games.
 */
class ANParserTest extends TestCase
{
    private ANParser $algebraicNotationParser;
    private PgnParser $pgnParser;

    public function setUp(): void
    {
        $this->algebraicNotationParser = new ANParser();
    }

    /**
     * @dataProvider provideEncodedGames
     */
    public function testParse(string $encodedGame, array $expectedMoveList, DecodedResult $expectedResult)
    {
        list ($actualMoveList, $actualResult) = $this->algebraicNotationParser->parse($encodedGame, false, true);

        $this->assertEquals($expectedMoveList, $actualMoveList);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function provideEncodedGames(): array
    {
        return [
            'draw' => [
                'encodedGame' => '1. a4 1/2-1/2',
                'expectedMoveList' => [
                    '1. White moves Pawn from A2 to A4',
                ],
                'expectedResult' => new DecodedResult(DecodedResult::DRAW),
            ],
            'white_wins' => [
                'encodedGame' => '1. a4 1-0',
                'expectedMoveList' => [
                    '1. White moves Pawn from A2 to A4',
                ],
                'expectedResult' => new DecodedResult(DecodedResult::WHITE_WINS),
            ],
            'black_wins' => [
                'encodedGame' => '1. a4 h5 0-1',
                'expectedMoveList' => [
                    '1. White moves Pawn from A2 to A4',
                    '1. Black moves Pawn from H7 to H5',
                ],
                'expectedResult' => new DecodedResult(DecodedResult::BLACK_WINS),
            ],
            'other_result' => [
                'encodedGame' => '1. a4 h5 *',
                'expectedMoveList' => [
                    '1. White moves Pawn from A2 to A4',
                    '1. Black moves Pawn from H7 to H5',
                ],
                'expectedResult' => new DecodedResult(DecodedResult::OTHER),
            ],
            'multiple_moves' => [
                'encodedGame' => '1. a4 h5 2. b4 g5 3. c4 f5 1/2-1/2',
                'expectedMoveList' => [
                    '1. White moves Pawn from A2 to A4',
                    '1. Black moves Pawn from H7 to H5',
                    '2. White moves Pawn from B2 to B4',
                    '2. Black moves Pawn from G7 to G5',
                    '3. White moves Pawn from C2 to C4',
                    '3. Black moves Pawn from F7 to F5',
                ],
                'expectedResult' => new DecodedResult(DecodedResult::DRAW),
            ],
            'long_syntax' => [
                'encodedGame' => '1. a2a4!! b7b5? 2. a4xb5!? *',
                'expectedMoveList' => [
                    '1. White moves Pawn from A2 to A4',
                    '1. Black moves Pawn from B7 to B5',
                    '2. White moves Pawn from A4 to B5',
                ],
                'expectedResult' => new DecodedResult(DecodedResult::OTHER),
            ],
            'full_game' => [
                'encodedGame' => '1. e4 c5 2. Bc4 Nc6 3. Nc3 d6 4. a3 g6 5. Ba2 Bg7 6. d3 Nf6 7. Nge2 O-O 8. O-O e5 9. f4 Nd4 '
                . '10. Nxd4 cxd4 11. Ne2 Bg4 12. fxe5 dxe5 13. Qe1 a6 14. Ng3 h5 15. Bg5 Qd6 16. Rc1 Be6 17. Bxf6 Bxa2 '
                . '18. Bxg7 Kxg7 19. b3 a5 20. Ra1 Bxb3 21. cxb3 a4 22. b4 b5 23. Rc1 Rae8 24. Rc5 Qd7 25. Qf2 h4 26. Qf6+ Kg8 '
                . '27. Qxh4 Rc8 28. Rxc8 Rxc8 29. Qg5 Rc3 30. Qxe5 Rxd3 31. Qg5 Rxa3 32. Nf5 d3 33. Nh6+ Kg7 34. Rxf7+ Qxf7 '
                . '35. Nxf7 Kxf7 36. e5 Rb3 37. e6+ Kxe6 38. Qe3+ Kd5 39. Qc5+ Ke4 40. Qxb5 Ke3 41. Qc5+ Kd2 42. Kf2 Kd1 '
                . '43. Kf1 d2 44. b5 Rb2 45. b6 Rc2 46. Qe3 Kc1 0-1',
                'expectedMoveList' => [
                    '1. White moves Pawn from E2 to E4',
                    '1. Black moves Pawn from C7 to C5',
                    '2. White moves Bishop from F1 to C4',
                    '2. Black moves Knight from B8 to C6',
                    '3. White moves Knight from B1 to C3',
                    '3. Black moves Pawn from D7 to D6',
                    '4. White moves Pawn from A2 to A3',
                    '4. Black moves Pawn from G7 to G6',
                    '5. White moves Bishop from C4 to A2',
                    '5. Black moves Bishop from F8 to G7',
                    '6. White moves Pawn from D2 to D3',
                    '6. Black moves Knight from G8 to F6',
                    '7. White moves Knight from G1 to E2',
                    '7. Black Kingside Castling',
                    '8. White Kingside Castling',
                    '8. Black moves Pawn from E7 to E5',
                    '9. White moves Pawn from F2 to F4',
                    '9. Black moves Knight from C6 to D4',
                    '10. White moves Knight from E2 to D4',
                    '10. Black moves Pawn from C5 to D4',
                    '11. White moves Knight from C3 to E2',
                    '11. Black moves Bishop from C8 to G4',
                    '12. White moves Pawn from F4 to E5',
                    '12. Black moves Pawn from D6 to E5',
                    '13. White moves Queen from D1 to E1',
                    '13. Black moves Pawn from A7 to A6',
                    '14. White moves Knight from E2 to G3',
                    '14. Black moves Pawn from H7 to H5',
                    '15. White moves Bishop from C1 to G5',
                    '15. Black moves Queen from D8 to D6',
                    '16. White moves Rook from A1 to C1',
                    '16. Black moves Bishop from G4 to E6',
                    '17. White moves Bishop from G5 to F6',
                    '17. Black moves Bishop from E6 to A2',
                    '18. White moves Bishop from F6 to G7',
                    '18. Black moves King from G8 to G7',
                    '19. White moves Pawn from B2 to B3',
                    '19. Black moves Pawn from A6 to A5',
                    '20. White moves Rook from C1 to A1',
                    '20. Black moves Bishop from A2 to B3',
                    '21. White moves Pawn from C2 to B3',
                    '21. Black moves Pawn from A5 to A4',
                    '22. White moves Pawn from B3 to B4',
                    '22. Black moves Pawn from B7 to B5',
                    '23. White moves Rook from A1 to C1',
                    '23. Black moves Rook from A8 to E8',
                    '24. White moves Rook from C1 to C5',
                    '24. Black moves Queen from D6 to D7',
                    '25. White moves Queen from E1 to F2',
                    '25. Black moves Pawn from H5 to H4',
                    '26. White moves Queen from F2 to F6',
                    '26. Black moves King from G7 to G8',
                    '27. White moves Queen from F6 to H4',
                    '27. Black moves Rook from E8 to C8',
                    '28. White moves Rook from C5 to C8',
                    '28. Black moves Rook from F8 to C8',
                    '29. White moves Queen from H4 to G5',
                    '29. Black moves Rook from C8 to C3',
                    '30. White moves Queen from G5 to E5',
                    '30. Black moves Rook from C3 to D3',
                    '31. White moves Queen from E5 to G5',
                    '31. Black moves Rook from D3 to A3',
                    '32. White moves Knight from G3 to F5',
                    '32. Black moves Pawn from D4 to D3',
                    '33. White moves Knight from F5 to H6',
                    '33. Black moves King from G8 to G7',
                    '34. White moves Rook from F1 to F7',
                    '34. Black moves Queen from D7 to F7',
                    '35. White moves Knight from H6 to F7',
                    '35. Black moves King from G7 to F7',
                    '36. White moves Pawn from E4 to E5',
                    '36. Black moves Rook from A3 to B3',
                    '37. White moves Pawn from E5 to E6',
                    '37. Black moves King from F7 to E6',
                    '38. White moves Queen from G5 to E3',
                    '38. Black moves King from E6 to D5',
                    '39. White moves Queen from E3 to C5',
                    '39. Black moves King from D5 to E4',
                    '40. White moves Queen from C5 to B5',
                    '40. Black moves King from E4 to E3',
                    '41. White moves Queen from B5 to C5',
                    '41. Black moves King from E3 to D2',
                    '42. White moves King from G1 to F2',
                    '42. Black moves King from D2 to D1',
                    '43. White moves King from F2 to F1',
                    '43. Black moves Pawn from D3 to D2',
                    '44. White moves Pawn from B4 to B5',
                    '44. Black moves Rook from B3 to B2',
                    '45. White moves Pawn from B5 to B6',
                    '45. Black moves Rook from B2 to C2',
                    '46. White moves Queen from C5 to E3',
                    '46. Black moves King from D1 to C1',
                ] ,
                'expectedResult' => new DecodedResult(DecodedResult::BLACK_WINS),
            ],
        ];
    }
}
