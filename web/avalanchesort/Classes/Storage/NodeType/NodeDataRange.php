<?php
namespace Porth\Avalanchesort\Storage\NodeType;

use Porth\Avalanchesort\Defs\DataRangeInterface;

class NodeDataRange implements DataRangeInterface
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