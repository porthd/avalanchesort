<?php
namespace Porthd\Avalanchesort\Storage\ListType;

use NamespaceCoverageClassExtendedTest;
use Porthd\Avalanchesort\Defs\DataCompareInterface;
use Porthd\Avalanchesort\Defs\DataListQuickSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
use stdClass;
use UnexpectedValueException;

/**
 * This is a hybrid between Array and List
 */
class  ListBase
{

    public const LIST_NEXT = 'next';
    public const LIST_PREV = 'prev';
    public const LIST_VALUE = 'val';
    public const LIST_SELF = 'selfkey';

    protected $list = [];
    protected $firstRef = null;
    protected $lastRef = null;

    public function getFirstRef(){
        return $this->firstRef;
    }

    public function getPrevRef($ref){
        return $ref[self::LIST_PREV];
    }

    public function getNextRef($ref){
        return $ref[self::LIST_NEXT];
    }

    public function getLastRef(){
        return $this->lastRef;
    }

    public function getArrayByList()
    {
        $next = $this->getFirstRef();
        $result = [];
        while ($next !== null) {
            $result[$next[self::LIST_SELF]] = $next[self::LIST_VALUE];
            $next = $next[self::LIST_NEXT];
        }
        return $result;
    }

    public function getArrayByArray()
    {
        $result = [];
        foreach($this->list as $key => $item){
            $result[$key] = $item;
        }
        return $result;
    }

    public function getValueByNode($node)
    {
        return $node[self::LIST_VALUE];
    }

    public function getValueByKey($Key)
    {
        return $this->list[$Key][self::LIST_VALUE];
    }

    public function addLast($value, $key = null)
    {
        if ($key === null) {
            $myKey = count($this->list);
            $this->list[$myKey] = [
                self::LIST_SELF => $myKey,
                self::LIST_NEXT => null,
                self::LIST_PREV => $this->lastRef,
                self::LIST_VALUE => $value,
            ];

        } else if ((is_string($key)) && (!isset($this->list[$key]))) {
            $myKey = $key;
            $this->list[$myKey]=  [
                self::LIST_SELF => $myKey,
                self::LIST_NEXT => null,
                self::LIST_PREV => $this->lastRef,
                self::LIST_VALUE => $value,
            ];
        } else {
            throw new UnexpectedValueException(
                'Value not defined or key exist already',
                123456789
            );
        }
        if($this->lastRef !== null) {
            $this->lastRef =  &$this->list[$myKey];
            $this->lastRef[self::LIST_NEXT] = $this->lastRef;
        } else {
            $this->lastRef =  &$this->list[$myKey];
        }
        if ($this->firstRef === null) {
            $this->firstRef = &$this->list[$myKey];
        }
    }

    public function addFirst($value, $key = null)
    {
        if ($key === null) {
            $item = [
                self::LIST_NEXT => $this->firstRef,
                self::LIST_PREV => null,
                self::LIST_VALUE => $value,
            ];
            array_unshift($this->list, $item);
            // must renumber the current state
            foreach($this->list as $key => $item) {
                $this->list[$key][ListBase::LIST_SELF]=$key;
            }
            $myKey = 0;
        } else if ((is_string($key)) && (!isset($this->list[$key]))) {
            $item =  [
                self::LIST_SELF => $key,
                self::LIST_NEXT => $this->firstRef,
                self::LIST_PREV => null,
                self::LIST_VALUE => $value,
            ];
            $myKey = $key;
            $this->list = [$myKey => $item] + $this->list;
        } else {
            throw new UnexpectedValueException(
                'Value not defined or key exist already',
                123456789
            );
        }
        if($this->firstRef !== null) {
            $this->firstRef =  &$this->list[$myKey];
            $this->firstRef[self::LIST_PREV] = $this->firstRef;
        } else {
            $this->firstRef =  &$this->list[$myKey];
        }
        if ($this->lastRef === null) {
            $this->lastRef = &$this->list[$myKey];
        }
    }

    public function synchronizeListgAndNaturalOrder()
    {
        $result = [];
        $step = $this->firstRef;
        $last = null;
        $first = null;
        while($step !== null) {
            if ($last === null) {
            $result[$step[ListBase::LIST_SELF]] = [
                ListBase::LIST_SELF => $step[ListBase::LIST_SELF],
                ListBase::LIST_NEXT => null,
                ListBase::LIST_PREV => null,
                ListBase::LIST_VALUE => $step[ListBase::LIST_SELF],
                ];
            } else {
                $result[$step[ListBase::LIST_SELF]] = [
                    ListBase::LIST_SELF => $step[ListBase::LIST_SELF],
                    ListBase::LIST_NEXT => null,
                    ListBase::LIST_PREV => $last,
                    ListBase::LIST_VALUE => $step[ListBase::LIST_SELF],
                ];
                $last[ListBase::LIST_NEXT] = &$result[$step[ListBase::LIST_SELF]];
            }
            $last = &$result[$step[ListBase::LIST_SELF]];
            if ($first === null) {
                $first = &$result[$step[ListBase::LIST_SELF]];
            }
        }
        return  $result;
    }
}

   