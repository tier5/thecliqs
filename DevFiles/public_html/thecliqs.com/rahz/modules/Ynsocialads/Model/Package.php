<?php

class Ynsocialads_Model_Package extends Core_Model_Item_Abstract
{
	protected $_type = 'ynsocialads_package';
	protected $_serializedColumns = array('module');
	protected $_owner_type = 'user';

	public function isOneTime()
	{
		return true;
	}

	public function getPrice()
	{
		return $this -> price;
	}

	public function getPackageParams($arr = array())
	{
		$params = array();
		$view = Zend_Registry::get('Zend_View');
		// General
		$params['name'] = $view -> translate('Buying Ad');
		if (!empty($arr['ad_id']) && $ad = Engine_Api::_() -> getItem('ynsocialads_ad', $arr['ad_id']))
		{
			$params['price'] = $total_pay = ($ad -> benefit_total * ($this -> price / $this -> benefit_amount));
		}
		else
		{
			$params['price'] = $this -> price;
		}
		$params['description'] = $view -> translate('Buying Ad from %s', $view -> layout() -> siteinfo['title']);
		$params['vendor_product_id'] = $this -> getGatewayIdentity($arr['ad_id']);
		$params['tangible'] = false;
		$params['recurring'] = false;
		return $params;
	}

	public function getGatewayIdentity($adId = 0)
	{
		$price = 0;
		$ad = Engine_Api::_() -> getItem('ynsocialads_ad', $adId);
		if ($ad)
			$price = ($ad -> benefit_total * ($this -> price / $this -> benefit_amount));
		if (!$adId)
		{
			$price = $this -> getIdentity();
		}
		return 'ynsocialads_ads_' . $adId . '_fee_' . $price;
	}

	function isViewable()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity() == $this -> getOwner() -> getIdentity())
		{
			return true;
		}
		$table = Engine_Api::_() -> getDbtable('levels', 'authorization');
		$level = $table -> fetchRow($table -> select() -> where('level_id = ?', $viewer -> level_id));
		$auth = Engine_Api::_() -> authorization() -> context;
		return $auth -> isAllowed($this, $level, 'view');
	}

	public function getAllModuleNames()
	{
		$modulesTbl = Engine_Api::_() -> getDbtable('modules', 'core');
		$tblName = $modulesTbl -> info('name');
		$select = $modulesTbl -> select() -> from($tblName, 'title');
		if (count($this -> modules))
		{
			$result = $modulesTbl -> fetchAll($select -> where('name IN (?)', $this -> modules));
			$moduleNameArr = array();
			foreach ($result as $value)
			{
				$moduleNameArr[] = $value['title'];
			}
			return $moduleNameArr;
		}
		else
		{
			return array();
		}
	}

	public function getAllModules()
	{
		$modulesTbl = Engine_Api::_() -> getDbtable('modules', 'ynsocialads');
		$tblName = $modulesTbl -> info('name');
		$select = $modulesTbl -> select() -> from($tblName, array(
			'module_id',
			'module_title'
		));
		if (count($this -> modules))
		{
			$result = $modulesTbl -> fetchAll($select -> where('module_name IN (?)', $this -> modules));
			return $result -> toArray();
		}
		else
		{
			return array();
		}
	}

	public function getAllPlacements()
	{
		$view = Zend_Registry::get('Zend_View');
		$placementArr = array(
			'middle_top' => $view -> translate('Middle Ads Top'),
			'middle_bottom' => $view -> translate('Middle Ads Bottom'),
			'left_top' => $view -> translate('Left Column Ads Top'),
			'left_bottom' => $view -> translate('Left Column Ads Bottom'),
			'right_top' => $view -> translate('Right Column Ads Top'),
			'right_bottom' => $view -> translate('Right Column Ads Bottom'),
		);
		$placements = array();
		$adblocks = $this -> getAllAdBlocks();
		foreach ($adblocks as $adblock)
		{
			if (empty($placementArr) && $placements['footer'])
				break;
			if (intval($adblock -> page_id) == 2)
			{
				$placements['footer'] = 'Footer Ads';
				continue;
			}
			if (in_array($adblock -> placement, array_keys($placementArr)))
			{
				$placements[$adblock -> placement] = $placementArr[$adblock -> placement];
				unset($placementArr[$adblock -> placement]);
			}
		}
		return $placements;
	}

	public function getAllPages($placement)
	{
		if ($placement == 'footer')
		{
			return array();
		}
		$adblocks = $this -> getAllAdBlocks();
		$pageArr = array();
		foreach ($adblocks as $adblock)
		{
			if ($adblock -> placement == $placement)
			{
				array_push($pageArr, array(
					'page_id' => $adblock -> page_id,
					'block_id' => $adblock -> adblock_id,
					'displayname' => $this -> getPageName($adblock -> page_id)
				));

			}
		}

		return $pageArr;
	}

	public function getPageName($page_id)
	{
		$pageTable = Engine_Api::_() -> getDbtable('pages', 'core');
		$page = $pageTable -> fetchRow($pageTable -> select() -> where('page_id = ?', $page_id));
		if ($page)
		{
			return $page -> displayname;
		}
		elseif (Engine_Api::_() -> hasModuleBootstrap('ynsocialadspage'))
		{
			$proxyTable = Engine_Api::_() -> getDbtable('proxies', 'ynsocialadspage');
			$pageSelect = $proxyTable -> select() -> where('page_id = ?', $page_id);
			$page = $proxyTable -> fetchRow($pageSelect);
			if ($page)
			{
				return $page -> title;
			}
			else
			{
				return "";
			}
		}
		else
		{
			return "";
		}
	}

	public function getAllAdBlocks()
	{
		$packageblocksTbl = Engine_Api::_() -> getItemTable('ynsocialads_packageblock');
		$packageblocksTblName = $packageblocksTbl -> info('name');
		$adblocks = $packageblocksTbl -> fetchAll($packageblocksTbl -> select() -> from($packageblocksTblName, 'block_id') -> where('package_id = ?', $this -> package_id)) -> toArray();
		if (empty($adblocks))
		{
			return array();
		}
		$adblockTbl = Engine_Api::_() -> getItemTable('ynsocialads_adblock');
		$adblocksData = $adblockTbl -> fetchAll($adblockTbl -> select() -> where('adblock_id IN (?)', $adblocks));
		return $adblocksData;
	}

}
