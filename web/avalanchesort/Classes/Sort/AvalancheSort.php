<?php

namespace Porthd\Avalanchesort\Sort;

use Porthd\Avalanchesort\Defs\DataListAvalancheSortInterface;
use Porthd\Avalanchesort\Defs\DataRangeInterface;
use UnexpectedValueException;


/**
 * AvalancheSort
 */
class AvalancheSort
{

    protected const INSTANCE_FOR_RANGE = DataRangeInterface::class;

    /**
     * @var string
     */
    protected $rangeClass = self::INSTANCE_FOR_RANGE;

    /**
     * @return string
     */
    public function getRangeClass(): string
    {
        return $this->rangeClass;
    }

    /**
     * @param string $rangeClass
     */
    public function setRangeClass(string $rangeClass): void
    {
        $this->rangeClass = $rangeClass;
    }


    /**
     * AvalancheSort constructor.
     * @param $classRangeName
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
     * @param DataListAvalancheSortInterface $dataList
     * @param DataRangeInterface $beginRange
     * @param int $avalancheIndex
     * @return bool
     */
    public function startAvalancheSort(DataListAvalancheSortInterface $dataList)
    {
        $avalancheIndex = 0;
        $rangeResult = $this->avalancheSort($dataList, $dataList->getFirstIdent(), $avalancheIndex);
        if (!$dataList->isLastIdent($rangeResult->getStop())) {
            do {
                $avalancheIndex++;
                $lastIdent = $rangeResult->getStop();
                if ($dataList->isLastIdent($lastIdent)) {
                    $rangeResult = new $this->rangeClass();
                    $rangeResult->setStart($dataList->getFirstIdent());
                    $rangeResult->setStop($dataList->getLastIdent());
                    break;
                }
                $nextIdent = $dataList->getNextIdent($lastIdent);
                $rangeFollow = $this->avalancheSort($dataList, $nextIdent, $avalancheIndex);
                $rangeResult = $this->mergeAvalanche($dataList, $rangeResult, $rangeFollow);
            } while (true);
        }
        return $rangeResult;
    }



//Was sind die Hauptprobleme beim Sortieren
//a) Ich möchte Vorsotierungen ausnutzen. Im Idealfall kann ich  eine vorgegeben Sortierung beibehalten.
// Man glaubt, dass der Vorsortierungsgrad überall ähnlich ist
//b) Der Algorithmus ist sollte die Zahl der Datenbewegungen, die Zahl der Vergleiche und die Zahl der internen Prozesse minimieren, wobei die drei genannten  Operationen je nach Umgebung unterschiedlich teuer sind.
//b) Es stellt normalerweise kein Problem dar, für rekursive Operationen einen Stack zur Verfügung zu stellen.
//c) Wenn ich mit dem Sortieren anfange, weiß ich nicht, wie viel ich sortieren muss. Wenn ich doch Informationen habe, sollten
// Sortiernngen  skalierbar sein.
    /**m
     * @param DataListAvalancheSortInterface $dataList
     * @param DataRangeInterface $range
     * @return DataRangeInterface
     */
    protected function findRun(DataListAvalancheSortInterface $dataList,
                               $startIdent)
    {
        $result = new $this->rangeClass();
        $result->setStart($startIdent);
        $result->setStop($startIdent);
        do {
            if ($dataList->isLastIdent($result->getStop())) {
                break;
            }
            $nextIdent = $dataList->getNextIdent($result->getStop());
            if ($dataList->oddLowerEqualThanEven(
                $dataList->getDataItem($result->getStop()),
                $dataList->getDataItem($nextIdent)
            )) {
                $result->setStop($nextIdent);
            } else {
                break;
            }
        } while (true);
        return $result;
    }

    /**
     * @param DataListAvalancheSortInterface $dataList
     * @param $beginIdent
     * @param int $avalancheIndex
     * @return DataRangeInterface|mixed
     */
    protected function avalancheSort(DataListAvalancheSortInterface $dataList,
                                     $beginIdent,
                                     int $avalancheIndex = 0)
    {
        if ($avalancheIndex === 0) {
            $rangeFirst = $this->findRun($dataList, $beginIdent);
            if ($dataList->isLastIdent($rangeFirst->getStop())) {
                // it is the last run
                $rangeResult = $rangeFirst;
            } else {
                $nextIdent = $dataList->getNextIdent($rangeFirst->getStop());
                $rangeSecond = $this->findRun($dataList, $nextIdent);
                $rangeResult = $this->mergeAvalanche($dataList, $rangeFirst, $rangeSecond);
            }
        } else {
            $rangeFirst = $this->avalancheSort($dataList,
                $beginIdent,
                ($avalancheIndex - 1)
            );
            if ($dataList->isLastIdent($rangeFirst->getStop())) {
                $rangeResult = $rangeFirst;
            } else {
                $nextIdent = $dataList->getNextIdent($rangeFirst->getStop());
                $rangeSecond = $this->avalancheSort($dataList,
                    $nextIdent,
                    ($avalancheIndex - 1)
                );
                $rangeResult = $this->mergeAvalanche($dataList, $rangeFirst, $rangeSecond);
            }
        }
        return $rangeResult;
    }

    protected function mergeAvalanche(DataListAvalancheSortInterface $dataList, $oddListRange, $evenListRange)
    {
        $resultRange = new $this->rangeClass();
        $oddNextIdent = $oddListRange->getStart();
        $oddStopIdent = $oddListRange->getStop();
        $evenNextIdent = $evenListRange->getStart();
        $evenStopIdent = $evenListRange->getStop();
//        echo(
//"liste: ".$oddNextIdent
//.$oddStopIdent
//.$evenNextIdent
//.$evenStopIdent."\r\n"
//        );
        $dataList->initNewListPart($oddListRange, $evenListRange);
        do {
            if ($dataList->oddLowerEqualThanEven(
                $dataList->getDataItem($oddNextIdent),
                $dataList->getDataItem($evenNextIdent)
            )) {
                $dataList->addListPart($oddNextIdent);
                if ($oddNextIdent === $oddStopIdent) {
                    $restTail = $evenNextIdent;
                    $stopTail = $evenStopIdent;
                    break;
                }
                $oddNextIdent = $dataList->getNextIdent($oddNextIdent);
            } else {
                $dataList->addListPart($evenNextIdent);
                if ($evenNextIdent === $evenStopIdent) {
                    $restTail = $oddNextIdent;
                    $stopTail = $oddStopIdent;
                    break;
                }
                $evenNextIdent = $dataList->getNextIdent($evenNextIdent);

            }
        } while (true);
        while ($stopTail !== $restTail) {
            $dataList->addListPart($restTail);
            $restTail = $dataList->getNextIdent($restTail);
        }
        $dataList->addListPart($restTail);
        $dataList->cascadeDataListChange($resultRange);
        return $resultRange;

    }
}

