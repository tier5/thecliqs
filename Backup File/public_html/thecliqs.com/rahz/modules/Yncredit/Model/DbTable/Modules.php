<?php
class Yncredit_Model_DbTable_Modules extends Engine_Db_Table
{
  protected $_rowClass = "Yncredit_Model_Module";

  public function getModulesDisabled($level_id)
  {
    $select = $this->select() -> from($this->info('name'), new Zend_Db_Expr("name"))
      -> where('level_id = ?', $level_id);
    return $this->fetchAll($select);
  }
  
  public function getModuleDisabled($name, $level_id)
  {
  	$select = $this->select()
      -> where('level_id = ?', $level_id)
      -> where('name = ?', $name);
    return $this->fetchRow($select);
  }
}