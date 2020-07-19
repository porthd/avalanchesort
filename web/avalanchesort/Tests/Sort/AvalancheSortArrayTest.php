<?php

namespace Porthd\Avalanchesort\Sort;

use PHPUnit\Framework\TestCase;
use Porthd\Avalanchesort\Defs\DataCompareInterface;
use Porthd\Avalanchesort\Service\GenerateTestArrayTestService;
use Porthd\Avalanchesort\Storage\Additional\MapperList;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayDataCompare;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayIndexDataRange;
use Porthd\Avalanchesort\Storage\ArrayType\ArrayList;
use Porthd\Avalanchesort\Storage\Additional\TestSimpleArrayCompare;
use Porthd\Avalanchesort\Storage\ListType\ListDataCompare;

class AvalancheSortArrayTest extends TestCase
{
    protected const TEST_KEY = 'test';

    /**
     * @var \Porthd\Avalanchesort\Sort\AvalancheSort
     */
    protected $avalanchesort;
    protected $compareFunctionName;

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

    public function showCountingResult($sortProblemMsg, $avalancheSortCounts, $quickSortCounts, $bubbleSortCounts)
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

        $max = [];
        $digitsInNumbers = [];

        $max[] = $myMax = abs(max($quickCounts));
        $digitsInNumbers[] = (ceil(log10($myMax + 1)) + 2);
        $max[] = $myMax = abs(max($avalancheCounts));
        $digitsInNumbers[] = (ceil(log10($myMax + 1)) + 2);
        $max[] = $myMax = abs(max($bubbleCounts));
        $digitsInNumbers[] = (ceil(log10($myMax + 1)) + 2);
        $key = array_search(max($max), $max);
        $digitsInNumber = $digitsInNumbers[$key];
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
        // If You use xdebug, then the nestinlevel may cause a fail of the tests
        foreach ([true, false,] as $flagAssoc) {
            $addMsg = ($flagAssoc ? ' in Associative array' : '');
            foreach ([
                         [20, true, 3.2, '20 elements'.$addMsg.', normal randomisiert'],
                         [100, false, 3.2, '100 elements'.$addMsg.', normal randomized'],
                         [100, true, 0.1, '100 elements'.$addMsg.', easy randomized'],
                         [100, true, 1.2, '100 elements'.$addMsg.', simple randomized'],
                         [100, true, 3.2, '100 elements'.$addMsg.', normal randomized'],
                         [100, true, 10.2, '100 elements'.$addMsg.', heavy randomized'],
                         [500, true, 3.2, '500 elements'.$addMsg.', normal randomized'],
                         [2000, true, 5.2, 'patience: 2000 elements'.$addMsg.', big randomized'],
                         [2000, true, 1.0, 'patience: 2000 elements'.$addMsg.', simple randomized'],
                         [2000, true, 0.5, 'patience:2000 elements'.$addMsg.', easy randomized'],
                         [200, true, 5.2, '200 elements'.$addMsg.', big randomized'],
                         [200, true, 1.0, '200 elements'.$addMsg.', simple randomized'],
                         [200, true, 0.5, '200 elements'.$addMsg.', easy randomized'],
                         [200, false, 0.01, '200 elements'.$addMsg.', sorted (only 200 because of recursion of Quicksort and nesting-problem in xdebug)'],
                         [-200, true, 5.2, '200 elements' . $addMsg . ', antisorted, big randomized'],
                         [-200, true, 1.0, '200 elements' . $addMsg . ', antisorted, simple randomized'],
                         [-200, true, 0.5, '200 elements' . $addMsg . ', antisorted easy randomized'],
                         [-200, true, 0.01, '200 elements' . $addMsg . ', antisorted '],
//                         [10000, true, 3.2, 'Super Patience, 10000 elements' . $addMsg . ', antisorted '],
                     ]
                     as $myKey => $myParam
            ) {
                /** @var GenerateTestArrayTestService $generateTestArrayTestService */
                $generateTestArrayTestService = new GenerateTestArrayTestService(self::TEST_KEY);
                $testArrayLength = abs($myParam[0]);
                $flagRevers = $myParam[0] <= 0;
                /// generate a sorter or a unsorted array
                $testArray = $generateTestArrayTestService->generateListSortedSimpleArray(
                    $testArrayLength,
                    $flagAssoc,
                    $flagRevers
                );
                if ($flagRevers) {
                    $sortedArray = $testArray;
                    $myDummylength = count($sortedArray)-1;
                    // generate a sorted varioation
                    array_walk($sortedArray,
                        function (&$item, $key, $myDummylength) {
                            $item[self::TEST_KEY] = $myDummylength - $item[self::TEST_KEY];
                        },
                        $myDummylength
                    );
                } else {
                    $sortedArray = $testArray;
                }
                if ($myParam[1]) {
                    $distoredArray = $generateTestArrayTestService->shuffleArrayForSorting(
                        $testArray,
                        0.01,
                        0.001,
                        $myParam[2]
                    );
                } else {
                    $distoredArray = $generateTestArrayTestService->arrangeResortArray($testArray);
                }
                $firstKeyDistorded = array_key_first($distoredArray);
                $firstKeySorted = array_key_first($sortedArray);
                $n = ($myParam[0]);
                $nLbN = round($n * log($n, 2), 2);
                $nSqr = $n * $n;

                $result[] = [
                    [
                        'main' => $myKey . '. Test an distorted array ',
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
            $keyList = array_keys($distorted);
            $dataList = new MapperList(ArrayList::class, $keyList);
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
            $keyList = array_keys($distorted);
            $dataListQ = new MapperList(ArrayList::class, $keyList);
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
            $keyList = array_keys($distorted);
            $dataListBubble = new MapperList(ArrayList::class, $keyList);
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

            $this->showCountingResult($message['mapper'], $mapperResult, $mapperResultQ, $mapperResultBubble);
        }
    }


    /**
     * @return array[]
     */
    public function dataProviderTestStartSortMethodsGivenRandomFilledListThenSortIt()
    {
        // If You use xdebug, then the nestinlevel may cause a fail of the tests
        foreach ([true, false,] as $flagAssoc) {
            $addMsg = ($flagAssoc ? ' in Associative array' : '');
            foreach ([
                         [20, true, 3.2, '20 elements'.$addMsg.', normal randomisiert'],
                         [100, false, 3.2, '100 elements'.$addMsg.', normal randomized'],
                         [100, true, 0.1, '100 elements'.$addMsg.', easy randomized'],
                         [100, true, 1.2, '100 elements'.$addMsg.', simple randomized'],
                         [100, true, 3.2, '100 elements'.$addMsg.', normal randomized'],
                         [100, true, 10.2, '100 elements'.$addMsg.', heavy randomized'],
                         [500, true, 3.2, '500 elements'.$addMsg.', normal randomized'],
                         [2000, true, 5.2, 'patience 2000 elements'.$addMsg.', big randomized'],
                         [2000, true, 1.0, 'patience 2000 elements'.$addMsg.', simple randomized'],
                         [2000, true, 0.5, 'patience 2000 elements'.$addMsg.', easy randomized'],
                         [200, true, 5.2, '200 elements' . $addMsg . ', big randomized'],
                         [200, true, 1.0, '200 elements' . $addMsg . ', simple randomized'],
                         [200, true, 0.5, '200 elements' . $addMsg . ', easy randomized'],
                         [200, false, 0.01, '200 elements' . $addMsg . ', sorted (only 200 because of recursion of Quicksort and nesting-problem in xdebug)'],
                         [-200, true, 5.2, '200 elements'.$addMsg.', antisorted, big randomized'],
                         [-200, true, 1.0, '200 elements'.$addMsg.', antisorted, simple randomized'],
                         [-200, true, 0.5, '200 elements'.$addMsg.', antisorted easy randomized'],
                         [-200, false, 0.01, '200 elements'.$addMsg.', antisorted '],
//                         [10000, true, 3.2, 'Super Patience, 10000 elements' . $addMsg . ', antisorted '],
                 ]
                     as $myKey => $myParam
            ) {
                /** @var GenerateTestArrayTestService $generateTestArrayTestService */
                $generateTestListTestService = new GenerateTestArrayTestService(self::TEST_KEY);
                $testListLength = abs($myParam[0]);
                $flagRevers = $myParam[0] <= 0;
                /// generate a sorter or a unsorted array
                $testList = $generateTestListTestService->generateListSortedSimpleList(
                    $testListLength,
                    $flagAssoc,
                    $flagRevers
                );
                if ($flagRevers) {
                    $sortedList = $testList;
                    $myDummylength = count($sortedList)-1;
                    // generate a sorted varioation
                    array_walk($sortedList,
                        function (&$item, $key, $myDummylength) {
                            $item[self::TEST_KEY] = $myDummylength - $item[self::TEST_KEY];
                        },
                        $myDummylength
                    );
                } else {
                    $sortedList = $testList;
                }
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
                        'main' => $myKey . '. Test an distorted array ',
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
            $keyList = array_keys($distorted);
            $dataList = new MapperList(ArrayList::class, $keyList);
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
            $keyList = array_keys($distorted);
            $dataListQ = new MapperList(ArrayList::class, $keyList);
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
            $keyList = array_keys($distorted);
            $dataListBubble = new MapperList(ArrayList::class, $keyList);
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

            $this->showCountingResult($message['mapper'], $mapperResult, $mapperResultQ, $mapperResultBubble);
        }
    }


    public static function cmpLastName($a, $b)
    {
        return ($a['lastName'] < $b['lastName']) ? -1 : 1;
    }


    public static function cmpLastAndFirstName($a, $b)
    {
        if ($a['lastName'] < $b['lastName']) {
            $flag = -1;
        } else if ($a['lastName'] === $b['lastName']) {
            if ($a['firstName'] < $b['firstName']) {
                $flag = -1;
            } else if ($a['firstName'] === $b['firstName']) {
                $flag = 0;
            } else {
                $flag = 1;
            }
        } else {
            $flag = 1;
        }
        return $flag;
    }

    public function testUsortUnstable()
    {
        $presortedFirstNameTestArray = [
            ['firstName' => 'Arthur', 'lastName' => 'Meyer'],
            ['firstName' => 'Arthur', 'lastName' => 'Schmidt'],
            ['firstName' => 'Nutella', 'lastName' => 'Meyer'],
            ['firstName' => 'Nutella', 'lastName' => 'Schmidt'],
        ];
        $copyTestArray = $presortedFirstNameTestArray;
        $expectedSorted = [
            ['firstName' => 'Arthur', 'lastName' => 'Meyer'],
            ['firstName' => 'Nutella', 'lastName' => 'Meyer'],
            ['firstName' => 'Arthur', 'lastName' => 'Schmidt'],
            ['firstName' => 'Nutella', 'lastName' => 'Schmidt'],
        ];
        $disorderdSorted = [
            ['firstName' => 'Nutella', 'lastName' => 'Meyer'],
            ['firstName' => 'Nutella', 'lastName' => 'Meyer'],
            ['firstName' => 'Arthur', 'lastName' => 'Meyer'],
            ['firstName' => 'Nutella', 'lastName' => 'Schmidt'],
            ['firstName' => 'Arthur', 'lastName' => 'Schmidt'],
        ];

        usort($presortedFirstNameTestArray, [AvalancheSortArrayTest::class, 'cmpLastName']);
        usort($copyTestArray, [AvalancheSortArrayTest::class, 'cmpLastAndFirstName']);
        $presortedFirstNameTestArrayList = "\n";
        $copyTestArrayList = "\n";
        foreach ($presortedFirstNameTestArray as $item) {
            $presortedFirstNameTestArrayList .= "\n" . $item['lastName'] . ' ' . $item['firstName'];
        }
        foreach ($copyTestArray as $item) {
            $copyTestArrayList .= "\n" . $item['lastName'] . ' ' . $item['firstName'];
        }

        $this->assertEquals(
            $disorderdSorted,
            $presortedFirstNameTestArray,
            '1. Test for Failure: The result should get the distroted Array, because quicksort is not stable ' .
            $presortedFirstNameTestArrayList . "\n"
        );
        $this->assertNotEquals(
            $expectedSorted,
            $presortedFirstNameTestArray,
            '2. Test for Failure: I want to have a list sorted by Lastname and firstname, but i get a List sorted by lastname and dissorted be firstname. ' .
            $presortedFirstNameTestArrayList . "\n"
        );
        $this->assertNotEquals(
            $disorderdSorted,
            $copyTestArray,
            'Better Cmparefunction: Check against distorted: I will get a list sorted by lastname and firstname and would not  get a List sorted by lastname and dissorted be firstname. ' .
            $copyTestArrayList . "\n"
        );
        $this->assertEquals(
            $expectedSorted,
            $copyTestArray,
            'Better Cmparefunction: Check against sorted/expected: I will get a list sorted by lastname and firstname and would not  get a List sorted by lastname and dissorted be firstname. ' .
            $copyTestArrayList . "\n" .
            print_r($expectedSorted, true) . "\n"
        );

    }

    public function testAvalancheSortStable()
    {
        $unsortedFirstNameTestArray = [
            ['firstName' => 'Nutella', 'lastName' => 'Schmidt'],
            ['firstName' => 'Arthur', 'lastName' => 'Meyer'],
            ['firstName' => 'Nutella', 'lastName' => 'Meyer'],
            ['firstName' => 'Arthur', 'lastName' => 'Schmidt'],
        ];
        $expectedSorted = [
            ['firstName' => 'Arthur', 'lastName' => 'Meyer'],
            ['firstName' => 'Nutella', 'lastName' => 'Meyer'],
            ['firstName' => 'Arthur', 'lastName' => 'Schmidt'],
            ['firstName' => 'Nutella', 'lastName' => 'Schmidt'],
        ];

        // Sort first with forst-name and the with lastname
        $compareClass = new TestSimpleArrayCompare('firstName');
        $keyList = array_keys($unsortedFirstNameTestArray);
        $dataList = new MapperList(ArrayList::class, $keyList);
        $dataList->setDataList($unsortedFirstNameTestArray, $compareClass);
        $this->avalanchesort->startAvalancheSort($dataList);

        $compareClass->changeTestKey('lastName');
        $this->avalanchesort->startAvalancheSort($dataList);
        $result = $dataList->getDataList();
        $resultList = "\n";
        foreach ($result as $item) {
            $resultList .= "\n" . $item['lastName'] . ' ' . $item['firstName'];
        }
        $this->assertEquals(
            $expectedSorted,
            $result,
            'Stable-Compare after two sort-threads: I will get a list sorted by lastname and firstname and would not  get a List sorted by lastname and dissorted be firstname. ' .
            $resultList . "\n" .
            print_r($expectedSorted, true) . "\n"
        );

    }
}
