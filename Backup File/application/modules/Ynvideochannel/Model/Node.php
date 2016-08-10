<?php

class Ynvideochannel_Model_Node extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    public function delete() {
        return $this -> _table -> deleteNode($this);
    }

    /**
     * get chilren node
     *
     */
    public function getChilren(){
        $table =  $this->_table;
        $select = $table->select()->where('parent_id = ?', $this->getIdentity()) -> order('order ASC');
        return $table->fetchAll($select);
    }

    public function getBreadCrumNode(){
        $table =  $this->_table;
        $select = $table->select()->where('pleft < ?', $this->pleft)->where('pright > ?',$this->pright) -> order('level');
        return $table->fetchAll($select);
    }

    /**
     * get chilren node
     *
     */
    public function countChildren(){
        $table =  $this->_table;
        $select = $table->select()->where('parent_id = ?', $this->getIdentity());
        return count($table->fetchAll($select));
    }

    public function getDescendent($include_own =true){
        return $this->_table->getDescendent($this, $include_own);
    }

}
