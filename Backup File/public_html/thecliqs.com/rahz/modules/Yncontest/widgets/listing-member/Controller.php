<?php
class Yncontest_Widget_ListingMemberController extends Engine_Content_Widget_Abstract 
{
	public function indexAction(){
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_()->user()->getViewer();
		$params = array();
	    $params['owner_id'] = $viewer->getIdentity();
		$params['manage'] = 1;
		// Process form
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'yncontest')->getContestPaginator($params);
		$items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
	}
}