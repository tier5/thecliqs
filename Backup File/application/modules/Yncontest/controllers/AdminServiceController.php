<?php
class Yncontest_AdminServiceController extends Core_Controller_Action_Admin
{
  /**
   * 
   */
	public function indexAction()
	  {
	  	// Get navigation bar
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	      					->getNavigation('yncontest_admin_main', array(), 'yncontest_admin_main_service');	
		
		$this->view->form = $form = new Yncontest_Form_Admin_Search;
	    
	    $form->isValid($this->_getAllParams());
	   $params = $form->getValues();
	    
	    if(empty($params['orderby'])) $params['orderby'] = 'start_date';
	    if(empty($params['direction'])) $params['direction'] = 'DESC';
	  //  $this->view->formValues = $params;
	    if ($this->getRequest()->isPost()) {
	      $values = $this->getRequest()->getPost();
	      foreach ($values as $key => $value) {
	        if ($key == 'delete_' . $value) {
	          $idea = Engine_Api::_()->getItem('yncontest_contest', $value);          
	          if( $idea ) $idea->delete();
	        }
	      }
	    }
	   	$params['admin'] = 1;
		$params['service'] = 1;
	   	if(empty($params['orderby'])) $params['orderby'] = 'modified_date';
	    if(empty($params['direction'])) $params['direction'] = 'DESC';
	    $this->view->formValues = $params;
	    $this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
	   
	    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page',10);
	    $this->view->paginator->setItemCountPerPage($items_per_page);
	    if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);						
	  }

	/**
	 * 
	 */
	public function approveAction() {
		
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$contest_id = $this -> _getParam('id');
		$contest = Engine_Api::_()->getItem('yncontest_contest', $contest_id);
		
		if(Engine_Api::_()->yncontest()->checkLiveService($contest_id,2))
			$contest->featuredContest();
		if(Engine_Api::_()->yncontest()->checkLiveService($contest_id,3))
			$contest->premiumContest();
		if(Engine_Api::_()->yncontest()->checkLiveService($contest_id,4))
			$contest->endingContest();
		
		
		$contest->approve_status = 'approved';
		$contest->contest_status = 'published';
		$contest->approved_date = date('Y-m-d H:i:s');
		$contest->save();
		
		$contest->approveTranByContest();
		
		
		
		$params["contest_link"] = $contest->getHref();
		$user = Engine_Api::_() -> user() -> getUser($contest -> user_id);
		
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		// send mail
		Engine_Api::_() -> getApi('mail', 'yncontest') -> send($contest -> contest_email, 'contest_approved', $params);
		// add notification
		$notifyApi -> addNotification($user, $viewer, $contest, 'contest_approved');
		//add action
		$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $contest, 'yncontest_new');
		if( $action != null )
		{
			Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $contest);	
		}
		
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Approve transaction successfully.'))));
	}
	
	public function denyAction() {
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$contest_id = $this -> _getParam('id');
		$contest = Engine_Api::_()->getItem('yncontest_contest', $contest_id);

		$contest->deniedContest();
		
		
		
		$contest->denyTranByContest();		

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Denied transaction successfully.'))));
	}
	
	public function denySelectedAction() {
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));
		// Save values
		if($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			foreach($ids_array as $id) {
				$product = Engine_Api::_() -> getItem('social_product', $id);
				if($product) {
					$product -> deleted = 1;
					$product->save();
					$params = $product -> toArray();
					
				}
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}

	}
}