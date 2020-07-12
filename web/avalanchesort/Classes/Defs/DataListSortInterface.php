<?php

namespace Porthd\Avalanchesort\Defs;


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
interface DataListSortInterface
{
// isLastIdent, getNextIdent, oddLowerEqualThanEven,getFirstIdent, getLastIdent, initNewListPart, oddLowerEqualThanEven, addListPart, cascadeDataListChange
//

    /**
     * The return-values are used in the compare-version of the sorting-algorithm
     * The function may contain the whole data for the comparsion-process or only a clculated search-order-value, which decide of the position in the sorted list
     *
     * @param $ident
     * @return mixed
     */
    public function getDataItem($ident);

    /**
     * The function returns the whole data-list or only the a reference, which point to the whole data-list.
     * The data-list itself can be any type of datacollection (arry, node-list, ....), which ist defined by the class of this interface
     *
     * @return mixed
     */
    public function getDataList();

    /**
     * The interface define the data-handling for the sorting-algorithm.
     * You have to import the datas to the object. this method handle the data-import.
     * The method of compasion depends on the specific structure. Even if the structure of data-holding (i.e array, nodelist, ..)
     *  is epual, the method of  ordering the ranks between two item differ often.
     *
     * It is recommended, to define getter and setter for the compare-method.
     *
     * @param $dataList
     * @param DataCompareInterface $compareFunc a emthod, which can order two items of the data.
     * @return mixed
     */
    public function setDataList($dataList, DataCompareInterface $compareFunc);

    public function getFirstIdent(); // okay

    public function getNextIdent($ident); //okay

    public function getLastIdent(); // okay

    public function isLastIdent($ident): bool;  // okay

    public function oddLowerEqualThanEven($oddData, $eventData): bool; // olkay


}

