<?php
class Yncontest_Widget_ListingCompareController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		
					$request = Zend_Controller_Front::getInstance() -> getRequest();
					$params = array();		 		
					
					$entry_id = $request -> getParam('entry_id');
					if(isset($entry_id) && $entry_id!="")
						//$this->view->entries = $entries = Engine_Api::_()->yncontest()->getEntriesById($entry_id);
						$this->view->entries = $entries = Engine_Api::_()->getItemTable('yncontest_entries')->find($entry_id)->current();  	
					//print_r(count($entries));die;						
					if($request->getParam('page'))
						$params['page'] = $request->getParam('page');
					$params['compare'] = 1;	
					$params['entry_id'] = $entry_id;
					$this->view->formValues = $params;
					$this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getEntryPaginator($params);
					$this->view->formValues = array_filter($params);
    	$items_per_page = 5;//Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.listing',12);
    	$this->view->paginator->setItemCountPerPage($items_per_page);
    	if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
		
	}
}
