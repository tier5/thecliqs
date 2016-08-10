<?php
class Yncontest_AdminManageController extends Core_Controller_Action_Admin
{
	private static $_log;

    /**
     * @return Zend_Log
     */
    public function getLog()
    {
        if (self::$_log == null)
        {
            self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/manage.log'));
        }
        return self::$_log;
    }

    /**
     * write log to temporary/log/manage.log
     * @param string $intro
     * @param string $message
     * @param string $type [Zend_Log::INFO]
     */
    public function log($intro = null, $message, $type)
    {
        return $this -> getLog() -> log(PHP_EOL . $intro . PHP_EOL . $message, $type);
    }
  /**
   * 
   */
  public function indexAction()
  {
   
		$view = Zend_Registry::get('Zend_View');
		$headLink = new Zend_View_Helper_HeadLink();
		$headLink -> appendStylesheet($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/styles/main.css');
  	// Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      					->getNavigation('yncontest_admin_main', array(), 'yncontest_admin_main_manage');	
	
	$this->view->form = $form = new Yncontest_Form_Admin_Search;
    
    $form->isValid($this->_getAllParams());
    $params = $form->getValues();
    
    if(empty($params['orderby'])) $params['orderby'] = 'modified_date';
    if(empty($params['direction'])) $params['direction'] = 'DESC';
    $this->view->formValues = $params;
	
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();		
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $contest = Engine_Api::_()->getItem('yncontest_contest', $value);
			 
          if( $contest ) $contest->delete();
        }
      }
    }
   	$params['admin'] = 1;
   	$params['manage'] = 1;
	
    $this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
   
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page',10);
    $this->view->paginator->setItemCountPerPage($items_per_page);
    if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);						
  }

 /**
  * param contest_id int
  * param type int 2,3,4
  * param service  
  */		
  public function serviceAction(){
	 //Get params
      $contest_id = $this->_getParam('contest_id'); 
      $type = $this->_getParam('type');
	  $service = $this->_getParam('status'); 
		
      //Get contest need to set featured
      $table = Engine_Api::_()->getItemTable('yncontest_contest');
      $select = $table->select()->where("contest_id = ?",$contest_id); 
    	
	  $contest = $table->fetchRow($select);
	  		
      //Set service
      if($contest){
      	switch($type){
			case 2:
				$contest->featured_id =  $service;	
				break;
			case 3:
				$contest->premium_id =  $service;
				break;
			case 4:
				$contest->endingsoon_id =  $service;
				break;
			default:
				break;
      	}
      	
      	$contest->save();
      }
	}
  	
  /**
   * 
   */
  	public function deleteAction()
  	{
	    $form = $this->view->form = new Yncontest_Form_Admin_Delete();
	    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
          	$values = $form->getValues();
	  		$contest_id = $values['contest_id'];
          	$contest = Engine_Api::_()->getItem('yncontest_contest', $contest_id);
		   
		    $this->view->contest_id = $contest->getIdentity();
		    // This is a smoothbox by default
		    if( null === $this->_helper->ajaxContext->getCurrentContext() )
		      $this->_helper->layout->setLayout('default-simple');
		    else // Otherwise no layout
		      $this->_helper->layout->disableLayout(true);
		    		    
			$contest->delete();			  										   
				     
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10, 
					'parentRefresh' => 10, 
					'messages' => array('Delete success!')));
		      
	    }
	    if (!($contest_id = $this->_getParam('id'))) {
      		throw new Zend_Exception('No contest specified');
    	}
		
	    //Generate form
	    $form->populate(array('contest_id' => $contest_id));
	    
	    //Output
	    $this->renderScript('admin-manage/form.tpl');
  	}
  
  	public function deleteSelectedAction() {
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));

		// Save values
		if($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			foreach($ids_array as $id) {
				
				$contest = Engine_Api::_() -> getItem('yncontest_contest', $id);
				
				if($contest) {
					$contest->delete();
				}
			}
			
			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}
		
	}

	public function statisticAction() {
  		$contest_id = $this->_getParam('contest_id');
  		$contest = Engine_Api::_()->getItem('yncontest_contest', contest_id);
  		$this->view->contest = $contest;
  	}	
	
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

		$owner = Engine_Api::_() -> user() -> getUser($contest -> user_id);
		$admin = Engine_Api::_() -> user() -> getUser(1);
		$contest->sendNotMailOwner($owner, $admin, 'contest_approved', 'yncontest_new' );
		
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Approve contest successfully.'))));
	}
	
	public function denyAction() {
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$contest_id = $this -> _getParam('id');
		$contest = Engine_Api::_()->getItem('yncontest_contest', $contest_id);

		$contest->deniedContest();

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Denied contest successfully.'))));
	}
	/* ----- Set Activated Contest Function ----- */
	public function activatedAction() {
		// Get params
		$id = $this->_getParam ( 'contest_id' );
		$is_activated = $this->_getParam ( 'status' );

		// Get contest need to set featured
		$table = Engine_Api::_ ()->getItemTable ( 'yncontest_contest' );
		$select = $table->select ()->where ( "contest_id = ?", $id );
		$contest = $table->fetchRow ( $select );
		
		// Set activi/unactive
		if ($contest) {
			$contest->activated = $is_activated;
			//send not and email with inactivated contests
			if($is_activated)
			{
				$key = 'contest_activated';
				//Your contest has been successfully.
			}	
			else {
				$key = 'contest_inactivated';
			}
			//send notify and email
			$viewer = Engine_Api::_()->user()->getViewer();
			$owner = $contest->getOwner();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($owner, $viewer, $contest, $key);
			
			$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $contest, $key);
			if( $action != null )
			{
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $contest);
			}
				
			$contest->save ();
			
			//set activate/in-activate entry
			$EntryTable = Engine_Api::_() -> getDbTable('entries', 'yncontest');	
			$select = $EntryTable->select()	->where('contest_id = ?' , $contest->getIdentity());			
			$results = $EntryTable->fetchAll($select);
			
			foreach($results as $entry){
				$entry->activated = $is_activated;
				$entry->save();
				//send not and email with inactivated contests
				if($is_activated)
				{
					$key = 'entry_activated';
					//Your contest has been successfully.
				}	
				else {
					$key = 'entry_inactivated';
				}
				//send notify and email				
				$owner = $entry->getOwner();
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($owner, $viewer, $entry, $key);
				
				$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $entry, $key);
				if( $action != null )
				{
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $entry);
				}
			}
			
			
			
		}
	}
	

	
}