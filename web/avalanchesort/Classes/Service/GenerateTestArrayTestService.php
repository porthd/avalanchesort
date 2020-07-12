<?php

namespace Porth\Avalanchesort\Service;


class GenerateTestArrayTestService
{
    public const TESTLIST_KEY_KEY = 'origKey';
    public const TESTLIST_KEY_POS = 'origPos';

    public const TESTLIST_SHUFFLE_KEY = 'shuffledKey';
    public const TESTLIST_SHUFFLE_POS = 'shuffledPos';

    public const TESTLIST_NEXT_POS = 'nextElem';
    public const TESTLIST_PREW_POS = 'prewElem';

    protected $keyForTest = 'test';

    public function __construct($keyForTest = 'test') {
        $this->keyForTest = $keyForTest;
    }


    public function generateListRandomSimpleArray($length = 10000, $flagAssoc = false)
    {
        $result = [];
        $keyKey = 'origKey';
        $maxValue = (10 * $length);
        $i = 0;
        do {
            $item = [
                $this->keyForTest => rand(0, $maxValue),
                self::TESTLIST_KEY_KEY => ($flagAssoc ? md5('No' . $i) : $i),
                self::TESTLIST_KEY_POS => $i,
            ];
            $result[$item[$keyKey]] = $item;
            $i++;
        } while (count($result) < $length);
        return $result;
    }

    public function generateListSortedSimpleArray($length = 10000, $flagAssoc = false, $flagReverse = false)
    {
        $result = [];
        $i = 0;
        do {
            $assocKey = ($flagAssoc ? md5('No' . $i) : $i);
            $item = [
                $this->keyForTest => ($flagReverse ? $length - $i : $i),
                self::TESTLIST_KEY_KEY => $assocKey,
                self::TESTLIST_KEY_POS => $i,
                self::TESTLIST_SHUFFLE_KEY =>  $i,
                self::TESTLIST_SHUFFLE_POS => $i,
//                self::TESTLIST_NEXT_POS => null,
//                self::TESTLIST_PREW_POS => null,
            ];
            if ($flagAssoc) {
                $result[$assocKey] = $item;
            } else {
                $result[] = $item;
            }
            $i++;
        } while (count($result) < $length);

//        $first = null;
//        $prev = null;
//        foreach($result as $key => $item){
//            if ($first === null) {
//                $prev = &$result[$key];
//                $first =  &$result[$key];
//            } else {
//                $result[$key][self::TESTLIST_PREW_POS] = $prev;
//                $prev[self::TESTLIST_NEXT_POS] = &$result[$key];
//                $prev = &$result[$key];
//            }
//        }
        return $result;
    }

    public function generateListSingleElementArray( $flagAssoc = false)
    {
        return $this->generateListSortedSimpleArray(1, $flagAssoc, false);
    }

    public function generateListTupleElementsArray($length = 2, $flagAssoc = false)
    {
        return $this->generateListSortedSimpleArray($length, $flagAssoc, false);
    }

    public function generateListAntisortedTupelElementsArray( $length = 2, $flagAssoc = false)
    {
        return $this->generateListSortedSimpleArray($length, $flagAssoc, true);
    }


    public function shuffleArrayForSorting($testArray, $stepRatio = 0.0001, $nextStartRatio = 0.0001, $swapRatio = 2.0)
    {
        $length = count($testArray);
        $currentKeyKey = 0;
        $maxStep = (int)(floor($stepRatio * $length) + 17);
        $nextStart = (int)(floor($nextStartRatio * $length) + 19);
        $maxSwap =(int)( floor($swapRatio * $length) + 1);
        $keyListe = array_keys($testArray);
        $i = 0;
        do {
            $nextKeyKey = ($currentKeyKey + random_int(1, $maxStep) )% $length ;

            $currentKey = $keyListe[$currentKeyKey];
            $nextKey = $keyListe[$nextKeyKey];

            $swap = $testArray[$currentKey];
            $testArray[$currentKey] = $testArray[$nextKey];
            $testArray[$nextKey] = $swap;

            $currentKeyKey = ($currentKeyKey + random_int(1, $nextStart))% $length ;
            $i++;
        } while ($i <= $maxSwap);
        return $this->arragneResortArray($testArray);

        return $result;
    }

    public function isArraySorted($testList)
    {
        $flag = true;
        $keyList = array_keys($testList);
        $countFails = 0;
        foreach ($testList as $key => $item) {
            $pos = array_search($key, $keyList);
            if (($item[self::TESTLIST_KEY_KEY] !== $key) ||
                ($pos !== $item[self::TESTLIST_KEY_POS])
            ) {
                $flag = false;
                $countFails++;
            }

        }
        return [$flag, $countFails, ($countFails/count($testList))];
    }

    public function isArrayDissorted($testList)
    {
        $flag = false;
        $keyList = array_keys($testList);
        $countFails = 0;
        foreach ($testList as $key => $item) {
            $pos = array_search($key, $keyList);
            if (($item[self::TESTLIST_SHUFFLE_KEY] !== $key) ||
                ($pos !== $item[self::TESTLIST_SHUFFLE_POS])
            ) {
                $flag = true;

            }
            if (($item[self::TESTLIST_KEY_KEY] === $item[self::TESTLIST_SHUFFLE_KEY]) &&
                ($item[self::TESTLIST_SHUFFLE_POS] === $item[self::TESTLIST_KEY_POS])
            ) {
                $countFails++;
            }

        }
        return [$flag, $countFails, ($countFails/count($testList))];
    }

    /**
     * @param $testArray
     * @return array
     */
    public function arragneResortArray($testArray): array
    {
// resort shuffled array
        $result = [];
        $i = 0;
        foreach ($testArray as $key => $item) {
            $item[self::TESTLIST_SHUFFLE_KEY] = $key;
            $item[self::TESTLIST_SHUFFLE_POS] = $i;
            $result[] = $item;
            $i++;
        }
        return $result;
    }

}

