<?php
class Yncontest_Widget_ProfileParticipantsController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {  	
  	if( !Engine_Api::_()->core()->hasSubject()) {
  		return $this->setNoRender();
  	}
	$request = Zend_Controller_Front::getInstance() -> getRequest();		
	$view_participants= $request->getParam('view_participants', 0);
	if(!empty($view_participants))	
		return $this->setNoRender();  	
	
	// Get subject and check auth 
	$contest = Engine_Api::_()->core()->getSubject();
	
	if( $contest instanceof Yncontest_Model_Entry)
	{			
		$contest = Engine_Api::_()->getItem('contest', $contest->contest_id);
	}
	$this->view->contest_id = $contest->contest_id;
	//init value from backend widget
	$this->view->limit = $limit = (int)$this->_getParam('number',16);	  	
  			
  	$memberSelect = $contest->membership()->getMembersObjectSelect(true);
	
  	$membershipTbl = new Yncontest_Model_DbTable_Membership();
  	$membershipTblName = $membershipTbl->info('name');   
  	
  	$memberSelect->order("$membershipTblName.creation_date DESC");
  	
	
	$userTbl = Engine_Api::_()->getItemTable('user');
	$this->view->items = $items = $userTbl->fetchAll($memberSelect);  	
  
 	if(count($items)==0) return $this->setNoRender();
	
  	if(count($items)>$limit)
			$this->view->canViewmore = true;
  }
}