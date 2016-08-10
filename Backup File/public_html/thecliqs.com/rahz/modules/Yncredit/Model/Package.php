<?php
class Yncredit_Model_Package extends Core_Model_Item_Abstract
{
	protected $_type = 'yncredit_package';

	public function getPrice()
	{
		return $this -> price;
	}

	public function getCredit()
	{
		return $this -> credit;
	}

	public function getPackageParams()
	{
		$params = array();
		$view = Zend_Registry::get('Zend_View');
		// General
		$params['name'] = $view -> translate('Buying %s credits', $this -> getCredit());
		$params['price'] = $this -> price;
		$params['description'] = $view -> translate('Buying Credit from %s', $view -> layout() -> siteinfo['title']);
		$params['vendor_product_id'] = $this -> getGatewayIdentity();
		$params['tangible'] = false;
		$params['recurring'] = false;
		return $params;
	}

	public function getGatewayIdentity()
	{
		return 'yncredit_package_' . $this -> getIdentity() . '_price_' . $this -> price;
	}

	public function isOneTime()
	{
		return true;
	}
}
