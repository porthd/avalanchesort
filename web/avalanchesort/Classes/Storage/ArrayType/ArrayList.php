<?php
namespace Porthd\Avalanchesort\Storage\ArrayType;

use http\Exception\UnexpectedValueException;
use Porthd\Avalanchesort\Defs\DataCompareInterface;
use Porthd\Avalanchesort\Defs\DataListAvalancheSortInterface;
use Porthd\Avalanchesort\Defs\DataListQuickSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
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
class ArrayList implements DataListQuickSortInterface
{
    protected $dataList = [];

    protected $keyList = [];
    protected $oldNewPartList = [];
    protected $firstKey = 0;
    protected $lastKey = 0;
    protected $lengthList = 0;
    protected $unusedKey = -1;

    /**
     * @var DataCompareInterface null
     */
    protected $compareFunc;

    public function getDataItem($ident) {
        if (!isset($this->dataList[$ident])){
            throw new \UnexpectedValueException(
                'An Unexpected error. The ident `'.print_r($ident,true).
                '` is undefrined or it indicates an undefined value in the datalist.',
                1592951234
            );

        }
        return $this->dataList[$ident];
    }

    /**
     * @return array
     */
    public function getDataList(): array
    {
        return $this->dataList;
    }

    /**
     * @param array $dataList
     * @param DataCompareInterface $compareFunc
     */
    public function setDataList($dataList, DataCompareInterface $compareFuncClass)
    {
        if ((!is_array($dataList)) ||
            (empty($dataList))
        ){
            throw new \UnexpectedValueException(
                'The value must be an array with at least one item.',
                1592421675
            );
        }
        $this->compareFunc = $compareFuncClass;
        $this->keyList = array_keys($dataList);
        $this->lengthList = count($this->keyList);
        $this->firstKey = $this->keyList[0];
        $this->lastKey = $this->keyList[($this->lengthList - 1)];
        $this->dataList = $dataList;
        // detect an unused key for th key-management of the keysorting
        $this->unusedKey = $this->lengthList;
        do {
            $flag = false;
            foreach ($this->keyList as $item) {
                if ($item === $this->unusedKey) {
                    $this->unusedKey++;
                    $flag = true;
                }
            }
        } while ($flag);
    }

    /**
     * @return mixed
     */
    public function getFirstIdent()
    {
        return $this->firstKey;
    }


    /**
     * @param $currentKey
     * @return bool|mixed
     */
    public function getNextIdent($currentKey)
    {
        if (($nextKey = array_search($currentKey, $this->keyList))=== false) {
            throw new \UnexpectedValueException(
                'Unexpected errror. The current key is not part of the data-list used by getNextIdent.'.
                "\n".print_r($currentKey,true)."\n".print_r($this->keyList,true),
                1592421875
            );
        }
        $nextKey++;
        return (($nextKey < $this->lengthList) ?
            $this->keyList[$nextKey] :
            false
        );

    }


    /**
     * @param DataRangeInterface $oddListRange
     * @return mixed
     */
    public function getMiddleIdent(DataRangeInterface $oddListRange)
    {
        if ((!($startStartKey = array_search($oddListRange->getStart(), $this->keyList))) &&
            (!($stopStopKey = array_search($oddListRange->getStop(), $this->keyList))) &&
            ($startStartKey <= $stopStopKey)
        ){
            throw new \UnexpectedValueException(
                'Unexpected errror. The one of the current keys `'.$oddListRange->getStart().
                '` or `'.$oddListRange->getStop().'`in the range seems to be undefined. Or the order in the keylist '.
                'between'.' start `'.$startStartKey.'` and stop `'.$stopStopKey.'` is wrong. ',
                1593539824
            );
        }
        $middleMiddleKdey = floor(($startStartKey+$stopStopKey)/2);
        return $this->keyList[$middleMiddleKdey];
    }


    /**
     * @param DataRangeInterface $oddListRange
     * @return mixed
     */
    public function getRandomIdent(DataRangeInterface $oddListRange)
    {
        if ((!($startStartKey = array_search($oddListRange->getStart(), $this->keyList))) &&
            (!($stopStopKey = array_search($oddListRange->getStop(), $this->keyList))) &&
            ($startStartKey <= $stopStopKey)
         ){
            throw new \UnexpectedValueException(
                'Unexpected errror. The one of the current keys `'.$oddListRange->getStart().
                '` or `'.$oddListRange->getStop().'`in the range seems to be undefined. Or the order in the keylist '.
                'between'.' start `'.$startStartKey.'` and stop `'.$stopStopKey.'` is wrong. ',
                1593539824
            );
        }
        $middleMiddleKdey = random_int($startStartKey,$stopStopKey);
        return $this->keyList[$middleMiddleKdey];
    }

    /**
     * @return mixed
     */
    public function getLastIdent()
    {
        return $this->lastKey;
    }

    /**
     * @param $currentIdent
     * @return bool
     */
    public function isLastIdent($currentIdent): bool
    {
        return ($this->lastKey === $currentIdent);
    }

