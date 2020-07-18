<?php

namespace Porthd\Avalanchesort\Sort;

use Porthd\Avalanchesort\Defs\DataListBubbleSortInterface;
use Porthd\Avalanchesort\Defs\DataListQuickSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
use UnexpectedValueException;

class BubbleSort
{

    protected const INSTANCE_FOR_RANGE = DataRangeInterface::class;

    /**
     * @var string
     */
    protected $rangeClass = self::INSTANCE_FOR_RANGE;


    /**
     * AvalancheSort constructor.
     * @param string $classRangeName
     * @param string $rangeClass
     * @param $listClass
     */
    public function __construct($classRangeName)
    {
        $this->rangeClass = $classRangeName;
        if (array_search(self::INSTANCE_FOR_RANGE, class_implements($this->rangeClass)) === false) {
            throw new UnexpectedValueException(
                'the class implements not the estimated interface. ',
                1592662983
            );
        }
    }

    /**
     * @param DataListQuickSortInterface $dataList
     */
    public function bubbleSortStart(DataListBubbleSortInterface $dataList)
    {
        $dataFullRange = new $this->rangeClass();
        $dataFullRange->setStart(
            $dataList->getFirstIdent()
        );
        $dataFullRange->setStop(
            $dataList->getLastIdent()
        );
        $this->bubbleSort($dataList, $dataFullRange);
        return $dataFullRange;
    }


    function bubbleSort(DataListBubbleSortInterface $dataList,
                   ?DataRangeInterface $dataPartRange = null)
    {
        if ($dataPartRange === null) {
            return true;
        }

        // see https://de.wikipedia.org/wiki/Bubblesort // https://en.wikipedia.org/wiki/Bubble_sort 20200718
        $start = $dataPartRange->getStart();
        $stop = $dataPartRange->getStop();
        if ($start === $stop) {
            return true;  // only one element => sortig solved
        } else {

            do {
                $left = $start;
                $nextStop = $start;
                $flag = false;
                do {
                    $nextLeft = $dataList->getNextIdent($left);
                    if (!$dataList->oddLowerEqualThanEven(
                        $dataList->getDataItem($left),
                        $dataList->getDataItem($nextLeft)
                    )) {
                        $dataList->swap($left, $nextLeft);  // PrevRight contains Pivot-Element
                        // detect the position of last change
                        $nextStop = $left;
                        $flag = true;
                    }
                } while ($nextLeft !== $stop);
                if ($nextStop === $start) {
                    $flag = false; // nothing more to sort
                } else {
                    // reduce the range of sorting, because the highest ranked item are bubbled to the top
                    $stop = $nextStop;
                }
            } while ($flag === true) ;
        }
    }
}

