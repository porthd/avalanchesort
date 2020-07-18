<?php
namespace Porthd\Avalanchesort\Defs;



interface DataListQuickSortInterface extends DataListSortInterface
{

     public function getPrevIdent($ident); //needed for a generel quicksort to make the change-in-place-feature possible

     public function swap($identA, $identB); //needed for a generel quicksort and bubblesort


}

