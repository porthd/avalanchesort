<?php
namespace Porth\Avalanchesort\Storage\ArrayType;

use PHPUnit\Framework\TestCase;
use Porth\Avalanchesort\Storage\ArrayType\ArrayDataCompare;
use Porth\Avalanchesort\Storage\ArrayType\ArrayList;
use stdClass;
use UnexpectedValueException;

class ArrayListTest extends TestCase
{

    /**
     * @var ArrayList
     */
    protected $subject;

    /**
     * @var ArrayDataCompare
     */
    protected $compare;

    public function setUp():void
    {
        $this->subject = new ArrayList();
        $this->compare = new ArrayDataCompare();

    }

    public function tearDown():void
    {
        unset($this->subject);
        unset($this->compare);
    }


    /**
     * @return array[]
     * @throws \Exception
     */
    public function dataProviderGetDataListGiveAndSetDataListDoNotChangeResultIfArrayIsGiven()
    {
        $dateString = '2011-01-01T15:03:01.012345Z';
        return [
//            [
//                'message' => '1. getDataList return an empty array, which was set before.',
//                'expects'=> [
//                    'list' => [],
//                ],
//                'params' => [
//                    'list' => [],
//                ]
//            ],
            [
                'message' => '2.a. getDataList return an normal array with one element, which was set before.',
                'expects' => [
                    'list' => [12],
                ],
                'params' => [
                    'list' => [12],
                ]
            ],
            [
                'message' => '2.b getDataList return an associatative array with one element, which was set before.',
                'expects' => [
                    'list' => ['blu'=>12],
                ],
                'params' => [
                    'list' => ['blu'=>12],
                ]
            ],
            [
                'message' => '3.a. getDataList return an normal array with two elements, which was set before.',
                'expects' => [
                    'list' => [12, 15],
                ],
                'params' => [
                    'list' => [12, 15],
                ]
            ],
            [
                'message' => '3.b getDataList return an associatative array with two elements, which was set before.',
                'expects' => [
                    'list' => ['blu'=>12, 'bluu'=> 17],
                ],
                'params' => [
                    'list' => ['blu'=>12, 'bluu'=> 17],
                ]
            ],
            [
                'message' => '4.a. getDataList return an normal array with five elements, which was set before.',
                'expects' => [
                    'list' => [12, 15, -1, 'halslo', new \DateTime($dateString)],
                ],
                'params' => [
                    'list' => [12, 15, -1, 'halslo', new \DateTime($dateString)],
                ]
            ],
            [
                'message' => '4.b getDataList return an associatative array with five elements, which was set before.',
                'expects' => [
                    'list' => ['blu'=>12, 'bluu'=> 17, -1, 'hallo', 'myObjet' => new \DateTime($dateString)],
                ],
                'params' => [
                    'list' => ['blu'=>12, 'bluu'=> 17, -1, 'hallo', 'myObjet' => new \DateTime($dateString)],
                ]
            ],
        ];
    }

    /**
     * @param $message
     * @param $expects
     * @param $params
     *
     * @dataProvider dataProviderGetDataListGiveAndSetDataListDoNotChangeResultIfArrayIsGiven
     * @test
     */
    public function getDataListGiveAndSetDataListDoNotChangeResultIfArrayIsGiven(string $message, array $expects, array $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'empty-data at the end of the provider');
        } else {
            $this->subject->setDataList($params['list'], $this->compare );
            $result = $this->subject->getDataList();
            $this->assertEquals(
                $result,
                $params['list'],
                'test get-/setDataList: ' . $message
            );
        }
    }

    /**
     * @return array[]
     * @throws \Exception
     */
    public function dataProviderSetDataListThrowsExceptionIfArrayEmptyOrNotArray()
    {
        return [
            [
                'message' => '1. setDataList generates an exception for an empty array.',
                'expects'=> [
                    'exceptionClass' => UnexpectedValueException::class,
                ],
                'params' => [
                    'list' => [],
                ]
            ],
            [
                'message' => '2. setDataList generates an exception for an integer',
                'expects'=> [
                    'exceptionClass' => UnexpectedValueException::class,
                ],
                'params' => [
                    'list' => 25,
                ]
            ],
            [
                'message' => '2. setDataList generates an exception for an string',
                'expects'=> [
                    'exceptionClass' => UnexpectedValueException::class,
                ],
                'params' => [
                    'list' => 'hallo world',
                ]
            ],
            [
                'message' => '2. setDataList generates an exception for an object',
                'expects'=> [
                    'exceptionClass' => UnexpectedValueException::class,
                ],
                'params' => [
                    'list' => new stdClass(),
                ]
            ],
        ];
    }
    /**
     * @param $message
     * @param $expects
     * @param $params
     *
     * @dataProvider dataProviderSetDataListThrowsExceptionIfArrayEmptyOrNotArray
     * @test
     */
    public function setDataListThrowsExceptionIfArrayEmptyOrNotArray(string $message, array $expects, array $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'empty-data at the end of the provider');
        } else {
            $this->expectException($expects['exceptionClass']);
            // or for PHPUnit < 5.2
            // $this->setExpectedException(InvalidArgumentException::class);

            //...and then add your test code that generates the exception
            $this->subject->setDataList($params['list'], $this->compare );
        }
    }


    /**
     * @return array[]
     */
    public function dataProvider()
    {
        return [
            [
                '1. Given something If something happens Then special result ',
                [
                    'expectsOne' => [
                        'something' => 'value'
                    ],
                ],
                [
                    'paramsOne' => [
                        'something' => 'value'
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
     * @dataProvider dataProviderMyFunctionGiveAndSetDataListDoNotChangeResultIfArrayIsGiven
     * @test
     */
    public function myFunctionGivenSomethingIfSomegeneralThenGeneralResult(string $message, array $expects, array $params)
    {
        if (!isset($expects) && empty($expects)) {
            $this->assertSame(true, true, 'empty-data at the end of the provider');
        } else {
            $this->subject->setDataList($params['list'], $this->compare );
            $result = $this->subject->myFunction(...$params);
            $this->assertEquals(
                $expects['expectsOne'],
                $result,
                'test myFunction: ' . $message
            );
        }
    }

}


