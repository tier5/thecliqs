<?php
class Yncontest_Widget_ProfileParticipantsCenterController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {
  	$request = Zend_Controller_Front::getInstance() -> getRequest();  	
  	//get object contest
	$contestId = $request->getParam('contestId');		
	if(empty($contestId))
	{			
		return $this->setNoRender();
	}
	$this->view->contest = $contest = Engine_Api::_()->getItem('contest', $contestId);
	$this->view->widgettype = 'participantviewer';
	
			
	$view_participants= $request->getParam('view_participants', 0);
	if(empty($view_participants))	
		return $this->setNoRender();  	
	
	// Get subject and check auth 
	
	$this->view->contest_id = $contest->contest_id;
	//init value from backend widget
	$this->view->limit = $limit = (int)$this->_getParam('number',9);	
	$this->view->height = (int)$this->_getParam('height',50);  	
	$this->view->width = (int)$this->_getParam('width',100);	  	
  	  			
  	$memberSelect = $contest->membership()->getMembersObjectSelect(true);
  	$membershipTbl = new Yncontest_Model_DbTable_Membership();
  	$membershipTblName = $membershipTbl->info('name');   
  	
  	$memberSelect->order("$membershipTblName.creation_date DESC");
  	
	
	$userTbl = Engine_Api::_()->getItemTable('user');
	$this->view->items = $items = $userTbl->fetchAll($memberSelect);  	
  	
	$this->view->paginator = Zend_Paginator::factory($items);
	  	
  	$this->view->paginator -> setItemCountPerPage(1);
  	$this->view->paginator -> setCurrentPageNumber($request->getParam('page'.$this->view->widgettype, 1));
		
	
 	if(count($items)==0) return $this->setNoRender();

  }
}