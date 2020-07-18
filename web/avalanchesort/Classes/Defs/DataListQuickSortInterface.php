<?php
namespace Porthd\Avalanchesort\Defs;



interface DataListQuickSortInterface extends DataListSortInterface
{

     public function getPrevIdent($ident); //needed for a generel quicksort to make the change-in-place-feature possible
     public function getMiddleIdent(DataRangeInterface $oddListRange); // = $ident needed for some variations of quicksort to detect the pivot-element
     public function getRandomIdent(DataRangeInterface $oddListRange); // = $ident needed for some variations of quicksort to detect the pivot-element

     public function swap($identA, $identB); //needed for a generel quicksort and bubblesort


}

