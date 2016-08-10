<?php
class Ynsocialads_Model_DbTable_Ads extends Engine_Db_Table
{
	protected $_rowClass = 'Ynsocialads_Model_Ad';

	public function getAdsSelect($params = array())
	{
		$tableAdsTable = Engine_Api::_() -> getItemTable('ynsocialads_ad');
		$tableAdsName = $tableAdsTable -> info('name');

		$tableUserTable = Engine_Api::_() -> getDbtable('users', 'user');
		$tableUserName = $tableUserTable -> info('name');

		$tableCampaignTable = Engine_Api::_() -> getDbtable('campaigns', 'ynsocialads');
		$tableCampaignName = $tableCampaignTable -> info('name');

		$select = $tableAdsTable -> select() -> from(array('ads' => $tableAdsName));
		$select -> setIntegrityCheck(false) -> joinLeft("$tableUserName as user", "user.user_id = ads.user_id", '') -> joinLeft("$tableCampaignName as campaign", "campaign.campaign_id = ads.campaign_id", '');

		if (!empty($params['name']))
		{
			$select -> where('ads.name LIKE ? OR campaign.title LIKE ? OR user.displayname LIKE ?', '%' . $params['name'] . '%', "%" . $params['name'] . "%", "%" . $params['name'] . "%");
		}
		if (!empty($params['campaign_id']))
		{
			$select -> where('ads.campaign_id = ?', $params['campaign_id']);
		}
		if (!empty($params['status']) && $params['status'] != 'all')
		{
			$select -> where('ads.status = ?', $params['status']);
		}
		if (!empty($params['user_id']))
		{
			$select -> where('ads.user_id = ?', $params['user_id']);
		}
		if (!empty($params['type']) && $params['type'] != 'all')
		{
			$select -> where('ads.ad_type = ?', $params['type']);
		}
		if (empty($params['direction']))
		{
			$params['direction'] = 'DESC';
		}
		if (!empty($params['order']))
		{
			$select -> order($params['order'] . ' ' . $params['direction']);
		}
		else
		{
			$select -> order('ads.ad_id DESC');
		}
		return $select;
	}

	public function getAdsPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getAdsSelect($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		return $paginator;
	}

	public function countAdsPackage($package)
	{
		$select = $this -> select();
		$select -> from($this, array('count(*) as amount'));
		$select -> where('package_id = ?', $package);
		$rows = $this -> fetchAll($select);
		return ($rows[0] -> amount);

	}

	public function getAdsRender($params, $id, $is_login)
	{
		//check hidden
		$arr_owner_id = array();
		$arr_ad_id = array();
		$hiddenTable = Engine_Api::_() -> getItemTable('ynsocialads_hidden');
		if ($is_login == 'yes')
		{
			$select = $hiddenTable -> select() -> where('user_id = ?', $id);
			$rows = $hiddenTable -> fetchAll($select);
			foreach ($rows as $row)
			{
				if ($row['type'] == 'owner')
				{
					$arr_owner_id[] = $row['id'];
				}
				elseif ($row['type'] == 'ad')
				{
					$arr_ad_id[] = $row['id'];
				}
			}
		}
		elseif ($is_login == 'no')
		{
			$select = $hiddenTable -> select() -> where('IP = ?', $id);
			$rows = $hiddenTable -> fetchAll($select);
			foreach ($rows as $row)
			{
				if ($row['type'] == 'owner')
				{
					$arr_owner_id[] = $row['id'];
				}
				elseif ($row['type'] == 'ad')
				{
					$arr_ad_id[] = $row['id'];
				}
			}
		}

		$tableAdsTable = Engine_Api::_() -> getItemTable('ynsocialads_ad');
		$tableAdsName = $tableAdsTable -> info('name');

		$tableMappingsTable = Engine_Api::_() -> getItemTable('ynsocialads_mapping');
		$tableMappingsName = $tableMappingsTable -> info('name');

		$select = $this -> select() -> from(array('ads' => $tableAdsName));
		$select -> setIntegrityCheck(false) -> joinLeft("$tableMappingsName as mapping", "mapping.ad_id = ads.ad_id", '');
		$select -> where("ads.status = 'running'");
		$select -> where("ads.ad_type <> 'feed'");
		if (!empty($params['content_id']))
		{
			$select -> where('mapping.content_id = ?', $params['content_id']);
		}
		if (!empty($arr_owner_id))
		{
			$select -> where('ads.user_id NOT IN (?)', $arr_owner_id);
		}
		if (!empty($arr_ad_id))
		{
			$select -> where('ads.ad_id NOT IN (?)', $arr_ad_id);
		}
		if (!empty($params['limit']))
		{
			$select -> limit($params['limit']);
		}
		return $this -> fetchAll($select);
	}

}
