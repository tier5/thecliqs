<?php

class Ynjobposting_JobsController extends Core_Controller_Action_Standard {
    public function indexAction() {
    	return $this->_helper->redirector->gotoRoute(array(), 'ynjobposting_general', true);
    }
	
	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynjobposting_general', true), 'messages' => array($message)));
	}
	
	public function composeMessageAction()
	{
		// Make form
		$this->view->form = $form = new Messages_Form_Compose();
		$form -> setDescription('')->setAttrib('class', 'global_form ynjobposting_messages_compose');
		//$form->setAction($this->view->url(array('to' => null, 'multi' => null)));

		// Get params
		$multi = $this->_getParam('multi');
		$to = $this->_getParam('to');
		$viewer = Engine_Api::_()->user()->getViewer();
		$toObject = null;

		// Build
		$isPopulated = false;
		if( !empty($to) && (empty($multi) || $multi == 'user') ) {
			$multi = null;
			// Prepopulate user
			$toUser = Engine_Api::_()->getItem('user', $to);
			/*
			$isMsgable = ( 'friends' != Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ||
			$viewer->membership()->isMember($toUser) );
			*/
			$isMsgable = true;
			if( $toUser instanceof User_Model_User &&
			(!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
			isset($toUser->user_id) &&
			$isMsgable ) {
				$this->view->toObject = $toObject = $toUser;
				$form->toValues->setValue($toUser->getGuid());
				$isPopulated = true;
			} else {
				$multi = null;
				$to = null;
			}
		} else if( !empty($to) && !empty($multi) ) {
			// Prepopulate group/event/etc
			$item = Engine_Api::_()->getItem($multi, $to);
			// Potential point of failure if primary key column is something other
			// than $multi . '_id'
			if( $item instanceof Core_Model_Item_Abstract &&
			$item->getIdentity() && (
			$item->isOwner($viewer) ||
			$item->authorization()->isAllowed($viewer, 'edit')
			)) {
				$this->view->toObject = $toObject = $item;
				$form->toValues->setValue($item->getGuid());
				$isPopulated = true;
			} else {
				$multi = null;
				$to = null;
			}
		}
		$this->view->isPopulated = $isPopulated;

		// Build normal
		if( !$isPopulated ) {
			// Apparently this is using AJAX now?
			//      $friends = $viewer->membership()->getMembers();
			//      $data = array();
			//      foreach( $friends as $friend ) {
			//        $data[] = array(
			//          'label' => $friend->getTitle(),
			//          'id' => $friend->getIdentity(),
			//          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
			//        );
			//      }
			//      $this->view->friends = Zend_Json::encode($data);
		}

		// Assign the composing stuff
		$composePartials = array();
		foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
			if( empty($data['composer']) ) {
				continue;
			}
			foreach( $data['composer'] as $type => $config ) {
				// is the current user has "create" privileges for the current plugin
				$isAllowed = Engine_Api::_()
				->authorization()
				->isAllowed($config['auth'][0], null, $config['auth'][1]);

				if( !empty($config['auth']) && !$isAllowed ) {
					continue;
				}
				$composePartials[] = $config['script'];
			}
		}
		$this->view->composePartials = $composePartials;
		// $this->view->composePartials = $composePartials;

		// Get config
		$this->view->maxRecipients = $maxRecipients = 10;


		// Check method/data
		if( !$this->getRequest()->isPost() ) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}

		// Process
		$db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
		$db->beginTransaction();

		try {
			// Try attachment getting stuff
			$attachment = null;
			$attachmentData = $this->getRequest()->getParam('attachment');
			if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
				$type = $attachmentData['type'];
				$config = null;
				foreach( Zend_Registry::get('Engine_Manifest') as $data )
				{
					if( !empty($data['composer'][$type]) )
					{
						$config = $data['composer'][$type];
					}
				}
				if( $config ) {
					$plugin = Engine_Api::_()->loadClass($config['plugin']);
					$method = 'onAttach'.ucfirst($type);
					$attachment = $plugin->$method($attachmentData);
					$parent = $attachment->getParent();
					if($parent->getType() === 'user'){
						$attachment->search = 0;
						$attachment->save();
					}
					else {
						$parent->search = 0;
						$parent->save();
					}
				}
			}

			$viewer = Engine_Api::_()->user()->getViewer();
			$values = $form->getValues();

			// Prepopulated
			if( $toObject instanceof User_Model_User ) {
				$recipientsUsers = array($toObject);
				$recipients = $toObject;
				// Validate friends
				/*
				if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
					if( !$viewer->membership()->isMember($recipients) ) {
						return $form->addError('One of the members specified is not in your friends list.');
					}
				}
				*/
			} else if( $toObject instanceof Core_Model_Item_Abstract &&
			method_exists($toObject, 'membership') ) {
				$recipientsUsers = $toObject->membership()->getMembers();
				//        $recipients = array();
				//        foreach( $recipientsUsers as $recipientsUser ) {
				//          $recipients[] = $recipientsUser->getIdentity();
				//        }
					$recipients = $toObject;
			}
			// Normal
			else {
				$recipients = preg_split('/[,. ]+/', $values['toValues']);
				// clean the recipients for repeating ids
				// this can happen if recipient is selected and then a friend list is selected
				$recipients = array_unique($recipients);
				// Slice down to 10
				$recipients = array_slice($recipients, 0, $maxRecipients);
				// Get user objects
				$recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
				// Validate friends
				if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
					foreach( $recipientsUsers as &$recipientUser ) {
						if( !$viewer->membership()->isMember($recipientUser) ) {
							return $form->addError('One of the members specified is not in your friends list.');
						}
					}
				}
			}

			// Create conversation
			$conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
			$viewer,
			$recipients,
			$values['title'],
			$values['body'],
			$attachment
			);

			// Send notifications
			foreach( $recipientsUsers as $user ) {
				if( $user->getIdentity() == $viewer->getIdentity() ) {
					continue;
				}
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
				$user,
				$viewer,
				$conversation,
          'message_new'
          );
			}

			// Increment messages counter
			Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

			// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		if( $this->getRequest()->getParam('format') == 'smoothbox' ) {
			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
		        'smoothboxClose' => true,
			));
		} else {
			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
		        'redirect' => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
			));
		}
	}
	
	protected function _checkEnoughCredits($credits)
	{
		$balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
		if (!$balance) {
			return false;
		}
		$currentBalance = $balance->current_credit;
		if ($currentBalance < $credits) {
			return false;
		}
		return true;
	}
	
	public function downloadAllAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> content -> setNoRender();
		$companyId = $this->_getParam('company_id', null);
    	if (is_null($companyId))
    	{
    		return $this->_helper->requireSubject()->forward();
    	}
    	$company = Engine_Api::_()->getItem('ynjobposting_company', $companyId);
		$this->view->jobId = $jobId = $this->_getParam('id', null);
		$job = null;
		if ($jobId)
		{
			$job = Engine_Api::_()->getItem('ynjobposting_job', $jobId);
		}
    	$jobIds = array();
    	$companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
    	$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
    	if (!is_null($job)){
    		$jobIds[] = $job -> getIdentity();
    	}
    	else 
    	{
    		$jobIds = $company -> getJobIds();
    	}
    	
    	$select = $applyTbl -> select();
		if (count($jobIds))
    	{
    		$select -> where("job_id in (?)", $jobIds);
    	}
    	else 
    	{
    		$select -> where("0 = 1");
    	}
    	$applies = $applyTbl -> fetchAll($select);
    	Engine_Api::_() -> getApi('createzipfile', 'ynjobposting') -> downloadAllResume($applies);
	}
	
	public function downloadResumeAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> content -> setNoRender();
		$applyId = $this->_getParam('id', null);
		if (is_null($applyId))
    	{
    		$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.");
			return;
    	}
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$apply = $applyTbl -> fetchRow(array(
			'jobapply_id = ?' => $applyId
		));
		Engine_Api::_() -> getApi('createzipfile', 'ynjobposting') -> downloadResume($apply);
	}
	
	public function deleteApplicationAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$this->view->form = $form = new Ynjobposting_Form_Application_Delete();
		
		$applyId = $this->_getParam('id', null);
		if (is_null($applyId))
    	{
    		$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.");
			return;
    	}
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$apply = $applyTbl -> fetchRow(array(
			'jobapply_id = ?' => $applyId
		));
		if( !$this->getRequest()->isPost() ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
			return;
		}

		$db = $applyTbl->getAdapter();
		$db->beginTransaction();

		try {
			$apply->delete();
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		$this->view->status = true;
		$this->view->message = Zend_Registry::get('Zend_Translate')->_('The application has been deleted.');
		return $this->_forward('success' ,'utility', 'core', array(
		      'parentRefresh' => true,
		      'messages' => Array($this->view->message)
		));
	}
	
	public function unsubscribeAction()
	{
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$email = $this ->_getParam('email');
		$tableAlert = Engine_Api::_() -> getItemTable('ynjobposting_alert');
		$tableSentJob = Engine_Api::_() -> getItemTable('ynjobposting_sentjob');
		$tableAlert -> deleteRowsByEmail($email);
		$tableSentJob -> deleteRowsByEmail($email);
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynjobposting_general', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Unsubscribed sucessfully!'))));
	}
	
	public function rejectApplicationAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$this->view->form = $form = new Ynjobposting_Form_Application_Reject();
		
		$applyId = $this->_getParam('id', null);
		if (is_null($applyId))
    	{
    		$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.");
			return;
    	}
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$apply = $applyTbl -> fetchRow(array(
			'jobapply_id = ?' => $applyId
		));

		if( !$this->getRequest()->isPost() ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
			return;
		}

		$db = $applyTbl->getAdapter();
		$db->beginTransaction();

		try {
			$apply->status = 'rejected';
			$apply->save();
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		$this->view->status = true;
		$this->view->message = Zend_Registry::get('Zend_Translate')->_('The application has been deleted.');
		return $this->_forward('success' ,'utility', 'core', array(
		      'parentRefresh' => true,
		      'messages' => Array($this->view->message)
		));
	}
	
	public function passApplicationAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		$this->view->form = $form = new Ynjobposting_Form_Application_Pass();
		
		$applyId = $this->_getParam('id', null);
		if (is_null($applyId))
    	{
    		$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_("This application doesn't exist.");
			return;
    	}
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$apply = $applyTbl -> fetchRow(array(
			'jobapply_id = ?' => $applyId
		));

		if( !$this->getRequest()->isPost() ) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
			return;
		}

		$db = $applyTbl->getAdapter();
		$db->beginTransaction();

		try {
			$apply->status = 'passed';
			$apply->save();
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		$this->view->status = true;
		$this->view->message = Zend_Registry::get('Zend_Translate')->_('The application has been set passed.');
		return $this->_forward('success' ,'utility', 'core', array(
		      'parentRefresh' => true,
		      'messages' => Array($this->view->message)
		));
	}
	
	public function viewApplicationAction()
	{
		$applyId = $this->_getParam('id', null);
		if (is_null($applyId))
    	{
    		return $this->_helper->requireSubject()->forward();
    	}
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$this -> view -> apply = $apply = $applyTbl -> fetchRow(array(
			'jobapply_id = ?' => $applyId
		));
		$this -> view -> job = $job = $apply->getJob();
		$this -> view -> company = $company = $job->getCompany();
		$this -> view -> submissionForm = $submissionForm = $job->getSubmissionForm();
		if (!$submissionForm)
		{
			return $this->_helper->requireSubject()->forward();
		}
    	if(empty($company))
		{
			return $this->_helper->requireSubject()->forward();
		}
	    if (!$company->isEditable()) 
	    {
	        return $this -> _helper -> requireAuth -> forward();
	    }
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->form = $form = new Ynjobposting_Form_Jobs_Application(array(
			'jobId' => $job->getIdentity(),
			'apply' => $apply
		));
		if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $posts = $this -> getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
            return;
        }
        $values = $form->getValues();
        $noteTbl = Engine_Api::_()->getDbTable('applynotes', 'ynjobposting');
        $note = $noteTbl -> createRow();
        $note -> setFromArray(array(
        	'creation_date' => date('Y-m-d H:i:s'),
        	'jobapply_id' => $apply->getIdentity(),
        	'user_id' => $viewer -> getIdentity(),
        	'content' => $values['content'],
        ));
        $note -> save();
        return $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
	
	public function candidatesAction()
	{
		$companyId = $this->_getParam('company_id', null);
    	if (is_null($companyId))
    	{
    		return $this->_helper->requireSubject()->forward();
    	}
    	$company = Engine_Api::_()->getItem('ynjobposting_company', $companyId);
		$this->view->jobId = $jobId = $this->_getParam('id', null);
		$job = null;
		if ($jobId)
		{
			$job = Engine_Api::_()->getItem('ynjobposting_job', $jobId);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> company = $company;
    	$this -> view -> job = $job;
    	$jobIds = array();
    	$companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
    	$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
    	$applyTblName = $applyTbl -> info('name');
    	if (!is_null($job)){
    		$jobIds[] = $job -> getIdentity();
    	}
    	else 
    	{
    		$jobIds = $company -> getJobIds();
    	}
    	
    	$userTbl = Engine_Api::_()->getItemTable('user');
    	$userTblName = $userTbl -> info('name');
    	$select = $userTbl 
    	-> select()->setIntegrityCheck(false)
    	-> from ($userTblName)
    	-> join ($applyTblName, "{$userTblName}.user_id = {$applyTblName}.user_id")
		;
		if (count($jobIds))
    	{
    		$select -> where("{$applyTblName}.job_id in (?)", $jobIds);
    	}
    	else 
    	{
    		$select -> where("0 = 1");
    	}
    	$this -> view -> candidates = $candidates = $userTbl -> fetchAll($select);
    	$this -> view -> jobs = $jobs = $company -> getJobsWithStatus();
	}

	public function applicationsAction()
	{
		// Return if guest try to access to edit link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
			
		$companyId = $this->_getParam('company_id', null);
    	if (is_null($companyId))
    	{
    		return $this->_helper->requireSubject()->forward();
    	}
    	$company = Engine_Api::_()->getItem('ynjobposting_company', $companyId);
    	if (empty($company))
		{
			return $this->_helper->requireSubject()->forward();
		}
	    if (!$company->isEditable()) 
	    {
	        return $this -> _helper -> requireAuth -> forward();
	    }
	    
		$this->view->jobId = $jobId = $this->_getParam('id', null);
		$job = null;
		if ($jobId)
		{
			$job = Engine_Api::_()->getItem('ynjobposting_job', $jobId);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> company = $company;
    	$this -> view -> job = $job;
    	$jobIds = array();
    	$companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
    	$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
    	$applyTblName = $applyTbl -> info('name');
    	if (!is_null($job)){
    		$jobIds[] = $job -> getIdentity();
    	}
    	else 
    	{
    		$jobIds = $company -> getJobIds();
    	}
    	
    	$userTbl = Engine_Api::_()->getItemTable('user');
    	$userTblName = $userTbl -> info('name');
    	$select = $userTbl 
    	-> select()->setIntegrityCheck(false)
    	-> from ($userTblName)
    	-> join ($applyTblName, "{$userTblName}.user_id = {$applyTblName}.user_id")
		;
		if (count($jobIds))
    	{
    		$select -> where("{$applyTblName}.job_id in (?)", $jobIds);
    	}
    	else 
    	{
    		$select -> where("0 = 1");
    	}
    	//echo $select; exit;
    	$this -> view -> candidates = $candidates = $userTbl -> fetchAll($select);
    	$this -> view -> jobs = $jobs = $company -> getJobsWithStatus();
    	$this -> _helper -> content -> setEnabled();
	}
	
	public function listingAction(){
    	// Setting to use landing page.
    	$this->_helper->content->setNoRender()->setEnabled();
    }
	
	public function getAlertAction()
	{
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$industry_id = $this ->_getParam('industry_id');
		$latitude = $this ->_getParam('latitude');
		$longitude = $this ->_getParam('longitude');
		$level = $this ->_getParam('level');
		$type = $this ->_getParam('type');
		$salary = $this ->_getParam('salary');
		$email = $this ->_getParam('email');
		$currency = $this ->_getParam('currency');
		$within = $this ->_getParam('within');
		
		// Load all emails
	    $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
	    $bannedEmails = $bannedEmailsTable->getEmails();
		
		//check if email is not in banned list
		if(!in_array($email, $bannedEmails))
		{
			//get setting max alert email by IP
			$settings = Engine_Api::_()->getApi('settings', 'core');
			$maxEmail = $settings->getSetting('ynjobposting_max_alertemail', 1);
			$maxGetAlertPerEmail = $settings->getSetting('ynjobposting_max_getalertperemail', 1);
			//table Jobalert
			$tableAlert = Engine_Api::_() -> getItemTable('ynjobposting_alert');
			
			// Get ip address
            $db = Engine_Db_Table::getDefaultAdapter();
            $ipObj = new Engine_IP();
            $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
                
			//check if ip is valid
			$ipEmails = $tableAlert -> getRowsByIP($ipExpr);
			if(count($ipEmails) < $maxEmail || in_array($email, $ipEmails))
			{
				$number_alerts = $tableAlert -> getRowsByEmail($email);
				if(count($number_alerts) < $maxGetAlertPerEmail)
				{
					$row = $tableAlert -> createRow();
					$row -> industry_id = $industry_id;
					$row -> latitude = $latitude;
					$row -> longitude = $longitude;
					$row -> level_id = $level;
					$row -> type_id = $type;
					$row -> salary = $salary;
					$row -> currency = $currency;
					$row -> within = $within;
					$row -> email = $email;
					$row -> ip = $ipExpr;
					$row -> save();
					echo Zend_Json::encode(array('json' => 'true', 'message' => 'Get job successfully.'));
				}
				else
				{
					echo Zend_Json::encode(array('json' => 'false', 'message' => 'Your email has reached the limit of get alert emails.'));
				}
			}
			else
			{
				echo Zend_Json::encode(array('json' => 'false', 'message' => 'Your IP has reached the limit of get alert emails.'));
			}
		}
		else
		{
			echo Zend_Json::encode(array('json' => 'false', 'message' => 'Your alert email was in banned list.'));
		}
        return true;
	}
	
	 public function placeOrderAction() 
    {
    	$settings = Engine_Api::_()->getApi('settings', 'core');
		$number_feature_day = 0;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> fee_featurejob = $fee_featurejob = $settings->getSetting('ynjobposting_fee_featurejob', 10);
        $this -> view -> job = $job = Engine_Api::_() -> getItem('ynjobposting_job', $this ->_getParam('id'));
		$this -> view -> package = $package = Engine_Api::_() -> getItem('ynjobposting_package', $this ->_getParam('packageId'));
		$this -> view -> number_feature_day = $number_feature_day = $this ->_getParam('number', 0);
        
        if($job->user_id != $viewer->getIdentity())
        {
        	$message = $this -> view -> translate('You do not have permission to do this.');
            return $this -> _redirector($message);
        }
        if (!$package && !$number_feature_day) {
            $message = $this -> view -> translate('Please select package or set feature day.');
            return $this -> _redirector($message);
        }
		if($number_feature_day)
		{
			if($number_feature_day <= 0)
			{
				$message = $this -> view -> translate('Invalid feature day.');
            	return $this -> _redirector($message);
			}
		}
		//Credit
        //check permission
        // Get level id
        $id = $viewer->level_id;
    	$action_type = "";
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynjobposting', null, 'use_credit') -> checkRequire()) {
            //TODO add implement code here
            $allowPayCredit = 0;
            $credit_enable = Engine_Api::_() -> ynjobposting() -> checkYouNetPlugin('yncredit');
            if ($credit_enable)
            {
            	if(!empty($package))
				{
					$action_type = 'buy_job';
				}
				if(!empty($number_feature_day))
				{
					$action_type = 'feature_job';
				}
				if(!empty($package) && !empty($number_feature_day))
				{
					$action_type = 'buyfeature_job';
				}
                $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
				if($type_spend)
				{
					$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
					$select = $creditTbl->select()
		                ->where("level_id = ? ", $id)
		                ->where("type_id = ?", $type_spend -> type_id)
		                ->limit(1 );
		            $spend_credit = $creditTbl->fetchRow($select);
					if($spend_credit)
					{
		               $allowPayCredit = 1;
		            }
				}
			}
            $this -> view -> allowPayCredit = $allowPayCredit;
        };
		$package_price = 0;
		if(!empty($package))
		{
			$package_price = $package -> price;
		}
        $this -> view -> total_pay = $total_pay = $package_price + $fee_featurejob * $number_feature_day;
        
	   //if package free & feature fee free????
	   if($total_pay == 0)
	   {
	   		if($package)
				$package_id = $package -> package_id;
			//core - buy job
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try 
			{
				Engine_Api::_() -> ynjobposting() -> buyJob($job->getIdentity(), $package_id, $number_feature_day);
				$db -> commit();
			} 
			catch (Exception $e) {
		      $db->rollBack();
		      throw $e;
		    }
		    
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'module' => 'ynjobposting',
					'controller' => 'jobs',
					'action' => 'view',
					'id' => $job->getIdentity(),
				), 'ynjobposting_extended', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Success...'))
			 ));
		}   
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit) || (!$package && !$number_feature_day)) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynjobposting');
        if ($row = $ordersTable -> getLastPendingOrder()) {
            $row -> delete();
        }
		$featured = 0;
		if($number_feature_day)
		{
			$featured = 1;
		}
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
		$package_id = 0;
		if(!empty($package))
		{
			$package_id = $package -> getIdentity();
		}
        try 
        {
            $ordersTable -> insert(array(
            	'user_id' => $viewer -> getIdentity(), 
	            'creation_date' => new Zend_Db_Expr('NOW()'), 
	            'package_id' => $package_id, 
	            'item_id' => $job -> getIdentity(),
	            'type' => 'job',
	            'price' => $total_pay, 
	            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				'featured' => $featured,
				'number_day' => $number_feature_day,
			));
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
    }

    public function updateOrderAction() 
    {
        $type = $this ->_getParam('type');
        $id = $this ->_getParam('id');
        if(isset($type))
        {
            switch ($type) {
                
                case 'paycredit':
					$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynjobposting');
					$order = $ordersTable -> getLastPendingOrder();
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
	                        'controller'=>'jobs',
	                        'action' => 'pay-credit', 
	                        'item_id' => $id,
							'order_id' => $order -> getIdentity()
						), 'ynjobposting_extended', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                default:
                    
                    break;
            }
        }

        $job = Engine_Api::_() -> getItem('ynjobposting_job', $id);
            
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynjobposting');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynjobposting', 'cancel_route' => 'ynjobposting_transaction', 'return_route' => 'ynjobposting_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'transaction', 'action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynjobposting_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function payCreditAction()
    {
    	$credit_enable = Engine_Api::_() -> ynjobposting() -> checkYouNetPlugin('yncredit');
        if (!$credit_enable)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
		$order = Engine_Api::_()->getItem('ynjobposting_order', $this->_getParam('order_id'));
		if(!$order)
        {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
		$action_type = "";
		$featured = $order -> featured;
		$package_id = $order -> package_id;
		if($package_id)
		{
			$action_type = 'buy_job';
		}
		if($featured)
		{
			$action_type = 'feature_job';
		}
		if($featured & $package_id)
		{
			$action_type = 'buyfeature_job';
		}
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
		$this-> view -> item = $job = Engine_Api::_() -> getItem('ynjobposting_job', $item_id);
        $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
		
        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(
	          array(
	            'controller' => 'jobs',
	            'action' => 'place-order',
	            'number' => $order -> number_day,
	            'id' => $item_id,
	            'packageId' => $order -> package_id
	          ), 'ynjobposting_extended', true);
	    //publish fee
        $this -> view -> total_pay = $total_pay =  $order -> price ;    
        $credits = ceil(($total_pay * $defaultPrice * $numbers));
        $this -> view -> cancel_url = $cancel_url;
        $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
        if (!$balance) 
        {
          $currentBalance = 0;
        } else 
        {
          $currentBalance = $balance->current_credit;
        }
        $this->view->currentBalance = $currentBalance;
        $this->view->credits = $credits;
        $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    
        // Check method
        if (!$this->getRequest()->isPost()) 
        {
          return;
        }
    
        // Insert member transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynjobposting');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
	     	Engine_Api::_() -> ynjobposting() -> buyJob($job->getIdentity(), $order -> package_id, $order -> number_day);
			//add feature
			$description = "";
			if($package_id)
			{
				$package = $order -> getSource();
				$description = $this ->view ->translate(array('Buy job in %s day', 'Buy job in %s days', $package -> valid_amount), $package -> valid_amount);
			}
			if($featured)
			{
				$description = $this ->view ->translate(array('Feature job in %s day', 'Feature job in %s days', $order -> number_day), $order -> number_day);
			}
			if($featured & $package_id)
			{
				$package = $order -> getSource();
				$description = $this ->view ->translate(array('Buy job in %1s day - Feature job in %2s day', 'Buy job in %1s days - Feature job in %2s days', $package -> valid_amount, $order -> number_day), $package -> valid_amount, $order -> number_day);
			}
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => '-3',
		     	'amount' => $order->price,
		     	'currency' => $order->currency,
		     	'user_id' => $order->user_id,
		     	'type' => $order->type,
		     	'item_id' => $order->item_id,
		     	'description' => $description,
			 ));
			 
			 //send notification to admin
			 if($order->type == 'company')
			 {
			 	$notificationType = 'ynjobposting_company_transaction';
				$item = Engine_Api::_() -> getItem('ynjobposting_company', $order->item_id);
			 }
		     elseif($order->type == 'job')
			 {
			 	$notificationType = 'ynjobposting_job_transaction';
				$item = Engine_Api::_() -> getItem('ynjobposting_job', $order->item_id);
			 }
			 $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			 $list_admin = Engine_Api::_()->user()->getSuperAdmins();
			 foreach($list_admin as $admin)
			 {
				 $notifyApi -> addNotification($admin, $item, $item, $notificationType);
			 }
	      $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
        Engine_Api::_()->yncredit()-> spendCredits($viewer, (-1) * $credits, $viewer->getTitle(), $action_type, $viewer);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'jobs', 'action' => 'view', 'id' => $order->item_id), 'ynjobposting_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }
	
    public function viewAction() {
    	$jobId = $this->_getParam('id', null);
    	if (is_null($jobId))
    	{
    		return $this->_helper->requireSubject()->forward();
    	}
		$job = Engine_Api::_()->getItem('ynjobposting_job', $jobId);
		if (is_null($job) || $job->isDeleted())
		{
			return $this->_helper->requireSubject()->forward();
		}
        if(!$job->isViewable()) {
            return $this -> _helper -> requireAuth -> forward();
        }
		$viewer = Engine_Api::_()->user()->getViewer();
    	
    	if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
    	{
    		Engine_Api::_()->core()->setSubject($job);
    	}
    	if (!$viewer->isSelf($job->getOwner()))
    	{
    	    if (!$job->isPublished() && !$job->isExpired() && !$job->isEnded()) {
    	        return $this->_helper->requireSubject()->forward();
    	    }
    		$job -> view_count++; 
    		$job -> save();
    	}
    	$this -> view -> job = $job;
    	$this -> _helper -> content -> setEnabled();
    }

    public function createAction() {
        // Return if guest try to access to create link.
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        
        // Check authorization to post job.
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynjobposting_job', null, 'create') -> isValid())
            return;
        
        $this -> _helper -> content -> setEnabled();
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $jobTbl = Engine_Api::_() -> getItemTable('ynjobposting_job');
        
        //get max jobs user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_jobs = $permissionsTable->getAllowed('ynjobposting', $viewer->level_id, 'max_job');
        if ($max_jobs == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynjobposting')
                ->where('name = ?', 'max_job'));
            if ($row) {
                $max_jobs = $row->value;
            }
        }
        
        $select = $jobTbl->select()
            -> where('user_id = ?', $viewer->getIdentity())
            -> where('status <> ?', 'deleted');
			
        $raw_data = $jobTbl->fetchAll($select);
        if (($max_jobs != 0) && (sizeof($raw_data) >= $max_jobs)) {
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate') -> _('Your jobs are reach limit. Plese delete some jobs for creating new.');
            return;
        }
        
        $oldParams = array();
        if ($this -> getRequest() -> isPost()) {
            $oldParams = $this -> getRequest() -> getPost();
        }
        $this -> view -> form = $form = new Ynjobposting_Form_Jobs_Create(array('oldParams' => $oldParams));
        
        //popuplate company
        $companies = Engine_Api::_()->getItemTable('ynjobposting_company')->getMyCompanies();
        $form->company_id->addMultiOptions($companies);
        
        if (sizeof($companies) <= 0) {
            $form->addError(Zend_Registry::get('Zend_Translate') -> _('You can not create a job. You have to create the company first, in order to create a job.'));
        }
        
        //populate industry
        $industries = Engine_Api::_() -> getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);
        foreach ($industries as $industry) {
            $form->industry_id->addMultiOption($industry['industry_id'], str_repeat("-- ", $industry['level'] - 1).$industry['title']);
        }
        if (sizeof($industries) <= 0) {
            $form->addError($this->view->translate('Create job require at least one industry. Please contact admin for more details.'));
        }

        //populate currency
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway -> gateway_id] = $gateway -> title;
            $gatewayObject = $gateway -> getGateway();
            $currencies = $gatewayObject -> getSupportedCurrencies();
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway -> title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            }
            else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

        $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
        $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
        $supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
        
        $form -> getElement('salary_currency') -> setMultiOptions(array(
            'Please select one' => array_merge($fullySupportedCurrencies, $supportedCurrencies)
        ));
        
		//populate package
        $packageTbl = Engine_Api::_()->getDbTable('packages', 'ynjobposting');
        $packages = $packageTbl->fetchAll();
        $hasPackage = false;
        foreach ($packages as $package) {
            if ($package->isViewable() && $package->show && !$package->deleted) {
                $form->package_id->addMultiOption($package->getIdentity(), $this->view->translate('%1s - %2s - Period %3s days', $package->title, $this->view->locale()->toCurrency($package->price, $package->currency), $package->valid_amount));
                $hasPackage = true;
            }
        }
		
        if (!$hasPackage) {
            $form->addError(Zend_Registry::get('Zend_Translate') -> _('Sorry, but we don\'t have any packages for you. Please contact with the administrators for more infomations.'));
        }
        
        // Check method and data validity.
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $posts = $this -> getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
            return;
        }
        $params = $this -> _getAllParams();
        $values = $form -> getValues();
        $values = array_merge($values, $params);
        $table = Engine_Api::_() -> getItemTable('ynjobposting_job');
        $values['working_place'] = $values['location'];
        $values['latitude'] = $values['lat'];
        $values['longitude'] = $values['long'];
        $values['user_id'] = $viewer -> getIdentity();
        if ($values['published']) {
            if (empty($values['package_id'])) {
                $form->addError(Zend_Registry::get('Zend_Translate') -> _('You must choose package for publishing job.'));
                return;
            }
            
            if ($values['feature'] && empty($values['feature_period'])) {
                $form->addError(Zend_Registry::get('Zend_Translate') -> _('You must enter feature period (days).'));
                return;
            }     
        }
        
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
			if (empty($values['salary_from']) && empty($values['salary_to'])) {
                $values['negotiable'] = 1;
            }
            if ($values['negotiable']) {
                unset($values['salary_from']);
                unset($values['salary_to']);
            }
            
            $job = $table -> createRow();
            $job -> setFromArray($values);
            $job -> save();
            
            //add Additional Infomation
            $jobInfoTable = Engine_Api::_()->getDbTable('jobinfos', 'ynjobposting');
            $allowed_tag = '<strong><b><em><i><u><strike><sub><sup><p><div><pre><address><h1><h2><h3><h4><h5><h6><span><ol><li><ul><a><img><embed><br><hr><object><param><iframe>';
            $jobInfoTable->deleteAllInfoByJobId($job->getIdentity());
            foreach ($values as $key => $value) {
                if (strpos($key, 'header') !== false) {
                    $index = explode('_', $key);
                    if (isset($index[1])) {
                        if (!empty($values[$key]) && !empty($values['content_'.$index[1]])) {
                            $addInfo  = $jobInfoTable->createRow();
                            $addInfo->header = strip_tags($values[$key]);
                            $addInfo->content = strip_tags($values['content_'.$index[1]], $allowed_tag);
                            $addInfo->job_id = $job->getIdentity();
                            $addInfo->save();
                        }
                    }
                    else {
                    	if (!empty($values['header']) && !empty($values['content'])) 
						{
	                        $addInfo  = $jobInfoTable->createRow();
	                        $addInfo->header = strip_tags($values[$key]);
	                        $addInfo->content = strip_tags($values['content'], $allowed_tag);
	                        $addInfo->job_id = $job->getIdentity();
	                        $addInfo->save();
						}
                    } 
                }  
            }
            
            $feature_period = 0;
            if ($values['feature_period'])
                $feature_period = $values['feature_period'];
            $db->commit();
            
            //set auth for view, comment
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = $values[$elem];
                if (!$auth_role) {
                    $auth_role = 'everyone';
                }
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i=>$role) {
                   $auth->setAllowed($job, $role, $elem, ($i <= $roleMax));
                }    
            }
            
            if(isset($values['tags']) && $values['tags']) {
                $tags = preg_split('/[,]+/', $values['tags']);
                $job -> tags() -> addTagMaps($viewer, $tags);
            }
            
            $search_table = Engine_Api::_() -> getDbTable('search', 'core');
            $select = $search_table -> select() -> where('type = ?', 'ynjobposting_job') -> where('id = ?', $job -> getIdentity());
            $row = $search_table -> fetchRow($select);
            if ($row)
            {
                $row -> keywords = $values['tags'];
                $row -> save();
            }
            else
            {
                $row = $search_table -> createRow();
                $row -> type = 'ynjobposting_job';
                $row -> id = $job -> getIdentity();
                $row -> title = $job -> title;
                $row -> description = $job -> description;
                $row -> keywords = $values['tags'];
                $row -> save();
            }
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
	        {
	            Engine_Api::_()->yncredit()-> hookCustomEarnCredits($job -> getOwner(), $job -> title, 'ynjobposting_job', $job);
			}
            
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        if ($values['published']) {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'jobs',
                    'action' => 'place-order',
                    'packageId' => $values['package_id'],
                    'number' => $feature_period,
                    'id' => $job -> getIdentity()
                ), 'ynjobposting_job', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
        else {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'jobs',
                    'action' => 'view',
                    'id' => $job -> getIdentity()
                ), 'ynjobposting_job', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
    }
    
    public function editAction() {
        // Return if guest try to access to create link.
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        
        $job_id = $this->_getParam('id', null);
        if (!$job_id || !($job = Engine_Api::_()->getItem('ynjobposting_job', $job_id))) {
            return $this -> _helper -> requireSubject -> forward();
        }
        
        //check auth for editing job
        if (!$job->isEditable()) {
            return $this -> _helper -> requireAuth -> forward();
        }
        
        $this->view->job = $job;
        $this -> _helper -> content -> setEnabled();
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $jobTbl = Engine_Api::_() -> getItemTable('ynjobposting_job');
        
        $oldParams = array();
        if ($this -> getRequest() -> isPost()) {
            $oldParams = $this -> getRequest() -> getPost();
        }
        $this -> view -> form = $form = new Ynjobposting_Form_Jobs_Edit(array('jobId' => $job_id, 'oldParams' => $oldParams));
        
        //populate industry
        $industries = Engine_Api::_() -> getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);
        foreach ($industries as $industry) {
            $form->industry_id->addMultiOption($industry['industry_id'], str_repeat("-- ", $industry['level'] - 1).$industry['title']);
        }
        if (sizeof($industries) <= 0) {
            $form->addError('Can not find any industries. Please contact admin for more details.');
        }
        
        //populate currency
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway -> gateway_id] = $gateway -> title;
            $gatewayObject = $gateway -> getGateway();
            $currencies = $gatewayObject -> getSupportedCurrencies();
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway -> title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            }
            else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

        $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
        $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
        $supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
        
        $form -> getElement('salary_currency') -> setMultiOptions(array(
            'Please select one' => array_merge($fullySupportedCurrencies, $supportedCurrencies)
        ));
        
        //populate package
        $packageTbl = Engine_Api::_()->getDbTable('packages', 'ynjobposting');
        $packages = $packageTbl->fetchAll();
		$hasPackage = false;
        foreach ($packages as $package) {
            if ($package->isViewable() && $package->show && !$package->deleted) {
                if ($job->status == 'draft') {
                    $hasPackage = true;
                    $form->package_id->addMultiOption($package->getIdentity(), $this->view->translate('%1s - %2s - Period %3s days', $package->title, $this->view->locale()->toCurrency($package->price, $package->currency), $package->valid_amount));
                }
                else {
                    $form->package_id->addMultiOption($package->getIdentity(), $this->view->translate('Add more %1s day(s) - %2s (package %3s)',  $package->valid_amount, $this->view->locale()->toCurrency($package->price, $package->currency), $package->title));
                }
            }
        }
        
		if ($job->status == 'draft' && !$hasPackage) {
            $form->package_id->setDescription($this->view->translate('Sorry, but we don\'t have any packages for you. Please contact with the administrators for more infomations.'));
        }
        //populate data of job
        $form->populate($job->toArray());
        
        if ($job->salary_from == null && $job->salary_to == null) {
            $form->negotiable->setValue(1);
            $form->salary_from->setAttrib('class', 'disabled');
            $form->salary_from->setAttrib('disabled', true);
            $form->salary_to->setAttrib('class', 'disabled');
            $form->salary_to->setAttrib('disabled', true);
            $form->salary_currency->setAttrib('class', 'disabled');
            $form->salary_currency->setAttrib('disabled', true);
            $form->salary_currency->setValue('USD');
        }
        
        $form->lat->setValue($job->latitude);
        $form->long->setValue($job->longitude);
        
        //populate auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
        $auth_arr = array('view', 'comment');
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($job, $role, $elem)) {
                    $form->$elem->setValue($role);
                }
            }    
        }
        
        //populate tags
        $tagStr = '';
        foreach ($job->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap -> getTag();
            if (!isset($tag -> text))
                continue;
            if ('' !== $tagStr)
                $tagStr .= ', ';
            $tagStr .= $tag -> text;
        }
        $form -> populate(array('tags' => $tagStr));
        
        // Check method and data validity.
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $posts = $this -> getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
            return;
        }
        $params = $this -> _getAllParams();
        $values = $form -> getValues();
        $values = array_merge($values, $params);
        $table = Engine_Api::_() -> getItemTable('ynjobposting_job');
        $values['working_place'] = $values['location'];
        $values['latitude'] = $values['lat'];
        $values['longitude'] = $values['long'];
        
        if (isset($values['published']) && $values['published']) {
            if (empty($values['package_id'])) {
                $form->addError(Zend_Registry::get('Zend_Translate') -> _('You must choose package for publishing job.'));
                return;
            }
            
            if ($values['feature'] && empty($values['feature_period'])) {
                $form->addError(Zend_Registry::get('Zend_Translate') -> _('You must enter feature period (days).'));
                return;
            }     
        }
        
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            if ($values['negotiable']) {
                $values['salary_from'] = null;
                $values['salary_to'] = null;
            }
            
            $job -> setFromArray($values);
            
            if (isset($values['end'])) {
                if ($values['end']) {
                    $job->status = 'ended';
                }
                else {
                    if ($job->status == 'ended') $job->status = 'published';        
                }
            }
            $job -> save();
            
            //add Additional Infomation
            $jobInfoTable = Engine_Api::_()->getDbTable('jobinfos', 'ynjobposting');
            $allowed_tag = '<strong><b><em><i><u><strike><sub><sup><p><div><pre><address><h1><h2><h3><h4><h5><h6><span><ol><li><ul><a><img><embed><br><hr><object><param><iframe>';
            $jobInfoTable->deleteAllInfoByJobId($job->getIdentity());
            foreach ($values as $key => $value) {
                if (strpos($key, 'header') !== false) {
                    $index = explode('_', $key);
                    if (isset($index[1])) {
                        if (!empty($values[$key]) && !empty($values['content_'.$index[1]])) {
                            $addInfo  = $jobInfoTable->createRow();
                            $addInfo->header = strip_tags($values[$key]);
                            $addInfo->content = strip_tags($values['content_'.$index[1]], $allowed_tag);
                            $addInfo->job_id = $job->getIdentity();
                            $addInfo->save();
                        }
                    }
                    else {
                    	if (!empty($values['header']) && !empty($values['content'])) 
						{
	                        $addInfo  = $jobInfoTable->createRow();
	                        $addInfo->header = strip_tags($values[$key]);
	                        $addInfo->content = strip_tags($values['content'], $allowed_tag);
	                        $addInfo->job_id = $job->getIdentity();
	                        $addInfo->save();
						}
                    } 
                }  
            }
            
            $feature_period = 0;
            if (isset($values['feature_period']) && $values['feature_period'])
            $feature_period = $values['feature_period'];
            
            //set auth for view, comment
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = $values[$elem];
                if (!$auth_role) {
                    $auth_role = 'everyone';
                }
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i=>$role) {
                   $auth->setAllowed($job, $role, $elem, ($i <= $roleMax));
                }    
            }
            
            // Add tags
            if(isset($values['tags']) && $values['tags']) {
                $tags = preg_split('/[,]+/', $values['tags']);
                $job -> tags() -> setTagMaps($viewer, $tags);
            }

            $search_table = Engine_Api::_() -> getDbTable('search', 'core');
            $select = $search_table -> select() -> where('type = ?', 'ynjobposting_job') -> where('id = ?', $job -> getIdentity());
            $row = $search_table -> fetchRow($select);
            if ($row) {
                $row -> keywords = $values['tags'];
                $row -> save();
            }
            else
            {
                $row = $search_table -> createRow();
                $row -> type = 'ynjobposting_job';
                $row -> id = $job -> getIdentity();
                $row -> title = $job -> title;
                $row -> description = $job -> description;
                $row -> keywords = $values['tags'];
                $row -> save();
            }
            
            //send notification
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
            $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_edited');
            
            $savedList = $job->getAllSaved();
            foreach ($savedList as $saved) {
                $user = Engine_Api::_()->user()->getUser($saved->user_id);
                if ($user) {
                    $notifyApi -> addNotification($user, $job, $job, 'ynjobposting_job_edited');
                }
            }
            
            $appliedList = $job->getAllApplied();
            foreach ($appliedList as $applied) {
                $user = Engine_Api::_()->user()->getUser($applied->user_id);
                if ($user) {
                    $notifyApi -> addNotification($user, $job, $job, 'ynjobposting_job_edited');
                }
            }
            $db->commit();
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $published = true;
        if (isset($values['published']) && $values['published'] == 0) {
            $published = false;
        }
        if (($values['package_id'] || $feature_period) && $published) {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'jobs',
                    'action' => 'place-order',
                    'packageId' => $values['package_id'],
                    'number' => $feature_period,
                    'id' => $job -> getIdentity()
                ), 'ynjobposting_job', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
        else {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'jobs',
                    'action' => 'view',
                    'id' => $job -> getIdentity()
                ), 'ynjobposting_job', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
    }

    public function uploadPhotoAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        if (!$this -> _helper -> requireUser() -> checkRequire())
            return;
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynjobposting_job', null, 'create') -> isValid())
            return;

        if (!$this -> getRequest() -> isPost()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }

        if (empty($_FILES['files'])) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('No file');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
        }
        $name = $_FILES['files']['name'][0];
        $type = explode('/', $_FILES['files']['type'][0]);
        if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        
        if($_FILES['upfile']['size'] > (5*1024)) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Exceeded filesize limit.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $temp_file = array(
            'type' => $_FILES['files']['type'][0],
            'tmp_name' => $_FILES['files']['tmp_name'][0],
            'name' => $_FILES['files']['name'][0]
        );
        $photo_id = Engine_Api::_() -> ynjobposting() -> createPhoto($temp_file);

        $status = true;
        $name = $_FILES['files']['name'][0];
        $photo_url = Engine_Api::_()->ynjobposting()->getPhoto($photo_id);
        return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $name, 'photo_id' => $photo_id, 'photo_url' => $photo_url)))));
    }
    
	public function deleteAction() {
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id');
        $this->view->job_id = $id;
        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
        if (!$job->isDeletable()) {
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to delete this job.');
            return;
        }
        
        if( $this->getRequest()->isPost()) {
            $job->delete();
            
            //send notification
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
            $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_deleted');
            
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' =>true,
                'parentRefresh'=> true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Delete job successful.'))
            ));
        }

        // Output
        //$this->renderScript('jobs/delete.tpl');
    }
	
	public function endAction() {
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id');
        $this->view->job_id = $id;
        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
        if (!$job->isEndable()) {
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to end this job.');
            return;
        }
        
        if (!$job->isPublished()) {
            $this->view->error = true;
            $this->view->message = Zend_Registry::get('Zend_Translate') -> _('Your request is invalid.');
            return;
        }
        
        if( $this->getRequest()->isPost()) {
            $job->changeStatus('ended');
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $owner = $job -> getOwner();
			$notifyApi -> addNotification($owner, $owner, $job, 'ynjobposting_job_ended');
            
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' =>true,
                'parentRefresh'=> true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('End job successful.'))
            ));
        }
    }
    
	public function rePublishAction() {
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id');
        $this->view->job_id = $id;
        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
        if( $this->getRequest()->isPost()) {
            $job->changeStatus('published');

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' =>true,
                'parentRefresh'=> true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Re-publish job successful.'))
            ));
        }

        // Output
        //$this->renderScript('jobs/end.tpl');
    }
    
    //apply job
    public function applyAction() {
     //   $this->_helper->content->setEnabled();
        $job_id = $this->_getParam('id');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$job_id || !($job = Engine_Api::_()->getItem('ynjobposting_job', $job_id))) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        
        if ($job->isDeleted()) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        //check can apply job
        if (!$this->_helper->requireAuth()->setAuthParams('ynjobposting_job', null, 'apply')->isValid() ) {
            return;
        }
        
        //check if is job owner
        if ($job->isOwner()) {
            return $this -> _helper -> requireAuth -> forward();
        }
        
        $this -> view -> submissionForm = $submissionForm = $job->getSubmissionForm();
        if (!$submissionForm) {
            $this->_helper->viewRenderer->setNoRender(true);
            echo (Zend_Registry::get('Zend_Translate') -> _('Can not apply job. Please contact with its company for more infomation.'));
            return;
        }
        
        //check if has applied this job
        if ($job->hasApplied()) {
            $this->_helper->viewRenderer->setNoRender(true);
            echo (Zend_Registry::get('Zend_Translate') -> _('You have applied this job.'));
            return;
        }
        
        //check job is published
        if (!$job->isPublished()) {
            $this->_helper->viewRenderer->setNoRender(true);
            echo (Zend_Registry::get('Zend_Translate') -> _('This job is not published now.'));
            return;
        }
        $this->view->job = $job;
        $this->view->company = $company = $job->getCompany();
        $this->view->form = $form = new Ynjobposting_Form_Jobs_Apply(array('jobId' => $job_id));
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $posts = $this -> getRequest() -> getPost();
        if (!$form -> isValid($posts)) {
        	$this -> view -> posts = $posts;
            return;
        }
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $values = $form->getValues();
		
        try {
            //save apply job info
            $table = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
            $jobApply = $table->createRow();
            $jobApply->job_id = $job_id;
            $jobApply->user_id = $viewer->getIdentity();
			if(isset($values['resume']) && !empty($values['resume'])) {
				$jobApply->resume = $values['resume'];
			}
            if (isset($values['resume_video'])) {
                if ($values['resume_video'] == 1 && !empty($values['video_link'])) {
                    $jobApply->video_link = $values['video_link'];
                }
                if ($values['resume_video'] == 2 && !empty($values['video_id'])) {
                    $jobApply->video_id = $values['video_id'];
                }
            }
            $jobApply->save();
            
            //count candidate
            $candidate_count = $job->candidate_count;
            $candidate_count++;
            $job->candidate_count = $candidate_count;
            $job->save();
            
			if (!empty($values['photo'])) {
				$questionFields = $company->getSubmissionQuestionFields();
				foreach ($questionFields as $questionField) {
					if ($questionField->type == 'file') {
						$field_id = $questionField->field_id;
						$photo_id = Engine_Api::_()->ynjobposting()->createPhoto($form->photo);
						$valuesTbl = Engine_Api::_()->getDbTable('submissionvalues', 'ynjobposting');
	                    $valueItem = $valuesTbl->createRow();
	                    $valueItem->item_id = $jobApply->getIdentity();
	                    $valueItem->field_id = $field_id;
	                    $valueItem->value = $photo_id;
	                    $valueItem->save();
						unset($values['photo']);
						break;
					}
				}
			}
            //save custom field info
            foreach ($values as $key => $value) {
                if (strpos($key, 'field_') !== false && $value) {
                    if ($_FILES[$key]) {
                        $value = Engine_Api::_()->ynjobposting()->createPhoto($form->$key);
                    }
                    if (is_array($value)) {
                        $value = serialize($value);
                    }
                    $valuesTbl = Engine_Api::_()->getDbTable('submissionvalues', 'ynjobposting');
                    $valueItem = $valuesTbl->createRow();
                    $valueItem->item_id = $jobApply->getIdentity();
                    $valueItem->field_id = substr($key, 6);
                    $valueItem->value = $value;
                    $valueItem->save();
                }
            }
			
            //save upload resume files
            $resumefilesTbl = Engine_Api::_()->getDbTable('resumefiles', 'ynjobposting');
            if (is_array($form->upload_files->getFileName())) {
                foreach ($form->upload_files->getFileName() as $file) {
                    $file_id = Engine_Api::_()->ynjobposting()->createFile($file);
                    $resumefile = $resumefilesTbl->createRow();
                    $resumefile->jobapply_id = $jobApply->getIdentity();
                    $resumefile->file_id = $file_id;
                    $resumefile->file_name = basename($file);
                    $resumefile->save();
                }
            }
            else if ($form->upload_files->getFileName()){
                $file = $form->upload_files->getFileName();
                $file_id = Engine_Api::_()->ynjobposting()->createFile($file);
                $resumefile = $resumefilesTbl->createRow();
                $resumefile->jobapply_id = $jobApply->getIdentity();
                $resumefile->file_id = $file_id;
                $resumefile->file_name = basename($file);
                $resumefile->save();
            }
            
            $job->removeSaveJob();
            
            //send notification
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
            $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_applied');
            
            $db->commit();
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'jobs',
                    'action' => 'view',
                    'id' => $job_id,
                ), 'ynjobposting_job', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Apply job successful.'))
            ));
        }
        catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    //get videos of viewer
    public function getVideosAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $videos = Engine_Api::_()->ynjobposting()->getMyVideos();
        echo Zend_Json::encode(array('json' => $videos));
        return true;
    }
    
    //save job
    public function saveAction() {
        $this->_helper->layout->setLayout('default-simple');
        $job_id = $this->_getParam('id');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$job_id || !($job = Engine_Api::_()->getItem('ynjobposting_job', $job_id))) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        
        //check if is job owner
        if ($job->isOwner()) {
            return $this -> _helper -> requireAuth -> forward();
        }
        
        $this -> _helper -> requireUser();
        
        //check if has applied this job
        if ($job->hasApplied()) {
            $this->_helper->viewRenderer->setNoRender(true);
            echo (Zend_Registry::get('Zend_Translate') -> _('You have applied this job.'));
            return;
        }
        
        if ($job->hasSaved()) {
            $this->_helper->viewRenderer->setNoRender(true);
            echo (Zend_Registry::get('Zend_Translate') -> _('You have saved this job.'));
            return;
        }
        
        $table = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');
        $db = $table->getAdapter();
        $db->beginTransaction();
        
        try {
            $saveJob = $table->createRow();
            $saveJob->user_id = $viewer->getIdentity();
            $saveJob->job_id = $job_id;
            $saveJob->save();
            $db->commit();
            
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' =>true,
                'parentRefresh'=> true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Save job successful.'))
            ));
        }
        
        catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //promote job
    public function promoteAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $job = Engine_Api::_() -> getItem('ynjobposting_job', $this -> getRequest() -> getParam('id'));
        if (!$job || $job->isDeleted()) {
            return $this -> _helper -> requireSubject() -> forward();
        }
        // In smoothbox
        $this -> _helper -> layout -> setLayout('default-simple');
        // Make form
        $this -> view -> job = $job;
        
        $company = $job->getCompany();
        if (!$company)
        {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> view -> company = $company;
    }
    
    //badge for job
    public function badgeAction() {
        $this -> _helper -> layout -> setLayout('default-simple');
        $job_id = $this -> _getParam('id');
        $this -> view -> status = $status = $this -> _getParam('status');
        $aStatus = str_split($status);
        $name = 0;
        $candidate = 0;
        $company = 0;
        if (count($aStatus) == 3)
        {
            if ($aStatus[0] == '1')
                $name = 1;
            if ($aStatus[1] == '1')
                $candidate = 1;
            if ($aStatus[2] == '1')
                $company = 1;
        }
        $this -> view -> name = $name;
        $this -> view -> candidate = $candidate;
        $this -> view -> company_name = $company;

        $job = Engine_Api::_() -> getItem('ynjobposting_job', $job_id);
        if (!$job || $job->isDeleted())
        {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $company = $job->getCompany();
        if (!$company)
        {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> view -> job = $job;
        $this -> view -> company = $company;
    }

    //my jobs page
    public function manageAction() {
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> _helper -> content -> setEnabled();
        $this->view->mode = $mode = $this->_getParam('mode', 'applied');
        $table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $tableName = $table->info('name');
        $select = $table->select();
        $select -> setIntegrityCheck(false); 
        if ($mode == 'applied') {
            $joinTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
            $joinTblName = $joinTbl->info('name');
        }
        elseif ($mode == 'saved') {
            $joinTbl = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');
            $joinTblName = $joinTbl->info('name');
        }
        $select->from("$tableName as job", "job.*");
        $select->where('job.status <> ?', 'deleted');
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($mode == 'applied') {
            $select->joinLeft("$joinTblName","$joinTblName.job_id = job.job_id", "$joinTblName.creation_date as applied_date");
            $select->where("$joinTblName.owner_deleted = ?", 0);
            $select->where("$joinTblName.user_id = ?", $viewer->getIdentity());
        }
        elseif ($mode == 'saved') {
            $select->joinLeft("$joinTblName","$joinTblName.job_id = job.job_id", "");
            $select->where("$joinTblName.user_id = ?", $viewer->getIdentity());
        }
        else {
            $select->where('job.user_id = ?', $viewer->getIdentity());
        }
        $page = $this->_getParam('page', 1);
        $this->view->jobs = $jobs = $table->fetchAll($select);
        $this->view->paginator = Zend_Paginator::factory($jobs);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function multiDeleteMyJobsAction(){
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $this->view->mode = $mode = $this->_getParam('mode', 'applied');
        $confirm = $this -> _getParam('confirm', false);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true)
        {
            //Process delete
            $ids_array = explode(",", $ids);
            if ($mode == 'applied') {
                $table = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
            }
            elseif ($mode == 'saved'){
                $table = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');
            }
            else {
                $table = Engine_Api::_()->getDbTable('jobs', 'ynjobposting');
            }
            $select = $table->select()->where('job_id IN (?)', $ids_array)->where('user_id = ?', $viewer->getIdentity());
            $results = $table->fetchAll($select);
            foreach ($results as $row) {
                if ($mode == 'applied') {
                    $row->owner_deleted = 1;
                    $row->save();
                }
                elseif ($mode == 'saved'){
                    $row->delete();
                }
                else {
                    $row->delete();
                    //send notification
                    $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                    $notifyApi -> addNotification($row->getOwner(), $row, $row, 'ynjobposting_job_deleted');
                }
            }
            $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage', 'mode' => $mode), 'ynjobposting_job', true);
        }
    }
    
    public function printAction() {
        $this -> _helper -> layout -> setLayout('default-simple');
        $jobId = $this->_getParam('id', null);
        if (is_null($jobId)) {
            return $this->_helper->requireSubject()->forward ();
        }
        $job = Engine_Api::_()->getItem('ynjobposting_job', $jobId);
        if (is_null($job) || $job->isDeleted()) {
            return $this->_helper->requireSubject()->forward ();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynjobposting_job', null, 'print') -> isValid())
            return;
        if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
        {
            Engine_Api::_()->core()->setSubject($job);
        }
        $this -> view -> job = $job;
    }
    
    public function shareAction() {
        $this->_helper->layout->disableLayout ();
        $this->_helper->viewRenderer->setNoRender ( TRUE );
        $job_id = $this->_getParam ( 'job_id' );
        $job = Engine_Api::_ ()->getItem ( 'ynjobposting_job', $job_id );
        if (!$job || $job->isDeleted()) {
            return $this->_helper->requireSubject()->forward ();
        }
        $job->share_count ++;
        $job->save ();
        echo '{"share":"' . $job->share_count . '"}';
    }
}