    /**
     * use the keylist to build an cascade-swap with data-rotation beween n elements
     * @return stdClass
     */
    public function cascadeDataListChange(DataRangeInterface $result)
    {

        $lengthOld = count($this->oldNewPartList['old']);
        $lengthNew = count($this->oldNewPartList['new']);
        if ($lengthNew !== $lengthOld) {
            throw new \UnexpectedValueException(
                'Unexpected errror. The current length of the list of keys for the original parts is not equal to length of the list of keys for the merge parts.',
                1592421875
            );
        }
        if ($lengthNew > 1){
            $startKeyKey = 0;
            while ($lengthNew > 1) {
    //            for($i = $startKeyKey; $i < $lengthOld; $i++){
    //                if ($this->oldNewPartList['new'][$i] !== $this->unusedKey) {
    //                    $item = $this->oldNewPartList['new'][$i];
    //
    //                    $lastKey = $item;
    //                    $startKeyKey = $i;
    //                    if ($i<$lengthOld) {
    //                        break 1;
    //                    } else {
    //                        break 2;
    //                    }
    //                }
    //            }
    //            $cascadeStorage = $this->dataList[$item];
                foreach ($this->oldNewPartList['new'] as  $item) {
                    if ($item !== $this->unusedKey) {
                        $startKey = $item;
                        $lastKey = $startKey;
                        break 1; // for
                    }
                }
                $cascadeStorage = $this->dataList[$startKey];
                do {
                    $nextKeyKey = array_search($lastKey, $this->oldNewPartList['old']);
                    $nextKey = $this->oldNewPartList['new'][$nextKeyKey];
                    $lengthNew--;
                    if ($nextKey === $startKey) {
                        $this->dataList[$lastKey]=$cascadeStorage;
                        $this->oldNewPartList['new'][$nextKeyKey] = $this->unusedKey;
                        $flagMoreChanges = false;
                    } else if ($nextKey === $this->unusedKey) {
                        throw new \UnexpectedValueException(
                            'Unexpected errror. This should never happen, because the cycle of swap should everytime closed.',
                            1592421875
                        );
                    } else {
                        $this->dataList[$lastKey]=$this->dataList[$nextKey];
                        $this->oldNewPartList['new'][$nextKeyKey] = $this->unusedKey;
                        $lastKey = $nextKey;
                        $flagMoreChanges = true;
                    }
                } while ($flagMoreChanges);

            }
        }
        $result->setStart($this->oldNewPartList['old'][0]);
        $result->setStop($this->oldNewPartList['old'][($lengthOld-1)]);
    }

    /**
     * @param $oddListRange
     * @param $evenListRange
     */
    public function initNewListPart($oddListRange, $evenListRange)
    {
        $startIndex = array_search($oddListRange->getStart(), $this->keyList);
        $stopIndex = array_search($oddListRange->getStop(), $this->keyList);
        $oddLength =  $stopIndex - $startIndex + 1;
        $oddKeyList = array_slice($this->keyList, $startIndex, $oddLength);
        $startIndex = array_search($evenListRange->getStart(), $this->keyList);
        $stopIndex = array_search($evenListRange->getStop(), $this->keyList);
        $evenLength =  $stopIndex - $startIndex + 1;
        $evenKeyList = array_slice($this->keyList, $startIndex, $evenLength);
        if ((!is_array($oddKeyList)) ||
            (!is_array($evenKeyList))
        )  {
            throw new \UnexpectedValueException(
                'Unexpected errror. One of the key-arrays is an array. This should not happen. check teh ranges-Definitions',
                1594461885
            );
        }

        $this->oldNewPartList['old'] = array_merge($oddKeyList, $evenKeyList); // merge arrays
        $this->oldNewPartList['new'] = [];
    }



    /**
     * @param $itemKey
     */
    public function addListPart($itemKey)
    {
        $this->oldNewPartList['new'][] = $itemKey;
    }

    /**
     * @param $oddData
     * @param $eventData
     * @return bool
     */
    public function oddLowerEqualThanEven($oddData, $eventData): bool
    {
        return $this->compareFunc->compare($oddData, $eventData);
    }

    //Quicksortpart

    public function getPrevIdent($currentKey) {
        if (($prevKey = array_search($currentKey, $this->keyList)) === false) {
            throw new \UnexpectedValueException(
                'Unexpected errror. The current key is not part of the data-list used by getPrevIdent.'.
                "\n".print_r($currentKey,true)."\n".print_r($this->keyList,true),
                1592421875
            );
        }
        $prevKey--;
        return (($prevKey >=0) ?
            $this->keyList[$prevKey] :
            false
        );


    } //needed for a generel quicksort

    public function swap($aKey, $bKey){
        $swapItem = $this->dataList[$aKey];
        $this->dataList[$aKey] = $this->dataList[$bKey];
         $this->dataList[$bKey] = $swapItem;
        fwrite(STDERR, print_r('swap :'.$aKey.'/'.$this->dataList[$bKey]['test'].' - '.$bKey .'/'.$this->dataList[$aKey]['test']."\n", TRUE));

    } //needed for a generel quicksort
}

