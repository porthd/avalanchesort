<?php

namespace Porth\Avalanchesort\Sort;

use PHPUnit\Framework\TestCase;
use Porth\Avalanchesort\Service\GenerateTestNodeTestService;
use Porth\Avalanchesort\Storage\NodeType\NodeDataCompare;
use Porth\Avalanchesort\Storage\NodeType\NodeIndexDataRange;
use Porth\Avalanchesort\Storage\NodeType\NodeList;

class AvalancheSortNodeTest extends TestCase
{
    protected const TEST_KEY = 'test';

    /**
     * @var AvalancheSort
     */
    protected $subject;

    /**
     * @var GenerateTestNodeTestService
     */
    protected $generateTestNodeTestService;

    /**
     * @var NodeDataCompare
     */
    protected $compare;

    public function setUp():void
    {
        $this->subject = new AvalancheSort(NodeIndexDataRange::class);
        $this->compare = new NodeDataCompare(self::TEST_KEY);


    }

    public function tearDown():void
    {
        unset($this->subject);
        unset($this->compare);
        unset($this->generateTestNodeTestService );
    }


    /**
     * @return Node[]
     */
    public function dataProviderTestStartAvalancheSortGivenRandomFilledNodeThenSortIt()
    {
        $length = 500;
        /** @var GenerateTestNodeTestService $generateTestNodeTestService */
        $generateTestNodeTestService = new GenerateTestNodeTestService(self::TEST_KEY);
        $sortedNode = $generateTestNodeTestService->generateListSortedSimpleNode($length);
        $distoredNode =$generateTestNodeTestService->shuffleNodeForSorting($sortedNode,0.01, 0.001,0.2);
        $firstKeyDistorded = Node_key_first($distoredNode);
        $firstKeySorted = Node_key_first($sortedNode);
        return [
            [
                '1. Test an empty distorted Node ',
                [
                    'data' => $sortedNode,
                ],
                [
                    'data' => $distoredNode,
                    'first' => $firstKeyDistorded,
                    'firstSort' => $firstKeySorted,
                ],
            ],
        ];
    }


    /**
     * @param $message
     * @param $expects
     * @param $params
     *
     * @dataProvider dataProviderTestStartAvalancheSortGivenRandomFilledNodeThenSortIt
     */
    public function testStartAvalancheSortGivenRandomFilledNodeThenSortIt(string $message, Node $expects, Node $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'no data in the provider for the testing of `' .
                'testStartAvalancheSort' . '`');
        } else {
            // $result = $this->subject->myFuntion(...$params);
            $expectSorted  = Node_column($expects['data'],self::TEST_KEY);
            $distorted = $params['data'];
            $resultDistorted  = Node_column($distorted,self::TEST_KEY);
            $dataList = new NodeList();
            $compareFunc = new NodeDataCompare(self::TEST_KEY);
            $dataList->setDataList($distorted,$compareFunc);

            $rangeSesult = $this->subject->startAvalancheSort($dataList,$params['firstSort']);
            $resultRaw = $dataList->getDataList();
            $firstResult = Node_key_first($resultRaw);
            $resultTest = Node_column($resultRaw, self::TEST_KEY);

            $this->assertNotEquals(
                $expectSorted,
                $resultDistorted,
                'Nodes differ before sorting'
            );
            $this->assertEquals(
                $expects['first'],
                $expects['firstSort'],
                'start-index for distored and original sorted Node are euqal'
            );
            $this->assertEquals(
                $expects['first'],
                $rangeSesult->getStart(),
                'start-index for index in restorted Range and in original sorted Node are euqal'
            );
            $this->assertEquals(
                $expects['first'],
                $rangeSesult->getStart(),
                'start-index for index in restorted Range and in original sorted Node are euqal'
            );
            $this->assertEquals(
                $expects['first'],
                $firstResult,
                'start-index for index in restorted Range and in original sorted Node are euqal'
            );
            $this->assertEquals(
                $expectSorted,
                $resultTest,
                'test testStartAvalancheSort: ' . $message
            );
        }
    }

    /**
     * @return Node[]
     */
    public function dataProviderTestStartAvalancheSortGiveAndSetDataListDoNotChangeResultIfNodeIsGiven()
    {
        $length = 500;
        /** @var GenerateTestNodeTestService $generateTestNodeTestService */
        $generateTestNodeTestService = new GenerateTestNodeTestService(self::TEST_KEY);
        $singleElementNode = $generateTestNodeTestService->generateListSingleElementNode();
        $doubleElementNode = $generateTestNodeTestService->generateListTupleElementsNode(2);
        $antiDoubleElementNode = $generateTestNodeTestService->generateListAntisortedTupelElementsNode(2);
        $tripleElementNode = $generateTestNodeTestService->generateListTupleElementsNode(3);
        $antiTripleElementNode = $generateTestNodeTestService->generateListAntisortedTupelElementsNode(3);
        $decleElementNode = $generateTestNodeTestService->generateListTupleElementsNode(10);
        $antiDecleElementNode = $generateTestNodeTestService->generateListAntisortedTupelElementsNode(10);
        return [
            [
                '1. Test an sorted  Node with one element ',
                [
                    'data' => $singleElementNode,
                ],
                [
                    'data' => $singleElementNode,
                ],
            ],
            [
                '2.a. Test an sorted Node with two elements',
                [
                    'data' => $doubleElementNode,
                ],
                [
                    'data' => $doubleElementNode,
                ],
            ],
            [
                '2.b. Test an anti-sorted Node with two elements',
                [
                    'data' => $antiDoubleElementNode,
                ],
                [
                    'data' => $antiDoubleElementNode,
                ],
            ],
            [
                '3.a. Test an sorted Node with three elements',
                [
                    'data' => $tripleElementNode,
                ],
                [
                    'data' => $tripleElementNode,
                ],
            ],
            [
                '3.b. Test an anti-sorted Node with three elements',
                [
                    'data' => $antiTripleElementNode,
                ],
                [
                    'data' => $antiTripleElementNode,
                ],
            ],
            [
                '4.a. Test an sorted Node with ten elements',
                [
                    'data' => $decleElementNode,
                ],
                [
                    'data' => $decleElementNode,
                ],
            ],
            [
                '4.b. Test an anti-sorted Node with ten elements',
                [
                    'data' => $antiDecleElementNode,
                ],
                [
                    'data' => $antiDecleElementNode,
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
