<?php

namespace Porthd\Avalanchesort\Sort;

use PHPUnit\Framework\TestCase;
use Porthd\Avalanchesort\Service\GenerateTestArrayTestService;
use Porthd\Avalanchesort\Storage\Additional\MapperList;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayDataCompare;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayIndexDataRange;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayList;
use Porthd\Avalanchesort\Sort\AvalancheSort;
use Porthd\Avalanchesort\Sort\QuickSort;

class AvalancheSortArrayTest extends TestCase
{
    protected const TEST_KEY = 'test';

    /**
     * @var \Porthd\Avalanchesort\Sort\AvalancheSort
     */
    protected $avalanchesort;

    /**
     * @var QuickSort
     */
    protected $quickSort;

    /**
     * @var ArrayDataCompare
     */
    protected $compare;

    public function setUp(): void
    {
        $this->avalanchesort = new AvalancheSort(ArrayIndexDataRange::class);
        $this->quickSort = new QuickSort(ArrayIndexDataRange::class);
        $this->compare = new ArrayDataCompare(self::TEST_KEY);
    }

    public function tearDown(): void
    {
        unset($this->avalanchesort);
        unset($this->quickSort);
        unset($this->compare);

    }

    public function showCountingResult($sortProblemMsg, $avalancheSortCounts, $quickSortCounts)
    {

        $name = array_column($avalancheSortCounts, MapperList::KEY_NAME);
        $flag = array_column($avalancheSortCounts, MapperList::KEY_FLAG);
        $infoLength = 0;
        foreach ($name as $item) {
            $infoLength = (($infoLength < strlen($item)) ?
                strlen($item) :
                $infoLength
            );
        }
        $infoLength = (int)$infoLength;
        $quickCounts = array_column($quickSortCounts, MapperList::KEY_COUNT);
        $avalancheCounts = array_column($avalancheSortCounts, MapperList::KEY_COUNT);

        $maxQuick = abs(max($quickCounts));
        $maxAvalanche = abs(max($avalancheCounts));
        $digitsInNumber = (int)(($maxQuick > $maxAvalanche) ?
            (ceil(log10($maxQuick + 1)) + 2) :
            (ceil(log10($maxAvalanche + 1)) + 2)
        );
        fwrite(STDERR, print_r($sortProblemMsg, TRUE));
        $result = '';
        $result .= sprintf("%3s.", '');
        $result .= sprintf("%" . $infoLength . "s ", 'type');
        $result .= sprintf("%" . $digitsInNumber . "s ", 'QS');
        $result .= sprintf("%" . $digitsInNumber . "s ", 'AS');
        $result .= "\n";
        fwrite(STDERR, print_r($result, TRUE));
        foreach ($name as $key => $value) {
            if ($flag[$key]) {
                $result = '';
                $result .= sprintf("%3s. ", $key);
                $result .= sprintf("%" . $infoLength . "s ", $value);
                $result .= sprintf("%" . $digitsInNumber . "s ", $quickCounts[$key]);
                $result .= sprintf("%" . $digitsInNumber . "s ", $avalancheCounts[$key]);
                $result .= "\n";
                fwrite(STDERR, print_r($result, TRUE));
            }
        }
        fwrite(STDERR, "A = avalanceSort, Q = quicksort\n\n", TRUE);
        fwrite(STDERR, "=========\n\n", TRUE);
    }

