<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Locations.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Locations extends Engine_Db_Table
{
	public function getCountries($params)
	{
		return $this->getPaginator($params);
	}
	
	public function getPaginator($params)
	{
		if (!empty($params['ips'])){
			$select = $this->getSelect($params['ips']);
		}else {
			return null;
		}
		
		$paginator = Zend_Paginator::factory($select);
		
		if (!empty($params['ipp'])){
			$paginator->setItemCountPerPage($params['ipp']);
		}
		
		if (!empty($params['p'])){
			$paginator->setCurrentPageNumber($params['p']);
		}
		
		return $paginator;
	}
	
	public function getSelect($ips)
	{
		$where = $this->_buildConditions($ips);
		if ($where){
			$select = $this->select()->where($where)->group('country');
			return $select;
		}
		
		return false;
	}
	
	public function _buildConditions($ips)
	{
		$ips = (array)$ips;
		if (empty($ips)){
			return false;
		}
		
		$ips = array_map(array($this, '_buildCondition'), $ips);
		
		return implode(' AND ', $ips);
	}
	
	public function _buildCondition($ip)
	{
		return "begin_num <= $ip AND end_num >= $ip";
	}
	
	public function getCountry($ip)
	{
		if (!$ip){
			return false;
		}
		
		$where = $this->_buildCondition($ip);
		$select = $this->select()
      ->where($where)
      ->group('country');
		
		return $this->fetchRow($select);
	}
}