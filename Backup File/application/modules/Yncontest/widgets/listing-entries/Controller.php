<?php
class Yncontest_Widget_ListingEntriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
			
		if (Zend_Registry::isRegistered('entries_search_params')) {
			$values = Zend_Registry::get('entries_search_params');
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
		$this->view->items_per_page = $items_per_page;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$values['page'] = $request -> getParam('page');
 		$this -> view -> user_id = $user_id = $viewer -> getIdentity();
		 		
 		$contest_id = $request->getParam('contestId');
 		if($contest_id!= null)
 			$values['contest_id'] = $contest_id;		
 		if($request->getParam('status')!="")
			$values['status'] = $request->getParam('status'); 		

		$this->view->formValues = $values;
		$this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getEntryPaginator($values);
		$paginator->setItemCountPerPage($items_per_page);
		$this->view->values = $values;
		if($paginator -> getTotalItemCount())
			$this -> setNoRender();
	}
}