    /**
     * @return array[]
     */
    public function dataProviderTestStartSortMethodsGivenRandomFilledArrayThenSortIt()
    {
        foreach ([
//                     [20, true, 3.2, '20 elements, normal randomisiert'],
//                     [100, false, 3.2, '100 elements, normal randomized'],
//                     [100, true, 0.1, '100 elements, easy randomized'],
//                     [100, true, 1.2, '100 elements, simple randomized'],
//                     [100, true, 3.2, '100 elements, normal randomized'],
//                     [100, true, 10.2, '100 elements, heavy randomized'],
//                     [500, true, 3.2, '500 elements, normal randomized'],
                     [2000, true, 5.2, '2000 elements, big randomized'],
                     [2000, true, 1.0, '2000 elements, simple randomized'],
                     [2000, true, 0.5, '2000 elements, easy randomized'],
                     [200, true, 5.2, '200 elements, big randomized'],
                     [200, true, 1.0, '200 elements, simple randomized'],
                     [200, true, 0.5, '200 elements, easy randomized'],
                     [200, false, 0.01, '200 elements, sorted (because of recursion of Quicksort and nesting)'],
//                     [10000, true, 3.2],
                 ]
                 as $myKey => $myParam
        ) {
            /** @var GenerateTestArrayTestService $generateTestArrayTestService */
            $generateTestArrayTestService = new GenerateTestArrayTestService(self::TEST_KEY);
            $sortedArray = $generateTestArrayTestService->generateListSortedSimpleArray($myParam[0]);
            if ($myParam[1]) {

                $distoredArray = $generateTestArrayTestService->shuffleArrayForSorting(
                    $sortedArray,
                    0.01,
                    0.001,
                    $myParam[2]
                );
            } else {
                $distoredArray = $generateTestArrayTestService->arragneResortArray($sortedArray);
            }
            $firstKeyDistorded = array_key_first($distoredArray);
            $firstKeySorted = array_key_first($sortedArray);
            $n = ($myParam[0]);
            $nLbN =round($n * log($n, 2),2);
            $nSqr = $n * $n;

            $result[] = [
                [
                    'main' => $myKey . '. Test an empty distorted array ',
                    'mapper' => 'operations in avalanche-sort an quicksort ' . "\n" .
                        $myParam[3]."\n".
                        '(n = ' . $n . ', n*lb(n) = ' . $nLbN . ', n^2 = ' . $nSqr . ")\n ",
                ],

                [ // Expects
                    'testDiffer' => $myParam[1],
                    'data' => $sortedArray,
                    'first' => $firstKeyDistorded,
                    'firstSort' => $firstKeySorted,

                ],
                [ // params
                    'data' => $distoredArray,
                    'first' => array_key_first($distoredArray),
                ],
            ];

        }
        return $result;
    }


