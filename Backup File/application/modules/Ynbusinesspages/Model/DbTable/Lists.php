<?php
class Ynbusinesspages_Model_DbTable_Lists extends Engine_Db_Table
{
	protected $_rowClass = 'Ynbusinesspages_Model_List';
	protected $_serializedColumns = array('privacy');
	
	public function getListAssocForClone($business)
	{
		$businessId = 0;
		if(is_object($business))
		{
			$businessId = $business -> getIdentity();
		}
		else if(is_numeric($business))
		{
			$businessId = (int)$business;
			$business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
		}
		if ($businessId)
		{
			$select = $this->select()->where("owner_id = ? ", $businessId);
			$lists = $this->fetchAll($select);
			$result = array();
			$result[0] = Zend_Registry::get("Zend_Translate")->_("None");
			foreach ($lists as $list) 
			{
				if ($list -> type != 'non-registered')
				{
					$result[$list->list_id] = $list->name;
				}
			}
			return $result;
		}
		return array();
	}
	
	public function getListAssocByBusiness($business, $withNone = true)
	{
		$businessId = 0;
		if(is_object($business))
		{
			$businessId = $business -> getIdentity();
		}
		else if(is_numeric($business))
		{
			$businessId = (int)$business;
			$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $businessId);
		}
		if ($businessId)
		{
			$select = $this->select()->where("owner_id = ? ", $businessId);
			$roles = $this->fetchAll($select);
			$result = array();
			if ($withNone)
			{
				$result[0] = Zend_Registry::get("Zend_Translate")->_("None");
			}
			foreach ($roles as $role) 
			{
				$package = $business -> getPackage();
				if($package -> getIdentity()) 
				{
					if($package -> allow_bussiness_multiple_admin)
					{
						$result[$role->list_id] = $role->name;
					}
					else
					{
						if($role -> type != 'admin')
							$result[$role->list_id] = $role->name;
					}
				}
				else
				{
					if($role -> type != 'admin')
						$result[$role->list_id] = $role->name;
				}
			}
			return $result;
		}
		return array();
	}
	
	public function getListByBusiness($business, $insertData = true)
	{
		$businessId = 0;
		if(is_object($business))
		{
			$businessId = $business -> getIdentity();
		}
		else if(is_numeric($business))
		{
			$businessId = (int)$business;
		}
		if ($businessId)
		{
			$select = $this->select()->where("owner_id = ? ", $businessId);
			$roles = $this->fetchAll($select);
			if ($insertData)
			{
				if (count($roles) == 0)
				{
					$this->insertSampleData($business);
					$roles = $this->fetchAll($select);
				}
			}
			return $roles;
		}
		return array();
	}
	
	public function insertSampleData($business)
	{
		$businessId = 0;
		if(is_object($business))
		{
			$businessId = $business -> getIdentity();
		}
		else if(is_numeric($business))
		{
			$businessId = (int)$business;
			$business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
		}
		if ($businessId)
		{
			$listItemTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list_item');
			
			//DEFAULT ADMIN ROLE
			$aRow = $this->createRow();
			$aRow->owner_id = $businessId;
			$aRow->name = 'Admin';
			$aRow->can_delete = 0;
			$aRow->can_edit = 0;
			$aRow->privacy = $this->getDefaultPrivacy('admin');
			$aRow->type = 'admin';
			$adminRoleId = $aRow->save();
			
			//DEFAULT MEMBER ROLE
			$mRow = $this->createRow();
			$mRow->owner_id = $businessId;
			$mRow->name = 'Member';
			$mRow->can_delete = 0;
			$mRow->can_edit = 0;
			$mRow->privacy = $this->getDefaultPrivacy('member');
			$mRow->type = 'member';
			$memberRoleId = $mRow->save();
			
			//DEFAULT REGISTERED MEMBER ROLE
			$rRow = $this->createRow();
			$rRow->owner_id = $businessId;
			$rRow->name = 'Registered User';
			$rRow->can_delete = 0;
			$rRow->can_edit = 0;
			$rRow->privacy = $this->getDefaultPrivacy('registered');
			$rRow->type = 'registered';
			$registerRoleId = $rRow->save();
			
			//DEFAULT REGISTERED MEMBER ROLE
			$nRow = $this->createRow();
			$nRow->owner_id = $businessId;
			$nRow->name = 'Non-Registered User';
			$nRow->can_delete = 0;
			$nRow->can_edit = 0;
			$nRow->privacy = array("view" => 1);
			$nRow->type = 'non-registered';
			$nonRegisterRoleId = $nRow->save();
			
			//ADD BUSINESS OWNER TO ADMIN
			if (!$business->is_claimed)
			{
				$businessOwner = $business -> getOwner();
				$aRow -> add($businessOwner);
				$business -> membership() -> addMember($businessOwner) -> setUserApproved($businessOwner) -> setResourceApproved($businessOwner);
				$business -> membership() -> getMemberInfo($businessOwner) -> setFromArray(array('list_id' => $adminRoleId)) -> save();	
			}			
		}
	}
	
	public function getPrivacyKey($type = null)
	{
		/**
		 * Enter description here ...
		 * @todo get enabled modules belong the package
		 */
		$moduleSettingTbl = Engine_Api::_()->getDbTable('modulesettings', 'ynbusinesspages');
		$select = $moduleSettingTbl -> select();
		if (!is_null($type)){
			$select -> where("`{$type}` > 0");
		}
		$privacyKey = array();
		foreach ($moduleSettingTbl->fetchAll($select) as $row)
		{
			if (Engine_Api::_()->hasModuleBootstrap($row->module_name))
			{
				$privacyKey[] = $row -> key;
			}
		}
		$privacyKey = array_unique($privacyKey);
		return $privacyKey;
	}
	
	public function getDefaultPrivacy($type = 'member')
	{
		$privacy = array();
		$allowed = $this->getPrivacyKey($type);
		
		$moduleSettingTbl = Engine_Api::_()->getDbTable('modulesettings', 'ynbusinesspages');
		foreach ($moduleSettingTbl->fetchAll($moduleSettingTbl -> select()) as $row)
		{
			if (in_array($row->key, $allowed))
			{
				if ($row->edit_or_delete == '1' && $type == 'admin')
				{
					$privacy[$row->key] = 2;	
				}
				else 
				{
					$privacy[$row->key] = 1;
				}
			}
		}
		return $privacy; 
	}
	
	public function getListByUser($user, $business)
	{
		$itemTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list_item');
		$itemTblName = $itemTbl -> info('name');
		$listTblName = $this -> info('name');
		
		$select = $this
		->select()->setIntegrityCheck(false)
		->from($listTblName)
		->join($itemTblName, "{$listTblName}.list_id = {$itemTblName}.list_id AND {$listTblName}.owner_id = {$business->getIdentity()} AND {$itemTblName}.child_id = {$user->getIdentity()}")
		->limit(1)
		;
		return $this->fetchRow($select);
	}
}