<?php

namespace Porthd\Avalanchesort\Storage\Additional;

use Porthd\Avalanchesort\Defs\DataListAllSortInterface;
use Porthd\Avalanchesort\Defs\DataListSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
use UnexpectedValueException;
use Porthd\Avalanchesort\Defs\DataCompareInterface;
use stdClass;

/***
 *
 * This file is part of the "Icon List" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Dr. Dieter Porth <info@mogber.de>
 *
 ***/
class MapperList implements DataListAllSortInterface
{

    public const KEY_COUNT = 'count';
    public const KEY_NAME = 'name';
    public const KEY_FLAG = 'flag'; // will you read this field with this flag in your statistics

    protected const FUNC_GET_DATA_LIST = 'gdl';
    protected const FUNC_SET_DATA_LIST = 'sdl';
    protected const FUNC_GET_FIRST_IDENT = 'gfi';
    protected const FUNC_GET_NEXT_IDENT = 'gni';
    protected const FUNC_GET_MIDDLE_IDENT = 'gmi';
    protected const FUNC_GET_RANDOM_IDENT = 'gri';
    protected const FUNC_GET_PREV_IDENT = 'gpi';
    protected const FUNC_GET_LAST_IDENT = 'gli';
    protected const FUNC_IS_LAST_IDENT = 'ili';
    protected const FUNC_CASCADE_DATA_LIST_CHANGE = 'casc';
    protected const FUNC_CASCADE_DATA_LIST_MOVES = 'cmov';
    protected const FUNC_SWAP = 'swap';
    protected const FUNC_SWAP_MOVES = 'smov';
    protected const FUNC_INIT_NEW_LIST_PART = 'inlp';
    protected const FUNC_ADD_LIST_PART = 'alp';
    protected const FUNC_ODD_LOWER_EQUAL_THAN_EVEN = 'compare';
    protected const FUNC_GET_DATA_ITEM = 'gdi';

