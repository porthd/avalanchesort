<?php
namespace Porthd\Avalanchesort\Storage\ArrayType;

use Porthd\Avalanchesort\Defs\DataRangeInterface;

class ArrayIndexDataRange implements DataRangeInterface
{

    protected $start;

    Protected $stop;

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