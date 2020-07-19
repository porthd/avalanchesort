<?php

namespace Porthd\Avalanchesort\Sort;

use Porthd\Avalanchesort\Defs\DataListQuickSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
use UnexpectedValueException;

class QuickSort
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
    public function qsortStart(DataListQuickSortInterface $dataList)
    {
        $dataFullRange = new $this->rangeClass();
        $dataFullRange->setStart(
            $dataList->getFirstIdent()
        );
        $dataFullRange->setStop(
            $dataList->getLastIdent()
        );
        $this->qsort($dataList, $dataFullRange);
        return $dataFullRange;
    }


    function qsort(DataListQuickSortInterface $dataList,
                   ?DataRangeInterface $dataPartRange = null)
    {
        if ($dataPartRange === null) {
            return true;
        }
        $pivot = $dataPartRange->getStart();

        $right = $dataPartRange->getStop();

        //              $HelpData = $dataList->getDataItem($pivot);
        //        fwrite(STDERR, 'range: '.print_r($pivot, TRUE).' - '.print_r($right, TRUE).' pivot:'.$HelpData['test']."\n");

        if ($pivot === $right) { // one element in range
            return true;
        } elseif (($nextLeft = $dataList->getNextIdent($pivot)) === $right) { // two element in range
            if (($pivot !== $right) &&
                (
                !$dataList->oddLowerEqualThanEven(
                $dataList->getDataItem($pivot),
                $dataList->getDataItem($right)
            ))) {
                $dataList->swap($pivot, $right);
            }
            return true;
        } else { // more than two elements in range

            $leftRange = new $this->rangeClass();
            $leftRange->setStart($pivot);
            $leftRange->setStop($pivot);
            $nextLeft = $dataList->getNextIdent($pivot);
            $rightRange = new $this->rangeClass();
            $rightRange->setStart($right);
            $rightRange->setStop($right);
            $prevRight = $right;
            // at beginn is defined: $nextLeft !==  prevRight
            do {
                while (($nextLeft !== $prevRight) &&
                    ($dataList->oddLowerEqualThanEven(
                        $dataList->getDataItem($nextLeft),
                        $dataList->getDataItem($pivot)
                    ))
                ) {
                    $nextLeft = $dataList->getNextIdent($nextLeft);
                }
                while (($nextLeft !== $prevRight) &&
                    ($dataList->oddLowerEqualThanEven(
                        $dataList->getDataItem($pivot),
                        $dataList->getDataItem($prevRight)
                    ))
                ) {
                    $prevRight = $dataList->getPrevIdent($prevRight);
                }
                if ($nextLeft !== $prevRight) {
                    $dataList->swap($nextLeft, $prevRight);
                    // Are $nextLeft and $prevRight neighbours with no element between?
                    if ($nextLeft === ($prevRight = $dataList->getPrevIdent($prevRight))) {
                        // no more elements to swap
                        $leftRange->setStop($nextLeft); // Range does not contain Pivot-element
                        $rightRange->setStart($dataList->getNextIdent($prevRight));
                        break;
                    // Are $nextLeft and $prevRight neighbours with one(!) element between?
                    } else if (($nextLeft = $dataList->getNextIdent($nextLeft)) === $prevRight) {
                        // make a last compare
                        if (!$dataList->oddLowerEqualThanEven(
                            $dataList->getDataItem($pivot),
                            $dataList->getDataItem($prevRight)
                        )) {
                            if ($pivot !== $prevRight) {
                                $dataList->swap($pivot, $prevRight);  // PrevRight contains Pivot-Element
                            }
                            $leftRange->setStop($prevRight); // Range does not contain Pivot-element
                            $rightRange->setStart($dataList->getNextIdent($prevRight));
                        } else {
                            $stopLeft = $dataList->getPrevIdent($nextLeft);
                            $leftRange->setStop($stopLeft); // Range does not contain Pivot-element
                            $rightRange->setStart($prevRight);
                        }
                        break;
                    }   // else there is perhaps more to swap?
                } else { // ($nextLeft === $prevRight)
                    if (!$dataList->oddLowerEqualThanEven(
                        $dataList->getDataItem($pivot),
                        $dataList->getDataItem($nextLeft)
                    )) {
                        if ($pivot !== $nextLeft) {
                            $dataList->swap($pivot, $nextLeft); // NextLeft contains Piovot-Element
                        }
                        $stopLeft = ($nextLeft !== $leftRange->getStart()) ? $dataList->getPrevIdent($nextLeft) :$nextLeft;
                        $leftRange->setStop($stopLeft); // Range does not contain Pivot-element
                        $startRight = ($nextLeft !== $right)?$dataList->getNextIdent($nextLeft):$right;
                        $rightRange->setStart($startRight);
                    } else {
                        $stopLeft = $dataList->getPrevIdent($nextLeft);
                        $startRight = $nextLeft;
                        if ($pivot !== $stopLeft) {
                            $dataList->swap($pivot, $stopLeft); // NextLeft contains Piovot-Element
                        }
                        $stopLeft = ($stopLeft !== $leftRange->getStart()) ?
                            $dataList->getPrevIdent($stopLeft) :
                            $stopLeft;
                        $leftRange->setStop($dataList->getPrevIdent($nextLeft)); // Range does not contain Pivot-element
                        $rightRange->setStart($startRight);

                    }
                    break;
                }
            } while (true);
            $this->qsort($dataList, $leftRange);
            $this->qsort($dataList, $rightRange);
        }
    }

    /**
     * @param DataListQuickSortInterface $dataList
     * @param DataRangeInterface $dataPartRange
     * @return mixed
     */
    public function getPivotData(DataListQuickSortInterface $dataList,
                                 DataRangeInterface $dataPartRange
    )
    {
        // the determination of the pivot is not optimal for normal sorting-situations
        // think about sorted array or list with many elemnt of equal ordering
        // for list with many different elements in randomized order, this no-good solution will cause no trouble.
        return $dataList->getDataItem($dataPartRange->getStop());
    }
}

