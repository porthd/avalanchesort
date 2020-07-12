<?php
namespace Porth\Avalanchesort\Storage\ArrayType;

use PHPUnit\Framework\TestCase;
use Porth\Avalanchesort\Storage\ArrayType\ArrayDataCompare;
use Porth\Avalanchesort\Storage\ArrayType\ArrayList;

class ArrayListTest extends TestCase
{

    public function provider(): array
    {
        return array(
            array(0, 1, 1),
            array(1, 2, 3),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testSum(int $first, int $second, int $expected)
    {
        $this->assertEquals($expected, ($first + $second), 'okay');
    }

    /**
     * @return array[]
     */
    public function dataProviderstartAvalancheSortGiveAndSetDataListDoNotChangeResultIfArrayIsGiven()
    {
        return [
            [
                '1. Given something If something happens Then special result ',
                [
                    'expectsOne' => [
                        'something' => 'value',
                    ],
                ],
                [
                    'paramsOne' => [
                        'something' => 'value',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $message
     * @param $expects
     * @param $params
     *
     * @dataProvider dataProviderstartAvalancheSortGiveAndSetDataListDoNotChangeResultIfArrayIsGiven
     * @test
     */
    public function startAvalancheSortGivenSomethingIfSomegeneralThenGeneralResult(string $message, array $expects, array $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'no data in the provider for the testing of `' .
                'startAvalancheSort' . '`');
        } else {
            // $result = $this->subject->myFuntion(...$params);
            $result = $params['paramsOne'];
            $this->assertEquals(
                $expects['expectsOne'],
                $result,
                'test startAvalancheSort: ' . $message
            );
        }
    }

}
