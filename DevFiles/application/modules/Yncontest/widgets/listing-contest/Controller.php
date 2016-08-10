<?php
class Yncontest_Widget_ListingContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$orderby = $request->getParam('orderby',"");
		$params = $request->getParams();
		if($orderby)
		{
			$params['orderby'] = $orderby;
			$params['direction'] = 'DESC';
		}
		
		// Process form
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'yncontest')->getContestPaginator($params);
		$items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
	}
}
