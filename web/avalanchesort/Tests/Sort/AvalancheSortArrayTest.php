<?php

namespace Porthd\Avalanchesort\Sort;

use PHPUnit\Framework\TestCase;
use Porthd\Avalanchesort\Service\GenerateTestArrayTestService;
use Porthd\Avalanchesort\Storage\Additional\MapperList;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayDataCompare;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayIndexDataRange;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayList;
use Porthd\Avalanchesort\Storage\ListType\ListDataCompare;

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
        $this->bubbleSort = new BubbleSort(ArrayIndexDataRange::class);
        $this->compare = new ArrayDataCompare(self::TEST_KEY);
    }

    public function tearDown(): void
    {
        unset($this->avalanchesort);
        unset($this->quickSort);
        unset($this->bubbleSort);
        unset($this->compare);

    }

    public function showCountingResult($sortProblemMsg, $avalancheSortCounts, $quickSortCounts, $bubbleSortCounts )
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
        $bubbleCounts = array_column($bubbleSortCounts, MapperList::KEY_COUNT);

        $max=[];
        $digitsInNumbers =[];

        $max[] = $myMax = abs(max($quickCounts));
        $digitsInNumbers[] = (ceil(log10($myMax + 1)) + 2);
        $max[] = $myMax = abs(max($avalancheCounts));
        $digitsInNumbers[] = (ceil(log10($myMax + 1)) + 2);
        $max[] = $myMax = abs(max($bubbleCounts));
        $digitsInNumbers[] = (ceil(log10($myMax + 1)) + 2);
        $key = array_search(max($max),$max);
        $digitsInNumber = $digitsInNumbers[$key] ;
        fwrite(STDERR, print_r($sortProblemMsg, TRUE));
        $result = '';
        $result .= sprintf("%3s.", '');
        $result .= sprintf("%" . $infoLength . "s ", 'type');
        $result .= sprintf("%" . $digitsInNumber . "s ", 'BS');
        $result .= sprintf("%" . $digitsInNumber . "s ", 'QS');
        $result .= sprintf("%" . $digitsInNumber . "s ", 'AS');
        $result .= "\n";
        fwrite(STDERR, print_r($result, TRUE));
        foreach ($name as $key => $value) {
            if ($flag[$key]) {
                $result = '';
                $result .= sprintf("%3s. ", $key);
                $result .= sprintf("%" . $infoLength . "s ", $value);
                $result .= sprintf("%" . $digitsInNumber . "s ", $bubbleCounts[$key]);
                $result .= sprintf("%" . $digitsInNumber . "s ", $quickCounts[$key]);
                $result .= sprintf("%" . $digitsInNumber . "s ", $avalancheCounts[$key]);
                $result .= "\n";
                fwrite(STDERR, print_r($result, TRUE));
            }
        }
        fwrite(STDERR, "BS = bubbleSort, AS = avalanceSort, QS = quicksort\n\n", TRUE);
        fwrite(STDERR, "=========\n\n", TRUE);
    }

    /**
     * @return array[]
     */
    public function dataProviderTestStartSortMethodsGivenRandomFilledArrayThenSortIt()
    {
//        foreach ([false, true] as $flagAssoc) {
        foreach ([true,false,] as $flagAssoc) {
            foreach ([
//                     [20, true, 3.2, '20 elements, normal randomisiert'],
//                     [100, false, 3.2, '100 elements, normal randomized'],
//                     [100, true, 0.1, '100 elements, easy randomized'],
//                     [100, true, 1.2, '100 elements, simple randomized'],
//                     [100, true, 3.2, '100 elements, normal randomized'],
//                     [100, true, 10.2, '100 elements, heavy randomized'],
//                     [500, true, 3.2, '500 elements, normal randomized'],
//                     [2000, true, 5.2, '2000 elements, big randomized'],
//                     [2000, true, 1.0, '2000 elements, simple randomized'],
//                     [2000, true, 0.5, '2000 elements, easy randomized'],
                         [200, true, 5.2, '200 elements, big randomized'],
                         [200, true, 1.0, '200 elements, simple randomized'],
                         [200, true, 0.5, '200 elements, easy randomized'],
                         [200, false, 0.01, '200 elements, sorted (only 200 because of recursion of Quicksort and nesting-problem in xdebug)'],
//                         [-200, true, 5.2, '200 elements, antisorted, big randomized'],
//                         [-200, true, 1.0, '200 elements, antisorted, simple randomized'],
//                         [-200, true, 0.5, '200 elements, antisorted easy randomized'],
//                         [-200, false, 0.01, '200 elements, antisorted '],
//                     [10000, true, 3.2],
                     ]
                     as $myKey => $myParam
            ) {
                /** @var GenerateTestArrayTestService $generateTestArrayTestService */
                $generateTestArrayTestService = new GenerateTestArrayTestService(self::TEST_KEY);
                $testArrayLength = abs($myParam[0]);
                $flagRevers = $myParam[0]<=0;
                /// generate a sorter or a unsorted array
                $sortedArray = $generateTestArrayTestService->generateListSortedSimpleArray(
                    $testArrayLength,
                    $flagAssoc,
                    $flagRevers
                );
                if ($myParam[1]) {
                    $distoredArray = $generateTestArrayTestService->shuffleArrayForSorting(
                        $sortedArray,
                        0.01,
                        0.001,
                        $myParam[2]
                    );
                } else {
                    $distoredArray = $generateTestArrayTestService->arrangeResortArray($sortedArray);
                }
                $firstKeyDistorded = array_key_first($distoredArray);
                $firstKeySorted = array_key_first($sortedArray);
                $n = ($myParam[0]);
                $nLbN = round($n * log($n, 2), 2);
                $nSqr = $n * $n;

                $result[] = [
                    [
                        'main' => $myKey . '. Test an empty distorted array ',
                        'mapper' => 'operations in avalanche-sort an quicksort ' . "\n" .
                            $myParam[3] . "\n" .
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


            // Quicksort
//            $dataListBubble = new ArrayList();
            $dataListBubble = new MapperList(ArrayList::class);

            $dataListBubble->setDataList($distorted, $compareFunc);

            $rangeResultBubble = $this->bubbleSort->bubbleSortStart($dataListBubble);
            $resultRawBubble = $dataListBubble->getDataList();
            $firstResultBubble = array_key_first($resultRawBubble);
            $resultTestBubble = array_column($resultRawBubble, self::TEST_KEY);
            $mapperResultBubble = $dataListBubble->getCountsResult();

            $this->assertEquals(
                $expects['first'],
                $rangeResultBubble->getStart(),
                'start-index for index in restorted Range and in original sorted array are euqal'
            );
            $this->assertEquals(
                $expects['first'],
                $firstResultBubble,
                'detect by result-array: start-index for index in restorted Range and in original sorted array are equal'
            );
            $this->assertEquals(
                $expectSorted,
                $resultTestBubble,
                'test QuickSort: ' . $message['main']
            );

            $mapperResultBubble = $dataList->getCountsResult();
            $this->showCountingResult($message['mapper'], $mapperResult, $mapperResultQ,$mapperResultBubble);
        }
    }


    /**
     * @return array[]
     */
    public function dataProviderTestStartSortMethodsGivenRandomFilledListThenSortIt()
    {
//        foreach ([false, true] as $flagAssoc) {
        foreach ([true,false,] as $flagAssoc) {
            foreach ([
//                     [20, true, 3.2, '20 elements, normal randomisiert'],
//                     [100, false, 3.2, '100 elements, normal randomized'],
//                     [100, true, 0.1, '100 elements, easy randomized'],
//                     [100, true, 1.2, '100 elements, simple randomized'],
//                     [100, true, 3.2, '100 elements, normal randomized'],
//                     [100, true, 10.2, '100 elements, heavy randomized'],
//                     [500, true, 3.2, '500 elements, normal randomized'],
//                     [2000, true, 5.2, '2000 elements, big randomized'],
//                     [2000, true, 1.0, '2000 elements, simple randomized'],
//                     [2000, true, 0.5, '2000 elements, easy randomized'],
                         [200, true, 5.2, '200 elements, big randomized'],
                         [200, true, 1.0, '200 elements, simple randomized'],
                         [200, true, 0.5, '200 elements, easy randomized'],
                         [200, false, 0.01, '200 elements, sorted (only 200 because of recursion of Quicksort and nesting-problem in xdebug)'],
//                         [-200, true, 5.2, '200 elements, antisorted, big randomized'],
//                         [-200, true, 1.0, '200 elements, antisorted, simple randomized'],
//                         [-200, true, 0.5, '200 elements, antisorted easy randomized'],
//                         [-200, false, 0.01, '200 elements, antisorted '],
//                     [10000, true, 3.2],
                     ]
                     as $myKey => $myParam
            ) {
                /** @var GenerateTestArrayTestService $generateTestArrayTestService */
                $generateTestListTestService = new GenerateTestArrayTestService(self::TEST_KEY);
                $testListLength = abs($myParam[0]);
                $flagRevers = $myParam[0]<=0;
                /// generate a sorter or a unsorted array
                $sortedList = $generateTestListTestService->generateListSortedSimpleList(
                    $testListLength,
                    $flagAssoc,
                    $flagRevers
                );
                if ($myParam[1]) {
                    $distoredList = $generateTestListTestService->shuffleListForSorting(
                        $sortedList,
                        0.01,
                        0.001,
                        $myParam[2]
                    );
                } else {
                    $distoredList = $generateTestListTestService->arrangeResortList($sortedList);
                }
                $firstKeyDistorded = array_key_first($distoredList);
                $firstKeySorted = array_key_first($sortedList);
                $n = ($myParam[0]);
                $nLbN = round($n * log($n, 2), 2);
                $nSqr = $n * $n;

                $result[] = [
                    [
                        'main' => $myKey . '. Test an empty distorted array ',
                        'mapper' => 'operations in avalanche-sort an quicksort ' . "\n" .
                            $myParam[3] . "\n" .
                            '(n = ' . $n . ', n*lb(n) = ' . $nLbN . ', n^2 = ' . $nSqr . ")\n ",
                    ],

                    [ // Expects
                        'testDiffer' => $myParam[1],
                        'data' => $sortedList,
                        'first' => $firstKeyDistorded,
                        'firstSort' => $firstKeySorted,

                    ],
                    [ // params
                        'data' => $distoredList,
                        'first' => array_key_first($distoredList),
                    ],
                ];

            }
        }
        return $result;
    }


    /**
     * @param array $message
     * @param array $expects
     * @param array $params
     *
     * @dataProvider dataProviderTestStartSortMethodsGivenRandomFilledListThenSortIt
     */
    public function testStartSortMethodsGivenRandomFilledListThenSortIt(array $message, array $expects, array $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'no data in the provider for the testing of `' .
                'testStartAvalancheSort' . '`');
        } else {
            $expectSorted = array_column($expects['data'], self::TEST_KEY);
            $distorted = $params['data'];
            $resultDistorted = array_column($distorted, self::TEST_KEY);
            $compareFunc = new ListDataCompare(self::TEST_KEY);
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


            // Quicksort
//            $dataListBubble = new ArrayList();
            $dataListBubble = new MapperList(ArrayList::class);

            $dataListBubble->setDataList($distorted, $compareFunc);

            $rangeResultBubble = $this->bubbleSort->bubbleSortStart($dataListBubble);
            $resultRawBubble = $dataListBubble->getDataList();
            $firstResultBubble = array_key_first($resultRawBubble);
            $resultTestBubble = array_column($resultRawBubble, self::TEST_KEY);
            $mapperResultBubble = $dataListBubble->getCountsResult();

            $this->assertEquals(
                $expects['first'],
                $rangeResultBubble->getStart(),
                'start-index for index in restorted Range and in original sorted array are euqal'
            );
            $this->assertEquals(
                $expects['first'],
                $firstResultBubble,
                'detect by result-array: start-index for index in restorted Range and in original sorted array are equal'
            );
            $this->assertEquals(
                $expectSorted,
                $resultTestBubble,
                'test QuickSort: ' . $message['main']
            );

            $this->showCountingResult($message['mapper'], $mapperResult, $mapperResultQ,$mapperResultBubble);
        }
    }

}
