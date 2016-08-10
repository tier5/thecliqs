<?php

class Ynbusinesspages_Model_DbTable_Membership extends Core_Model_DbTable_Membership
{
	protected $_type = 'ynbusinesspages_business';


	// Configuration

	/**
	 * Does membership require approval of the resource?
	 *
	 * @param Core_Model_Item_Abstract $resource
	 * @return bool
	 */
	public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource)
	{
		return $resource->approval;
	}
	
	public function _checkActive(Core_Model_Item_Abstract $resource, User_Model_User $user)
	{
		parent::_checkActive($resource, $user);
		$row = $this->getRow($resource, $user);
		if ($row->active == 1)
		{
			$row->actived_date = date('Y-m-d H:i:s');
			$row->save();
			if ($row -> list_id)
			{
				$memberList = Engine_Api::_()->getItem('ynbusinesspages_list', $row -> list_id);
				if (!is_null($memberList))
				{
					$viewer = Engine_Api::_()->user()->getViewer();
					if (!$memberList -> has($viewer))
					{
						$memberList -> add($viewer);	
					}	
				}
			}
		}
	}
}