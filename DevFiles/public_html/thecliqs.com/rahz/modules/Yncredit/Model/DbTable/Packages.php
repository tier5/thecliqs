<?php
class Yncredit_Model_DbTable_Packages extends Engine_Db_Table
{
  protected $_rowClass = "Yncredit_Model_Package";

  public function getPackages($active = null)
  {
  	$select = $this->select();
	if($active)
	{
		$select -> where ('active = 1');
	}
	$select -> order('credit ASC');
    return $this->fetchAll($select);
  }

  public function checkPackage($values)
  {
    $select = $this->select()
      ->where('price = ?', $values['price'])
      ->orWhere('credit = ?', $values['credit'])
      ->limit(1);
    if (null !== $this->fetchRow($select)) {
      return true;
    } else {
      return false;
    }
  }

  public function getPackage($package_id = null)
  {
    if ($payment_id !== null) {
      $select = $this->select()
        ->where('package_id = ?', $package_id);
    } else {
      $select = $this->select();
    }
    return $this->fetchRow($select);
  }


  public function deletePackage($package_id)
  {
    $this->delete("package_id = {$package_id}");
  }
  
  public function activePackage($package_id, $status)
  {
    $this -> update(array('active' => $status), "package_id = $package_id");
  }
}