    protected const FUNC_RESET_COUNT = [
        self::FUNC_GET_DATA_LIST => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getDataList',
        ],
        self::FUNC_SET_DATA_LIST => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'setDataList',
        ],
        self::FUNC_GET_DATA_ITEM => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getDataItem',
        ],
        self::FUNC_GET_FIRST_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getFirstIdent',
        ],
        self::FUNC_GET_NEXT_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getNextIdent',
        ],
        self::FUNC_GET_MIDDLE_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getMiddleIdent',
        ],
        self::FUNC_GET_RANDOM_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getRandomIdent',
        ],
        self::FUNC_GET_PREV_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getPrevIdent',
        ],
        self::FUNC_GET_LAST_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'getLastIdent',
        ],
        self::FUNC_IS_LAST_IDENT => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'isLastIdent',
        ],
        self::FUNC_CASCADE_DATA_LIST_CHANGE => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'cascadeDataListChange',
        ],
        self::FUNC_CASCADE_DATA_LIST_MOVES => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'cascadeData (moves)',
        ],
        self::FUNC_SWAP => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'swap',
        ],
        self::FUNC_SWAP_MOVES => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'swap (moves)',
        ],
        self::FUNC_INIT_NEW_LIST_PART => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'initNewListPart',
        ],
        self::FUNC_ADD_LIST_PART => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'addListPart',
        ],
        self::FUNC_ODD_LOWER_EQUAL_THAN_EVEN => [
            self::KEY_FLAG => true,
            self::KEY_COUNT => 0,
            self::KEY_NAME => 'oddLowerEqualThanEven',
        ],
    ];

    /**
     * @var array|int[]
     */
    protected $count = [];

    /**
     * @var DataListSortInterface
     *
     */
    protected $mapped;

    public function __construct($className)
    {
        $this->count = self::FUNC_RESET_COUNT;
        $this->mapped = new $className();
        if (array_search(DataListSortInterface::class, class_implements($this->mapped)) === false) {
            throw new UnexpectedValueException(
                'the class implements not the estimated interface. `' . DataListSortInterface::class . '`',
                1592662983
            );
        }
    }

    public function reinitCount()
    {
        $this->count = self::FUNC_RESET_COUNT;
    }

    public function getCountsResult(): array
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function getDataList(): array
    {
        $this->count[self::FUNC_GET_DATA_LIST][self::KEY_COUNT]++;
        return $this->mapped->getDataList();
    }

    /**
     * @param $dataList
     */
    public function setDataList($dataList, DataCompareInterface $compareFunc)
    {
        $this->count[self::FUNC_SET_DATA_LIST][self::KEY_COUNT]++;
        return $this->mapped->setDataList($dataList, $compareFunc);
    }

    /**
     * @return mixed
     */
    public function getFirstIdent()
    {
        $this->count[self::FUNC_GET_FIRST_IDENT][self::KEY_COUNT]++;
        return $this->mapped->getFirstIdent();
    }

    /**
     * @param $currentKey
     * @return bool|mixed
     */
    public function getNextIdent($currentKey)
    {
        $this->count[self::FUNC_GET_NEXT_IDENT][self::KEY_COUNT]++;
        return $this->mapped->getNextIdent($currentKey);
    }


    /**
     * @param DataRangeInterface $oddListRange
     * @return mixed
     */
    public function getMiddleIdent(DataRangeInterface $oddListRange)
    {
        $this->count[self::FUNC_GET_MIDDLE_IDENT][self::KEY_COUNT]++;
        return $this->mapped->getMiddleIdent($oddListRange);
    }


    /**
     * @param DataRangeInterface $oddListRange
     * @return mixed
     */
    public function getRandomIdent(DataRangeInterface $oddListRange)
    {
        $this->count[self::FUNC_GET_RANDOM_IDENT][self::KEY_COUNT]++;
        return $this->mapped->getRandomIdent($oddListRange);
    }


    /**
     * @param $currentKey
     * @return bool|mixed
     */
    public function getPrevIdent($currentKey)
    {
        $this->count[self::FUNC_GET_PREV_IDENT][self::KEY_COUNT]++;
        return $this->mapped->getPrevIdent($currentKey);
    }


    /**
     * @return mixed
     */
    public function getLastIdent()
    {
        $this->count[self::FUNC_GET_LAST_IDENT][self::KEY_COUNT]++;
        return $this->mapped->getLastIdent();
    }

    /**
     * @param $currentIdent
     * @return bool
     */
    public function isLastIdent($currentIdent): bool
    {
        $this->count[self::FUNC_IS_LAST_IDENT][self::KEY_COUNT]++;
        return $this->mapped->isLastIdent($currentIdent);
    }

    /**
     * @return stdClass
     */
    public function cascadeDataListChange(DataRangeInterface $resultRange)
    {
        $this->count[self::FUNC_CASCADE_DATA_LIST_CHANGE][self::KEY_COUNT]++;
        $start = $resultRange->getStart();
        $stop = $resultRange->getStop();
        $moves = $stop-$start+2;
        $this->count[self::FUNC_CASCADE_DATA_LIST_MOVES][self::KEY_COUNT] += $moves;
        return $this->mapped->cascadeDataListChange($resultRange);
    }

    /**
     * @param $oddListRange
     * @param $evenListRange
     */
    public function initNewListPart($oddListRange, $evenListRange)
    {
        $this->count[self::FUNC_INIT_NEW_LIST_PART][self::KEY_COUNT]++;
        return $this->mapped->initNewListPart($oddListRange, $evenListRange);
    }

    /**
     * @param $itemKey
     */
    public function addListPart($itemKey)
    {
        $this->count[self::FUNC_ADD_LIST_PART][self::KEY_COUNT]++;
        return $this->mapped->addListPart($itemKey);
    }

    /**
     * @param $oddIdent
     * @param $eventIdent
     * @return bool
     */
    public function oddLowerEqualThanEven($oddIdent, $eventIdent): bool
    {
        $this->count[self::FUNC_ODD_LOWER_EQUAL_THAN_EVEN][self::KEY_COUNT]++;
        return $this->mapped->oddLowerEqualThanEven($oddIdent, $eventIdent);
    }

    public function swap($oddIdent, $eventIdent)
    {
        $this->count[self::FUNC_SWAP][self::KEY_COUNT]++;
        $this->count[self::FUNC_SWAP_MOVES][self::KEY_COUNT] +=3; // you need three datamoves for each swap
        return $this->mapped->swap($oddIdent, $eventIdent);
    }

    public function getDataItem($ident)
    {
        $this->count[self::FUNC_GET_DATA_ITEM][self::KEY_COUNT]++;
        return $this->mapped->getDataItem($ident);
    }
}

