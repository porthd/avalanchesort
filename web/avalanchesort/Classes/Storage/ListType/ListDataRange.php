<?php
namespace Porthd\Avalanchesort\Storage\ListType;

use Porthd\Avalanchesort\Defs\DataRangeInterface;

class ListDataRange implements DataRangeInterface
{

    protected $start = null;

    Protected $stop = null;

    public function getStart(){
        return $this->start;
    }
    public function setStart($start)
    {
        $this->start = $start;
    }
    public function getStop()
    {
        return $this->stop;
    }
    public function setStop($stop){
        $this->stop = $stop;
    }

}