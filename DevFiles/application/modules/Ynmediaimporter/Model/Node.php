<?php

class Ynmediaimporter_Model_Node extends Core_Model_Item_Abstract
{

    public function getDownloadFilename()
    {
        return $this -> src_big;
    }
    
    public function getUUID(){
        return (microtime(1)*10000) . '_'.$this->node_id .'.jpg';
    }

}
