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
interface DataListAvalancheSortInterface extends DataListSortInterface
{

    public function initNewListPart(DataRangeInterface $oddListRange, DataRangeInterface $evenListRange); // okay

    public function addListPart($ident); // okay

    public function cascadeDataListChange(DataRangeInterface $resultRange); // okay


}

