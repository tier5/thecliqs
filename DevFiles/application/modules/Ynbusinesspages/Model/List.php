<?php
class Ynbusinesspages_Model_List extends Core_Model_List
{
	  protected $_owner_type = 'ynbusinesspages_business';
	  protected $_child_type = 'user';
	  protected $_searchTriggers = false;
	  
	  public function getListItemTable()
	  {
			return Engine_Api::_()->getItemTable('ynbusinesspages_list_item');
	  }
	  
	  public function add(Core_Model_Item_Abstract $child, $params = array())
	  {
	  		try 
	  		{
	  			parent::add($child, $params);
	  		} 
	  		catch (Exception $e) {}
	  }
	  
	  public function remove(Core_Model_Item_Abstract $child)
	  {
	  		try 
	  		{
	  			parent::remove($child);
	  		} 
	  		catch (Exception $e) {}
	  }
}