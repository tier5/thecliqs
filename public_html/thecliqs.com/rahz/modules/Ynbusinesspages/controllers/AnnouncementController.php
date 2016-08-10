<?php
class Ynbusinesspages_AnnouncementController extends Core_Controller_Action_Standard {
   
   public function init() {
		
		if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
		{
			Engine_Api::_() -> core() -> setSubject($business);
		}
		$this -> _helper -> requireSubject('ynbusinesspages_business');
	}
   
   public function manageAction()
   {
   		
	  	$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->business = $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam("business_id"));
		$this -> _helper -> content -> setEnabled();
		//check if member can manage announcement
		
		if(!$business->isAllowed('manage_announcement'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		if(!$business)
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$viewer || (!$business->isOwner($viewer))) 
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$this->view->formFilter = $formFilter = new Ynbusinesspages_Form_Announcement_Filter();
		$page = $this->_getParam('page', 1);
		$table = Engine_Api::_()->getItemtable('ynbusinesspages_announcement');
		if($formFilter->isValid($this->_getAllParams())) 
		{
			$values = $formFilter->getValues();
			$select = $table->select()->where('business_id = ?', $business->getIdentity())->order( !empty($values['orderby']) ? $values['orderby'].' '.$values['orderby_direction'] : 'announcement_id DESC' )->limit(10);
			$paginator = Zend_Paginator::factory($select);
			if ($values['orderby'] && $values['orderby_direction'] != 'DESC') 
			{
				$this->view->orderby = $values['orderby'];
			}
		} 
		else 
		{
			$select = $table->select()->order( 'announcement_id DESC' )->limit(10);
			$paginator = Zend_Paginator::factory($select);
		}
		
		$this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

 public function createAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam("business_id"));
	
	//check if member can manage announcement
	$allow_manage = true;
	if(!$business->isAllowed('manage_announcement'))
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
    
    if(!$business)
	{
		return $this->_helper->requireSubject()->forward();
	}
	
	if(!$viewer || (!$business->isOwner($viewer) && !$allow_manage)) 
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
	
    $this->view->form = $form = new Ynbusinesspages_Form_Announcement_Create();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) 
    {
      $params = $form->getValues();
      $params['business_id'] = $this->_getParam('business_id');
      $announcement = Engine_Api::_()->getItemtable('ynbusinesspages_announcement')->createRow();
      $announcement->setFromArray($params);
      $announcement->save();
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  public function editAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam("business_id"));
	
	//check if member can manage announcement
	$allow_manage = true;
	if(!$business->isAllowed('manage_announcement'))
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
    
    if(!$business)
	{
		return $this->_helper->requireSubject()->forward();
	}
	
	if(!$viewer || (!$business->isOwner($viewer) && !$allow_manage)) 
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
	
    $id = $this->_getParam('id', null);
    $announcement = Engine_Api::_()->getItem('ynbusinesspages_announcement', $id);

    $this->view->form = $form = new Ynbusinesspages_Form_Announcement_Edit();
    $form->populate($announcement->toArray());

    // Save values
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $params = $form->getValues();
      $announcement->setFromArray($params);
      $announcement->save();
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam("business_id"));
	
	//check if member can manage announcement
	$allow_manage = true;
	if(!$business->isAllowed('manage_announcement'))
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
    
    if(!$business)
	{
		return $this->_helper->requireSubject()->forward();
	}
	
	if(!$viewer || (!$business->isOwner($viewer) && !$allow_manage)) 
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
	
    $this->view->id = $id = $this->_getParam('id', null);
    $announcement = Engine_Api::_()->getItem('ynbusinesspages_announcement', $id);

    // Save values
    if( $this->getRequest()->isPost() )
    {
      $announcement->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Announcement is deleted successfully.')
      ));
    }
  }

  public function deleteselectedAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam("business_id"));
	
	// check if member can manage announcement
	$allow_manage = true;
	if(!$business->isAllowed('manage_announcement'))
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
	
    if(!$business)
	{
		return $this->_helper->requireSubject()->forward();
	}
	
	if(!$viewer || (!$business->isOwner($viewer) && !$allow_manage)) 
	{
		return $this -> _helper -> requireAuth() -> forward();
	}
	
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $announcement = Engine_Api::_()->getItem('ynbusinesspages_announcement', $id);
        if( $announcement ) $announcement->delete();
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }
  
  public function markAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');  
    $this->view->form = $form = new Ynbusinesspages_Form_Announcement_Mark();
		
	if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
	
	$params = $this -> _getAllParams();
	$params['user_id'] = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
	$result = Engine_Api::_() -> getDbtable('marks', 'ynbusinesspages') -> markAnnouncement($params);
	
	$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $params['business_id']);
	return $this -> _forward('success', 'utility', 'core', array(
		'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Announcement has been marked as read.')),
		'layout' => 'default-simple',
		'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
			'id' => $business -> getIdentity(),
		), 'ynbusinesspages_profile', true),
		'closeSmoothbox' => true,
	));
  } 
}
?>
