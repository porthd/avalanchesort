<?php
namespace Porth\Avalanchesort\Storage\NodeType;

use Porth\Avalanchesort\Defs\DataCompareInterface;
use Porth\Avalanchesort\Defs\DataListQuickSortInterface;
use Porth\Avalanchesort\Defs\DataRangeInterface;
use stdClass;
use UnexpectedValueException;

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
class NodeList implements DataListQuickSortInterface
{

    public const NODE_LIST_NEXT_DATA_POINTER = 'next';
    protected $nextName =  self::NODE_LIST_NEXT_DATA_POINTER;



    protected $dataList=null;

    protected $keyList = [];
    protected $firstNode;
    protected $lastNode;
    protected $lengthList = 0;

    protected $holdPartList;
    protected $lastPartInList;
    protected $startPart;
    protected $stopPart;

    /**
     * @var DataCompareInterface null
     */
    protected $compareFunc;

    public function getDataItem($node) {
        if (($node === null) || (empty($node->data))){
            throw new UnexpectedValueException(
                'An Unexpected error. The ident `'.print_r($node,true).
                '` is undefrined or it indicates an undefined value in the datalist.',
                1592951234
            );

        }
        return $node->data;
    }

    /**
     * @return object
     */
    public function getDataList(): object
    {
        return $this->dataList;
    }

    /**
     * @param object $dataList
     * @param DataCompareInterface $compareFunc
     */
    public function setDataList($dataList, DataCompareInterface $compareFunc)
    {
        if ((!is_object($dataList))||
            (empty($dataList)) ||
            (property_exists($dataList,$this->nextName))
        ){
            throw new UnexpectedValueException(
                'The value must be at least an object, which point on the start of the node-list - even if the current type is not checked. '.
                'Theer must at least exist a property `'.$this->nextName.'`.',
                1592421675
            );
        }
        $this->compareFunc = $compareFunc;
        $this->dataList = $dataList;
        $this->firstNode = $dataList;
        $this->lengthList = 1;
        $this->lastNode = $dataList;
        $nextPtr = $this->nextName;
        while (($this->lastNode->$nextPtr !== null) &&
            ($this->lastNode->$nextPtr !== $this->firstNode)
        ) {
            $this->lastNode = $this->lastNode->$nextPtr;
            $this->lengthList++;
        }
    }

    /**
     * @return mixed
     */
    public function getFirstIdent()
    {
        return $this->firstNode;
    }


    /**
     * @param $currentKey
     * @return bool|mixed
     */
    public function getNextIdent($currentKey)
    {
        $next =$this->nextName;
        return $currentKey->$next;
    }

    /**
     * @return mixed
     */
    public function getLastIdent()
    {
        return $this->lastNode;
    }

    /**
     * @param $currentIdent
     * @return bool
     */
    public function isLastIdent($currentIdent): bool
    {
        return ($this->lastNode === $currentIdent);
    }


    /**
     * @param $oddListRange
     * @param $evenListRange
     */
    public function initNewListPart($oddListRange, $evenListRange)
    {
        $this->holdPartList = new stdClass();
        $this->holdPartList = null;
        $this->startPart = null;
        $this->stopPart = null;
    }

    /**
     * @return stdClass
     */
    public function cascadeDataListChange(DataRangeInterface $resultRange)
    {
        $resultRange->setStart( $this->startPart);
        $resultRange->setStop( $this->stopPart);
    }



    /**
     * @param $itemKey
     */
    public function addListPart($itemKey)
    {
        if ($this->startPart === null) {
            $this->startPart = $itemKey;
        } else {
            // reorganize sthe start-parts informations
            // the first element of the list has changed
            if ($itemKey === $this->firstNode) {
                $this->firstNode = $this->startPart;
                $this->dataList = $this->startPart;
            }
        }
        $this->stopPart = $itemKey;
        if ($this->holdPartList === $this->lastNode) {
            // the last element has changed
            $this->lastNode= $itemKey;
        }
        $this->holdPartList = $itemKey;
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
        $next = $this->nextName;
        if ($currentKey === $this->firstNode) {
            $result = false;
        } else if ($currentKey === $this->firstNode->$next) {
            $result = $this->firstNode;
        } else {
            $result = $this->firstNode;
            while ($this->firstNode->$next !== $currentKey) {
                if ($this->firstNode->$next === null) {
                    throw new UnexpectedValueException(
                        'Unexpected errror. The current key copuld not find in the current dataList.',
                        1592421875
                    );
                }
                $result = $result->$next;
            }
        }
        return $result;
   } //needed for a generel quicksort

    //** the trouble-maker for node-lists */
    public function swap($aNode, $bNode){
        $next = $this->nextName;
        // time killer, a double chained list may be better for quicksort
        $prevANode = $this->getPrevIdent($aNode);
        $prevBNode = $this->getPrevIdent($bNode);
        // swap pointer in previious node
        $prevANode->$next = $bNode;
        $prevBNode->$next = $aNode;
        // swap pointer in current node
        $aNode->$next = $bNode->$next;
        $bNode->$next = $aNode->$next;

        // handle the problem with the first element and with dataList
        if ($aNode === $this->firstNode) {
            $this->firstNode = $bNode;
            $this->dataList = $bNode;
        } else if ($bNode === $this->firstNode) { // is this part really needed
            $this->firstNode = $aNode;
            $this->dataList = $aNode;
        }
        if ($aNode === $this->lastNode) { // is this part really needed?
            $this->lastNode = $bNode;
        } else if ($bNode === $this->lastNode) {
            $this->lastNode = $aNode;
        }

    } //needed for a generel quicksort
}

