<?php

namespace Porth\Avalanchesort\Sort;

use PHPUnit\Framework\TestCase;
use Porth\Avalanchesort\Service\GenerateTestArrayTestService;
use Porth\Avalanchesort\Storage\Additional\MapperList;
use Porth\Avalanchesort\Storage\ArrayType\ArrayDataCompare;
use Porth\Avalanchesort\Storage\ArrayType\ArrayIndexDataRange;
use Porth\Avalanchesort\Storage\ArrayType\ArrayList;
use Porth\Avalanchesort\Sort\AvalancheSort;
use Porth\Avalanchesort\Sort\QuickSort;

class AvalancheSortArrayTest extends TestCase
{
    protected const TEST_KEY = 'test';

    /**
     * @var \Porth\Avalanchesort\Sort\AvalancheSort
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
        $quickCounts = array_column($quickSortCounts, MapperList::KEY_COUNT);
        $avalancheCounts = array_column($avalancheSortCounts, MapperList::KEY_COUNT);

        $maxQuick = abs(max($quickCounts));
        $maxAvalanche = abs(max($avalancheCounts));
        $digitsInNumber = (($maxQuick > $maxAvalanche) ?
            (ceil(log10($maxQuick + 1)) + 2) :
            (ceil(log10($maxAvalanche + 1)) + 2)
        );
        fwrite(STDERR, print_r($sortProblemMsg, TRUE));
        $result = '';
        $result .= printf("%-3s.", '');
        $result .= printf("%-" . $infoLength . "s.", 'type');
        $result .= printf("%-" . $digitsInNumber . "s.", 'AS');
        $result .= printf("%-" . $digitsInNumber . "s.", 'QS');
        $result .= "\n";
        fwrite(STDERR, print_r($result, TRUE));
        foreach ($name as $key => $value) {
            if ($flag[$key]) {
                $result = '';
                $result .= printf("%-3s.", $key);
                $result .= printf("%-" . $infoLength . "s.", $value);
                $result .= printf("%-" . $digitsInNumber . "s.", $quickCounts[$key]);
                $result .= printf("%-" . $digitsInNumber . "s.", $avalancheCounts[$key]);
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
                     [20, true, 3.2],
//                     [100, false, 3.2],
//                     [100, true, 0.1],
//                     [100, true, 1.2],
//                     [100, true, 3.2],
//                     [100, true, 10.2],
//                     [500, true, 3.2],
//                     [2000, true, 3.2],
//                     [50000, true, 3.2],
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
            $nLbN = $n * log($n, 2);
            $nSqr = $n * $n;
            $result[] = [
                [
                    'main' => $myKey . '. Test an empty distorted array ',
                    'mapper' => 'operations in avalanche-sort an quicksort ' . "\n" .
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
            // $result = $this->avalanchesort->myFuntion(...$params);
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
//
//            // Avalanchesort
////            $dataList = new ArrayList();
//            $dataList = new MapperList(ArrayList::class);
//            $dataList->setDataList($distorted, $compareFunc);
//            $rangeResult = $this->avalanchesort->startAvalancheSort($dataList);
//            $resultRaw = $dataList->getDataList();
//            $firstResult = array_key_first($resultRaw);
//            $resultTest = array_column($resultRaw, self::TEST_KEY);
//            $mapperResult = $dataList->getCountsResult();
//            $this->assertEquals(
//                $expects['first'],
//                $rangeResult->getStart(),
//                'start-index for index in restorted Range and in original sorted array are equal'
//            );
//            $this->assertEquals(
//                $expects['first'],
//                $firstResult,
//                'detect by result-array: start-index for index in restorted Range and in original sorted array are equal'
//            );
//            $this->assertEquals(
//                $expectSorted,
//                $resultTest,
//                'test AvalancheSort: ' . $message['main']
//            );
//
            // Quicksort
            $dataListQ = new ArrayList();
//            $dataListQ = new MapperList(ArrayList::class);

            $dataListQ->setDataList($distorted, $compareFunc);

            $rangeResultQ = $this->quickSort->qsortStart($dataListQ);
            $resultRawQ = $dataListQ->getDataList();
            $firstResultQ = array_key_first($resultRawQ);
            $resultTestQ = array_column($resultRawQ, self::TEST_KEY);
//            $mapperResultQ = $dataListQ->getCountsResult();

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
//
//            $mapperResult = $dataList->getCountsResult();
//            $this->showCountingResult($message['mapper'], $mapperResult, $mapperResultQ);
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