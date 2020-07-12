<?php
namespace Porthd\Avalanchesort\Defs;



interface DataListQuickSortInterface extends DataListSortInterface
{
// isLastIdent, getNextIdent, oddLowerEqualThanEven,getFirstIdent, getLastIdent, initNewListPart, oddLowerEqualThanEven, addListPart, cascadeDataListChange
//

     public function getPrevIdent($ident); //needed for a generel quicksort
     public function getMiddleIdent(DataRangeInterface $oddListRange); // = $ident needed for a generel quicksort
     public function getRandomIdent(DataRangeInterface $oddListRange); // = $ident needed for a generel quicksort
     public function swap($identA, $identB); //needed for a generel quicksort


}

