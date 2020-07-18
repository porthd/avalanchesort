<?php

namespace Porthd\Avalanchesort\Storage\ListType;

use Porthd\Avalanchesort\Defs\DataCompareInterface;
use Porthd\Avalanchesort\Defs\DataListAllSortInterface;
use Porthd\Avalanchesort\Defs\DataListQuickSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
use stdClass;
use UnexpectedValueException;

/**
 * This is a hybrid between Array and List
 */
class ListList implements DataListAllSortInterface
{


    protected $list = null;


    public const NODE_LIST_NEXT_DATA_POINTER = 'next';
    protected $nextName = self::NODE_LIST_NEXT_DATA_POINTER;


    /** @var null|ListBase */
    protected $dataList = null;

    protected $firstRef;
    protected $lastRef;
    protected $lengthList = 0;

    protected $startRef;
    protected $stopRef;
    protected $startOdd;
    protected $stopOdd;
    protected $startEven;
    protected $stopEven;
    protected $priorOddRef;
    protected $postEvenRef;

    /**
     * @var DataCompareInterface null
     */
    protected $compareFunc;

    public function getDataItem($ref) //okay
    {
        if (($ref === null) ||
            (!isset($ref[ListBase::LIST_VALUE]))
        ) {
            throw new UnexpectedValueException(
                'An Unexpected error. The ident `' . print_r($ref, true) .
                '` is undefined or it indicates an undefined value in the datalist.',
                1592951234
            );

        }
        return $ref[ListBase::LIST_VALUE];
    }

    /**
     * @return object
     */
    public function getDataList(): array // okay
    {

        return $this->dataList->getArrayByList();
    }

    /**
     * @param array $dataList
     * @param DataCompareInterface $compareFunc
     */
    public function setDataList($dataList, DataCompareInterface $compareFunc) // okay
    {
        if ((!is_array($dataList)) ||
            (empty($dataList)) ||
            ($this->dataList !== null)
        ) {
            throw new UnexpectedValueException(
                'The value must be at least an object, which point on the start of the node-list - even if the current type is not checked. ' .
                'Theree must at least exist a property `' . $this->nextName . '`.',
                1592421675
            );
        }
        $this->compareFunc = $compareFunc;
        $this->dataList = new ListBase();
        foreach ($dataList as $key => $item) {
            $this->dataList->addLast($item, $key);
        }
        $this->firstRef = $this->dataList->getFirstRef();
        $this->lengthList = count($dataList);
        $this->lastRef = $this->dataList->getLastRef();
    }

    /**
     * @return mixed
     */
    public function getFirstIdent() // okay
    {
        return $this->dataList->getFirstRef();
    }

    /**
     * @return mixed
     */
    public function getLastIdent() // okay
    {
        return $this->dataList->getLastRef();

    }

    /**
     * @param $currentRef
     * @return mixed
     */
    public function getNextIdent($currentRef) // okay
    {
        return $this->dataList->getNextRef($currentRef);
    }


    /**
     * @param $currentRef
     * @return bool
     */
    public function isLastIdent($currentRef): bool // okay
    {
        return ($this->dataList->getNextRef($currentRef) === null); // natural choice
        //        return ($currentRef === $this->dataList->getLastRef()); // not good, it depends on the correct solving the meta-data
    }

//    /** not needed here
//     * @param $currentRef
//     * @return bool
//     */
//    public function isFirstIdent($currentRef): bool // okay
//    {
//        return ($this->dataList->getPrevRef($currentRef) === null); // natural choice
////        return ($currentRef === $this->dataList->getLastRef());
//    }


    /**
     * This part is needed for the array-part of the avalanche sort. It holds the meta-datas für the the list
     * @param $oddListRange
     * @param $evenListRange
     */
    public function initNewListPart($oddListRange, $evenListRange) // okay
    {
        $this->startOdd = $oddListRange->getStart();
        $this->stopOdd = $oddListRange->getStop();
        $this->startEven = $evenListRange->getStart();
        $this->stopEven = $evenListRange->getStop();
        // the merge-Sort should be slice in priorOddRef and postEvenRef
        $this->priorOddRef = $this->startOdd[ListBase::LIST_PREV];  // can be null
        $this->postEvenRef = $this->startOdd[ListBase::LIST_NEXT]; // cann be null
        $this->startRef = null;
        $this->stopRef = null;
    }

    /**
     * @return stdClass
     */
    public function cascadeDataListChange(DataRangeInterface $resultRange) // okay
    {
        $resultRange->setStart($this->startRef);
        $resultRange->setStop($this->stopRef);
    }


