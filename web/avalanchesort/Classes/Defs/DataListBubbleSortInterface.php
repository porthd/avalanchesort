<?php
namespace Porthd\Avalanchesort\Defs;



interface DataListBubbleSortInterface extends DataListSortInterface
{

    public function swap($identA, $identB); //needed for a quicksort

}

