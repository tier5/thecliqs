<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Statistics.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Api_Statistics extends Core_Api_Abstract
{
	public function getPageObject($data)
	{
		if (is_numeric($data)){
			$pageObject = Engine_Api::_()->getItem('page', (int)$data);
		}elseif ($data instanceof Core_Model_Item_Abstract){
			$pageObject = $data;
		}elseif (is_array($data)){
			$pageObject = Engine_Api::_()->getItem('page', (int)$data['page_id']);
		}else{
			$pageObject = false;
		}
		
		return $pageObject;
	}
	
	public function calculatePercentage($stats, $total)
	{
		if (empty($stats)){
			return false;
		}
		
		foreach ($stats as $key => $stat){
			$stats[$key]['percentage'] = round((($stat['count'] * 1.0) / $total) * 100, 2);
		}
		
		return $stats;
	}
	
	public function getTotalVisitorsCount($page)
	{
		$page = $this->getPageObject($page);
		if (!$page){
			return false;
		}
		
		return $page->getTotalVisitorsCount();
	}
}