    /**
     * This ist part of the merge-Sort
     *
     * @param $currentRef
     */
    public function addListPart($currentRef)
    {

        if ((null === $currentRef) ||
            (
                ($this->startOdd !== $currentRef) &&
                ($this->startEven !== $currentRef)
            )
        ) {
            throw new UnexpectedValueException(
                'The value of $currentKey in addListPart must be sthe start of the ood-run or even-run. ' .
                'The objekt is in both unknown. This should not happen.',
                1592421675
            );
//            error
        } elseif ($this->startOdd === $currentRef) {
            // integrate Elemet from Odd-Site
            if ($this->startRef === null) {
                // first element in merge
                if ($this->priorOddRef !== null) {
                    $this->priorOddRef[ListBase::LIST_NEXT] = $currentRef;
                    $currentRef[ListBase::LIST_PREV] = $this->priorOddRef;
                } else {
                    $currentRef[ListBase::LIST_PREV] = null;
                }
                $this->startRef = $currentRef;
                $this->priorOddRef = $currentRef;
            } else {
                // secend element in merge // $this->priorOddRef must contain an element
                $this->priorOddRef[ListBase::LIST_NEXT] = $currentRef;
                $currentRef[ListBase::LIST_PREV] = $this->priorOddRef;
                $this->priorOddRef = $currentRef;
            }
            // check, if it was the last Element
            if ($this->startOdd === $this->stopOdd) {
                $this->startOdd = null;
            } else {
                $this->startOdd = $this->startOdd[ListBase::LIST_NEXT];
            }
        } else { // flag for $this->startEven === $currentRef
            if ($this->startRef === null) {
                // first element in merge
                if ($this->priorOddRef !== null) {
                    $this->priorOddRef[ListBase::LIST_NEXT] = $currentRef;
                    $currentRef[ListBase::LIST_PREV] = $this->priorOddRef;
                } else {
                    $currentRef[ListBase::LIST_PREV] = null;
                }
                $this->startRef = $currentRef;
            } else {
                // secend element in merge // $this->priorOddRef must contain an element
                $this->priorOddRef[ListBase::LIST_NEXT] = $currentRef;
                $currentRef[ListBase::LIST_PREV] = $this->priorOddRef;
                $this->priorOddRef = $currentRef;
            }
            // check, if it was the last Element
            if ($this->startEven === $this->stopEven) {
                $this->startEven = null;
            } else {
                $this->startEven = $this->startEven[ListBase::LIST_NEXT];
            }

        }
        $this->stopRef = $currentRef;
        if (($this->startOdd === null) &&
            ($this->startEven === null)
        ) {
            // the last element ist merge in
            $this->stopRef[ListBase::LIST_NEXT] = $this->postEvenRef;
            if ($this->postEvenRef !== null) {
                $this->postEvenRef[ListBase::LIST_PREV]=$this->stopRef;
            }
        }
    }

    /**
     * @param $oddData
     * @param $eventData
     * @return bool
     */
    public function oddLowerEqualThanEven($oddData, $eventData): bool  // okay
    {
        return $this->compareFunc->compare($oddData, $eventData);
    }

    //Quicksortpart

    /**
     * @param $currentRef
     * @return mixed
     */
    public function getPrevIdent($currentRef) // okay
    {
        return $this->dataList->getPrevRef($currentRef);
    } //needed for a generel quicksort

    /**
     * @param $aRef
     * @param $bRef
     */
    public function swap($aRef, $bRef)
    {
        // Is on of the Elements a start-Element or an end-Element
        if ($aRef === $this->firstRef) {
            $this->firstRef = $bRef;
        }
        if ($aRef === $this->lastRef) {
            $this->lastRef = $bRef;
        }
        if ($bRef === $this->firstRef) {
            $this->firstRef = $aRef;
        }
        if ($bRef === $this->lastRef) {
            $this->lastRef = $aRef;
        }
        // change the reference to the neighboour
        $prevA = $aRef[ListBase::LIST_PREV];
        $nextA = $aRef[ListBase::LIST_NEXT];
        $aRef[ListBase::LIST_PREV] = $bRef[ListBase::LIST_PREV];
        $aRef[ListBase::LIST_NEXT] = $bRef[ListBase::LIST_NEXT];
        $bRef[ListBase::LIST_PREV] = $prevA;
        $bRef[ListBase::LIST_NEXT] = $nextA;
        // handle the  references of the neighbours to the changed elements
        if ($aRef[ListBase::LIST_PREV] !== null) {
            $aRef[ListBase::LIST_PREV][ListBase::LIST_NEXT] = $aRef;
        }
        if ($aRef[ListBase::LIST_NEXT] !== null) {
            $aRef[ListBase::LIST_NEXT][ListBase::LIST_PREV] = $aRef;
        }
        if ($bRef[ListBase::LIST_PREV] !== null) {
            $bRef[ListBase::LIST_PREV][ListBase::LIST_NEXT] = $bRef;
        }
        if ($bRef[ListBase::LIST_NEXT] !== null) {
            $bRef[ListBase::LIST_NEXT][ListBase::LIST_PREV] = $bRef;
        }
    } //needed for a generel quicksort

}

   