    /**
     * @param array $message
     * @param array $expects
     * @param array $params
     *
     * @dataProvider dataProviderTestStartSortMethodsGivenRandomFilledArrayThenSortIt
     */
    public function testStartSortMethodsGivenRandomFilledArrayThenSortIt(array $message, array $expects, array $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'no data in the provider for the testing of `' .
                'testStartAvalancheSort' . '`');
        } else {
            $expectSorted = array_column($expects['data'], self::TEST_KEY);
            $distorted = $params['data'];
            $resultDistorted = array_column($distorted, self::TEST_KEY);
            $compareFunc = new ArrayDataCompare(self::TEST_KEY);
            if ($expects['testDiffer']) {

                $this->assertNotEquals(
                    $expectSorted,
                    $resultDistorted,
                    'arrays must differ before sorting'
                );
            } else {
                $this->assertEquals(
                    $expectSorted,
                    $resultDistorted,
                    'arrays must NOT differ before sorting'
                );

            }
            $this->assertEquals(
                $expects['first'],
                $expects['firstSort'],
                'start-index for distored and original sorted array are euqal'
            );

            // Avalanchesort
//            $dataList = new ArrayList();
            $dataList = new MapperList(ArrayList::class);
            $dataList->setDataList($distorted, $compareFunc);
            $rangeResult = $this->avalanchesort->startAvalancheSort($dataList);
            $resultRaw = $dataList->getDataList();
            $firstResult = array_key_first($resultRaw);
            $resultTest = array_column($resultRaw, self::TEST_KEY);
            $mapperResult = $dataList->getCountsResult();
            $this->assertEquals(
                $expects['first'],
                $rangeResult->getStart(),
                'start-index for index in restorted Range and in original sorted array are equal'
            );
            $this->assertEquals(
                $expects['first'],
                $firstResult,
                'detect by result-array: start-index for index in restorted Range and in original sorted array are equal'
            );
            $this->assertEquals(
                $expectSorted,
                $resultTest,
                'test AvalancheSort: ' . $message['main']
            );

            // Quicksort
//            $dataListQ = new ArrayList();
            $dataListQ = new MapperList(ArrayList::class);

            $dataListQ->setDataList($distorted, $compareFunc);

            $rangeResultQ = $this->quickSort->qsortStart($dataListQ);
            $resultRawQ = $dataListQ->getDataList();
            $firstResultQ = array_key_first($resultRawQ);
            $resultTestQ = array_column($resultRawQ, self::TEST_KEY);
            $mapperResultQ = $dataListQ->getCountsResult();

            $this->assertEquals(
                $expects['first'],
                $rangeResultQ->getStart(),
                'start-index for index in restorted Range and in original sorted array are euqal'
            );
            $this->assertEquals(
                $expects['first'],
                $firstResultQ,
                'detect by result-array: start-index for index in restorted Range and in original sorted array are equal'
            );
            $this->assertEquals(
                $expectSorted,
                $resultTestQ,
                'test QuickSort: ' . $message['main']
            );

            $mapperResult = $dataList->getCountsResult();
            $this->showCountingResult($message['mapper'], $mapperResult, $mapperResultQ);
        }
    }

    /**
     * @return array[]
     */
    public function dataProviderTestStartAvalancheSortGiveAndSetDataListDoNotChangeResultIfArrayIsGiven()
    {
        /** @var GenerateTestArrayTestService $generateTestArrayTestService */
        $generateTestArrayTestService = new GenerateTestArrayTestService(self::TEST_KEY);
        $singleElementArray = $generateTestArrayTestService->generateListSingleElementArray();
        $doubleElementArray = $generateTestArrayTestService->generateListTupleElementsArray(2);
        $antiDoubleElementArray = $generateTestArrayTestService->generateListAntisortedTupelElementsArray(2);
        $tripleElementArray = $generateTestArrayTestService->generateListTupleElementsArray(3);
        $antiTripleElementArray = $generateTestArrayTestService->generateListAntisortedTupelElementsArray(3);
        $decleElementArray = $generateTestArrayTestService->generateListTupleElementsArray(10);
        $antiDecleElementArray = $generateTestArrayTestService->generateListAntisortedTupelElementsArray(10);
        return [
            [
                '1. Test an sorted  array with one element ',
                [
                    'data' => $singleElementArray,
                ],
                [
                    'data' => $singleElementArray,
                ],
            ],
            [
                '2.a. Test an sorted array with two elements',
                [
                    'data' => $doubleElementArray,
                ],
                [
                    'data' => $doubleElementArray,
                ],
            ],
            [
                '2.b. Test an anti-sorted array with two elements',
                [
                    'data' => $antiDoubleElementArray,
                ],
                [
                    'data' => $antiDoubleElementArray,
                ],
            ],
            [
                '3.a. Test an sorted array with three elements',
                [
                    'data' => $tripleElementArray,
                ],
                [
                    'data' => $tripleElementArray,
                ],
            ],
            [
                '3.b. Test an anti-sorted array with three elements',
                [
                    'data' => $antiTripleElementArray,
                ],
                [
                    'data' => $antiTripleElementArray,
                ],
            ],
            [
                '4.a. Test an sorted array with ten elements',
                [
                    'data' => $decleElementArray,
                ],
                [
                    'data' => $decleElementArray,
                ],
            ],
            [
                '4.b. Test an anti-sorted array with ten elements',
                [
                    'data' => $antiDecleElementArray,
                ],
                [
                    'data' => $antiDecleElementArray,
                ],
            ],
        ];
    }


    public function testWas()
    {
        $this->assertEquals(
            1,
            1,
            'immer wahr / ever true'
        );
    }

}
