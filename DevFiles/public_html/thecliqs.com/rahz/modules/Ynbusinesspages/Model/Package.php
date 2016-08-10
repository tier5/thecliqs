<?php

class Ynbusinesspages_Model_Package extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    
    public function getTitle() {
        return $this->title;
    }
    
	public function getPrice(){
		$view = Zend_Registry::get('Zend_View');
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'); 
		return ($view -> locale()->toCurrency($this->price, $currency));
	}
	
    public function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }
	
	public function getAllBusinesses()
	{
		$businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$select = $businessTable -> select() -> where('package_id = ?', $this -> getIdentity());
		return $businessTable -> fetchAll($select);
	}
	
	public function checkHasBusiness(){
		$rows = $this -> getAllBusinesses();
		if(empty($rows))
			return false;
		return true;
	}
    
    public function getAvailableFeatures() {
        $title = array();
        $view = Zend_Registry::get('Zend_View');
        if ($this->allow_owner_manage_page) {
            array_push($title, $view->translate('Allow business owner to manage pages'));
        }
        if ($this->allow_user_join_business) {
            array_push($title, $view->translate('Allow users to join Business'));
        }
        if ($this->allow_user_share_business) {
            array_push($title, $view->translate('Allow users to share Business'));
        }
        if ($this->allow_user_invite_friend) {
            array_push($title, $view->translate('Allow users to invite friends to Business'));
        }
        if ($this->allow_owner_add_contactform) {
            array_push($title, $view->translate('Allow business owner to add contact form'));
        }
        if ($this->allow_owner_add_customfield) {
            array_push($title, $view->translate('Allow business owner to add more custom fields to his Business'));
        }
        if ($this->allow_bussiness_multiple_admin) {
            array_push($title, $view->translate('Allow business to have multiple admins'));
        }
        return $title;
    }
    
    public function getAvailableModules($itemType = NULL) {
    	$moduleTbl = Engine_Api::_() -> getDbTable('modules', 'ynbusinesspages');
        $moduleTblName = $moduleTbl->info('name');
		
        $table = Engine_Api::_()->getDbTable('packagemodules', 'ynbusinesspages');
		$packageModules = $table -> getModuleByPackageId($this->getIdentity());
		
		// get all modules of this package supported
		$module_ids = array();
		foreach ($packageModules as $packageModule) {
			$module_ids[] = $packageModule -> module_id;
		}
		
		//get all item type available
		$item_types = Engine_Api::_() -> getItemTypes();
        
        $select = $moduleTbl->select();
        $select -> from("$moduleTblName", "$moduleTblName.*");
		if($module_ids)
		{
        	$select -> where("$moduleTblName.module_id IN (?)", $module_ids);
		}
		else 
		{
			$select -> where("$moduleTblName.module_id = 0");
		}
		
		if($item_types)
		{
			$select -> where("$moduleTblName.item_type IN (?)", $item_types);
		}
		else 
		{
			$select -> where("$moduleTblName.item_type = ''");
		}
		if(!empty($itemType))
		{
			$select -> where("$moduleTblName.item_type = ?", $itemType);
		}
        return $rows = $moduleTbl->fetchAll($select);
    }

    public function getAvailableTitleModules() {
        $modules = array();
		$view = Zend_Registry::get('Zend_View');
        $rows = $this->getAvailableModules();
        foreach ($rows as $row) {
            array_push($modules, $view -> translate($row->title));
        }
        return $modules;
    }
    
	public function getAvailableTitleCategories() {
		$view = Zend_Registry::get('Zend_View');
        $categoryTbl = Engine_Api::_()->getItemTable('ynbusinesspages_category');
		if (empty($this->category_id)) return array();
		$categories_str = implode(',', $this->category_id);
		$select = $categoryTbl->select()->where('category_id IN (?)', $this->category_id);
		$select -> order(new Zend_Db_Expr("FIELD(category_id, $categories_str)"));
		$rows = $categoryTbl->fetchAll($select);
		$result = array();
		foreach ($rows as $row) {
			$result[] = $view->translate($row->title);
		}
		return $result;
    }
    
    public function checkAvailableModule($itemType) 
    {
        $rows = $this->getAvailableModules($itemType);
        if(count($rows))
			return true;
		else
        	return false;
    }
}
