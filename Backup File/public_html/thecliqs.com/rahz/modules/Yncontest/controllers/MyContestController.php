<?php

class Yncontest_MyContestController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return; 
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid()) return;
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $this->_getParam('contestId', null));

		if($contest) {
			if( !Engine_Api::_()->core()->hasSubject() ) {
				Engine_Api::_() -> core() -> setSubject($contest);
			}
		}

	}
	public function shareAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		$contest_id = $this->_getParam ( 'contest_id' );
		$contest = Engine_Api::_ ()->getItem ( 'contest', $contest_id );
		if (! $contest) {
			return $this->_helper->requireAuth->forward ();
		}
		$contest->share_count ++;
		$contest->save ();
		echo '{"share":"' . $contest->share_count . '"}';
	}


	public function indexAction()
	{
		// Render
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		Zend_Registry::set('active_mini_menu','my_contest');
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function cancelAction(){

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$contest = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
			
		$this -> view -> form = $form = new  Yncontest_Form_Member_Cancel();
			
		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {



			$member = Engine_Api::_()->getDbTable('members', 'yncontest')->getMemberContest2(array(
					'contestId'=>$contest->contest_id,
					'user_id'=> $viewer->getIdentity()));
			$member->delete();
			// Remove the notification?
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($contest -> getOwner(), $contest, 'contest_approve');
			if ($notification) {
				$notification -> delete();
			}
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Contest members request cancelled.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}
	public function leaveAction() {
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$contest = Engine_Api::_() -> core() -> getSubject();

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> isOwner($viewer))
			return;
		// Make form
		$this -> view -> form = $form = new Yncontest_Form_Member_Leave();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try {
				$contest -> membership() -> removeMember($viewer);
				$db -> commit();
			}
			catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have successfully left this contest.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}

	public function joinAction(){

		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
			
		$contest = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->condition = $contest->condition;
		$this -> view -> form = $form = new Yncontest_Form_Member_Join(array(
				'terms' =>$contest->condition,
		));

		// If member is already part of the contest
		if ($contest -> membership() -> isMember($viewer, true)) {
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are already a member of this contest.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
			
		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$db = $contest -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try {
					
				if ($contest -> membership() -> isMember($viewer)) {
					$contest -> membership() -> setUserApproved($viewer);
				}
				else {
					$contest -> membership() -> addMember($viewer) -> setUserApproved($viewer);
				}
				//send notify + mail to members
				$contest-> sendNotMailOwner($viewer, $viewer, 'new_participant', null);
				//send notify to owner
				$user = Engine_Api::_() -> user() -> getUser($contest -> user_id);
				$contest-> sendNotMailOwner($user, $viewer, 'new_participant_f', null);
					
				$db -> commit();
					
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are now a member of this contest.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}

	public function deleteAction(){
			
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->_forward('requireauth', 'error', 'core');
		}
			
		// Get subject and check auth
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_()->core()->getSubject();

		if( !$viewer->isAdminOnly() && !$this->_helper->requireAuth()->setAuthParams($contest, $viewer, 'deletecontests')->isValid() ) {
			return;
		}
		if( $this->getRequest()->isPost() )
		{

			$contest->delete();
		
		
			 return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Contest is deleted successfully.')),
					//'layout' => 'default-simple',
					//'parentRefresh' => true,
			 		'closeSmoothbox' => true,
			 		
			 		'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'yncontest_mycontest', true),
			));

		}
	}

	public function closeAction(){


		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->_forward('requireauth', 'error', 'core');
		}
			
		// Get subject and check auth
		$contest = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if(!$viewer->isAdminOnly())
			if( !$this->_helper->requireAuth()->setAuthParams($contest, $viewer, 'deletecontests')->isValid() ) {
	
				return;
			}

		if($contest->contest_status != 'published' ){
			return $this->_forward('requireauth', 'error', 'core');

		}
		
		// Save values
		if( $this->getRequest()->isPost() )
		{

			$contest->closeContest($viewer);

			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Contest is closed successfully.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}

	public function viewAction()   {
		
		Zend_Registry::set('active_menu','');
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->_forward('requireauth', 'error', 'core');
		}
		$contest = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//hide campaign with inactivated without user
		if(!$contest->isOwner ( $viewer ) && empty($contest->activated))
			 return $this->_helper->requireAuth->forward ();
		
		if($contest->contest_status == 'hide'|| $contest->contest_status == 'draft' ){
			if(!$contest->IsOwner($viewer) && !$viewer->isAdminOnly()){
				$this->_forward('requireauth', 'error', 'core');
				return;
			}
		}
		else{
			//alway display
		}
			
		$contestTable = Engine_Api::_()->getDbtable('contests', 'yncontest');
			
		if( !$contest->IsOwner($viewer)  ) {
			$contestTable->update(array(
					'view_count' => new Zend_Db_Expr('view_count + 1'),
			), array(
					'contest_id = ?' => $contest->getIdentity(),
			));
		}
		// Render
		$this -> _helper -> content -> setEnabled();
	}
	// create contest information
	public function createContestAction(){
		Zend_Registry::set('active_menu','yncontest_main_createcontests');
		//check authoziration
		$viewer = Engine_Api::_()->user()->getViewer();
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'createcontests')->isValid()) return;

		//check plun-in you net module blog, videos, album
		$results = Engine_Api::_()->yncontest()->getPlugins();

		$Plugin = false;
		if(count($results)>0)
			$Plugin = true;
		else
			$Plugin = false;
		$this->view->plugin = $Plugin;

		$this->view->form = $form = new Yncontest_Form_Contest_Create(array(
				'plugin' => $results,
		));

 		$this->view->checkGateway = 1;
 		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        if ((!$gatewayTable -> getEnabledGatewayCount()) && !Engine_Api::_() -> hasModuleBootstrap('yncredit')){
			$this->view->checkGateway = 0;
		}
			
		$this->view->contest_id = $contestID= $this->_getParam('contest', null);

		// If not post or form not valid, return
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}
		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post))
			return ;
		
		// Process
		$table = new Yncontest_Model_DbTable_Contests;
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create contest
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity()));

			//Set viewer time zone
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer->timezone);
			$start_date = strtotime($values['start_date']);
			$end_date = strtotime($values['end_date']);
			$start_date_submit_entries = strtotime($values['start_date_submit_entries']);
			$end_date_submit_entries = strtotime($values['end_date_submit_entries']);
			$start_date_vote_entries = strtotime($values['start_date_vote_entries']);
			$end_date_vote_entries = strtotime($values['end_date_vote_entries']);
			$now = date('Y-m-d H:i:s');
			date_default_timezone_set($oldTz);

			if($values['start_date']< $now){
				$form->addError(sprintf('%s must be greater than or equal to Current Date.', $form->getElement('start_date')->getlabel()));
				return;
			}
			if($values['end_date']< $values['start_date']){
				$form->addError(sprintf('%s must be less than %s.',$form->getElement('start_date')->getlabel(),$form->getElement('end_date')->getlabel()));
				return;
			}
			// check Time Submit Entries
			if($values['start_date_submit_entries']< $now){
				$form->addError(sprintf('%s must be greater than or equal to Current Date.', $form->getElement('start_date_submit_entries')->getlabel()));
				return;
			}
			if($values['end_date_submit_entries']< $values['start_date_submit_entries']){
				$form->addError(sprintf('%s must be less than %s.',$form->getElement('start_date_submit_entries')->getlabel(),$form->getElement('end_date_submit_entries')->getlabel()));
				return;
			}
			// check Time Voting Entries
			if($values['start_date_vote_entries']< $now){
				$form->addError(sprintf('%s must be greater than or equal to Current Date.', $form->getElement('start_date_vote_entries')->getlabel()));
				return;
			}

			if($values['end_date_vote_entries']< $values['start_date_vote_entries']){
				$form->addError(sprintf('%s must be less than %s.',$form->getElement('start_date_vote_entries')->getlabel(),$form->getElement('end_date_vote_entries')->getlabel()));
				return;
			}

			//CHECK TIME FOR SUBMIT ENTRIES
			if($values['start_date_submit_entries'] < $values['start_date'] || $values['end_date_submit_entries'] > $values['end_date'])
			{
				$form->addError(sprintf('%s is between %s and %s.',$form->getElement('start_date_submit_entries')->getlabel(),$form->getElement('start_date')->getlabel(),$form->getElement('end_date')->getlabel()));
				return;
			}

			//CHECK TIME FOR VOTING ENTRIES
			if($values['start_date_vote_entries'] < $values['start_date'] || $values['end_date_vote_entries'] > $values['end_date'])
			{
				$form->addError(sprintf('%s is between %s and %s.',$form->getElement('start_date_vote_entries')->getlabel(),$form->getElement('start_date')->getlabel(),$form->getElement('end_date')->getlabel()));
				return;
			}

			//CHECK TIME START SUBMIT < START VOTE
			if($values['start_date_submit_entries'] > $values['start_date_vote_entries'])
			{
				$form->addError(sprintf('%s must be greater than or equal to %s.',$form->getElement('start_date_vote_entries')->getlabel(),$form->getElement('start_date_submit_entries')->getlabel()));
				return;
			}

			$values['start_date'] = date('Y-m-d H:i:s', $start_date);
			$values['end_date'] = date('Y-m-d H:i:s', $end_date);
			$values['start_date_submit_entries'] = date('Y-m-d H:i:s', $start_date_submit_entries);
			$values['end_date_submit_entries'] = date('Y-m-d H:i:s', $end_date_submit_entries);
			$values['start_date_vote_entries'] = date('Y-m-d H:i:s', $start_date_vote_entries);
			$values['end_date_vote_entries'] = date('Y-m-d H:i:s', $end_date_vote_entries);

			$values['location'] = $values['location_address'];
			$values['latitude'] = $values['lat'];
			$values['longitude'] = $values['long'];

			$contest = $table -> createRow();
			$contest -> setFromArray($values);
			
			//check image
			if(!empty($values['photo'])) {
				
				$file = $form -> photo -> getFileName();
				$info = getimagesize($file);
				if($info[2] > 3 || $info[2] == "") {
					return $form -> getElement('photo') -> addError('The uploaded file is not supported or is corrupt.');
				}				
			}
			
			$contest -> modified_date = date('Y-m-d H:i:s');	;
			
			$contest -> save();
			
			// Set photo
			if(!empty($values['photo'])) {
				$contest -> setPhoto($form -> photo, 0);
			}
			//create album contest for contest photo
			if($contest->contest_type =='album' || $contest->contest_type =='advalbum' ){
				$contest->album_id = $this->createAlbumContest($contest);
				$contest->save();
			}
			
			
			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$contest->tags()->addTagMaps($viewer, $tags);
			
			$db -> commit();

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}


		// Add owner as member
		if(!$contest-> membership() -> isMember($viewer))
			$contest->membership()->addMember($viewer)
			->setUserApproved($viewer)
			->setResourceApproved($viewer);

		//get organizer list
		$organizerList = $contest->getOrganizerList();
		//add ownwer as organizer
		if(!$organizerList->has($viewer))
			$organizerList->add($viewer);

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'create-contest-setting', 'contest'=>$contest->contest_id), 'yncontest_mysetting', true);

	}

	private function createAlbumContest($contest)
	{
		$values = Array();
		$params = Array();
		if ((empty($values['owner_type'])) || (empty($values['owner_id'])))
		{
			$params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
			$params['owner_type'] = 'user';
		}

		$params['title'] = $contest->getTitle();

		$params['description'] = $contest->description;
		$params['search'] = $contest->search;


		$albumPlugin = Engine_Api::_()->yncontest()->getPluginsAlbum();

		$album = Engine_Api::_()->getDbtable('albums', $albumPlugin)->createRow();
		$album->setFromArray($params);
		$album->save();

		// CREATE AUTH STUFF HERE
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

		$values['auth_view'] = 'everyone';
		$values['auth_comment'] = 'owner_member';
		$values['auth_tag'] = 'owner_member';


		$viewMax = array_search($values['auth_view'], $roles);
		$commentMax = array_search($values['auth_comment'], $roles);
		$tagMax = array_search($values['auth_tag'], $roles);

		foreach( $roles as $i => $role ) {
			$auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
			$auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
			$auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
		}

		$file = Engine_Api::_()->getDbtable('files', 'storage')->find($contest->photo_id)->current();

		if($file){
			$contest->createPhoto($album->album_id, $file);
		}

		return $album->album_id;
	}

	public function editContestAction(){
		//check authoziration
		Zend_Registry::set('active_menu','');
		$viewer = Engine_Api::_()->user()->getViewer();
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'editcontests')->isValid()) return;

		//check plun-in you net module blog, videos, album
		$results = Engine_Api::_()->yncontest()->getPlugins();

		$contestID= $this->_getParam('contest', null);
		$this->view->contest_id = $contestID;
		$this -> view -> contest = $contest = Engine_Api::_() -> getItem('yncontest_contest', $contestID);
		if(count($contest) == 0)
			return $this->_forward('requireauth', 'error', 'core');
		
		if($contest->contest_status == "denied") 
			$contest->contest_status = "draft";
		
		$contest->save();
		
		$this->view->form = $form = new Yncontest_Form_Contest_Create(array(
				'plugin' => $results,
				'contest' => $contest,
				'location' => $contest -> location,
		));
		$form -> getElement('photo') -> setRequired(false);
		$form -> getElement('photo') -> setLabel("Contest Image");

		$timeArray = array();
		
        $start_date = strtotime($contest->start_date);
      	$end_date = strtotime($contest->end_date);
		$start_date_submit_entries = strtotime($contest->start_date_submit_entries);
		$end_date_submit_entries = strtotime($contest->end_date_submit_entries);
		$start_date_vote_entries = strtotime($contest->start_date_vote_entries);
		$end_date_vote_entries = strtotime($contest->end_date_vote_entries);
      	$oldTz = date_default_timezone_get();
      	date_default_timezone_set($viewer->timezone);
      	$timeArray['start_date'] = date('Y-m-d H:i:s', $start_date);
      	$timeArray['end_date'] = date('Y-m-d H:i:s', $end_date);
		$timeArray['start_date_submit_entries'] = date('Y-m-d H:i:s', $start_date_submit_entries);
		$timeArray['end_date_submit_entries'] = date('Y-m-d H:i:s', $end_date_submit_entries);
		$timeArray['start_date_vote_entries'] = date('Y-m-d H:i:s', $start_date_vote_entries);
		$timeArray['end_date_vote_entries'] = date('Y-m-d H:i:s', $end_date_vote_entries);
     	date_default_timezone_set($oldTz);
		
		$array = $contest->toArray();
		$array = array_merge($array, $timeArray);
		
		//remove some element
		if($contest->contest_status != 'draft' || $contest->approve_status == 'pending' ){			
			$form->removeElement('contest_name');
			$form->removeElement('tags');			
			$form->removeElement('contest_type');
			$form->removeElement('photo');
		}

		$array['location_address'] = $array['location'];
		$array['lat'] = $array['latitude'];
		$array['long'] = $array['longitude'];
		$form->populate($array);

		$tagStr = '';
		foreach( $contest->tags()->getTagMaps() as $tagMap ) {
			$tag = $tagMap->getTag();
			if( !isset($tag->text) ) continue;
			if( '' !== $tagStr ) $tagStr .= ', ';
			$tagStr .= $tag->text;
		}
		$form->populate(array(
				'tags' => $tagStr,
		));

		// If not post or form not valid, return
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}
		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post))
			return ;

		// Process
		$table = new Yncontest_Model_DbTable_Contests;
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Edit Contest
			$values = array_merge($form -> getValues());
			if($contest->contest_status != 'draft' || $contest->approve_status == 'pending' ){
				unset($values['start_date']);
				unset($values['end_date']);
				unset($values['start_date_submit_entries']);
				unset($values['end_date_submit_entries']);
				unset($values['start_date_vote_entries']);
				unset($values['end_date_vote_entries']);
			}
			else
			{
				$oldTz = date_default_timezone_get();
				date_default_timezone_set($viewer->timezone);
				$start_date = strtotime($values['start_date']);
				$end_date = strtotime($values['end_date']);
				$start_date_submit_entries = strtotime($values['start_date_submit_entries']);
				$end_date_submit_entries = strtotime($values['end_date_submit_entries']);
				$start_date_vote_entries = strtotime($values['start_date_vote_entries']);
				$end_date_vote_entries = strtotime($values['end_date_vote_entries']);
				$now = date('Y-m-d H:i:s');
				date_default_timezone_set($oldTz);

				if($values['start_date']< $now){
					$form->addError(sprintf('%s must be greater than or equal to Current Date.', $form->getElement('start_date')->getlabel()));
					return;
				}
				if($values['end_date']< $values['start_date']){
					$form->addError(sprintf('%s must be less than %s.',$form->getElement('start_date')->getlabel(),$form->getElement('end_date')->getlabel()));
					return;
				}
				// check Time Submit Entries
				if($values['start_date_submit_entries']< $now){
					$form->addError(sprintf('%s must be greater than or equal to Current Date.', $form->getElement('start_date_submit_entries')->getlabel()));
					return;
				}
				if($values['end_date_submit_entries']< $values['start_date_submit_entries']){
					$form->addError(sprintf('%s must be less than %s.',$form->getElement('start_date_submit_entries')->getlabel(),$form->getElement('end_date_submit_entries')->getlabel()));
					return;
				}
				// check Time Voting Entries
				if($values['start_date_vote_entries']< $now){
					$form->addError(sprintf('%s must be greater than or equal to Current Date.', $form->getElement('start_date_vote_entries')->getlabel()));
					return;
				}

				if($values['end_date_vote_entries']< $values['start_date_vote_entries']){
					$form->addError(sprintf('%s must be less than %s.',$form->getElement('start_date_vote_entries')->getlabel(),$form->getElement('end_date_vote_entries')->getlabel()));
					return;
				}

				//CHECK TIME FOR SUBMIT ENTRIES
				if($values['start_date_submit_entries'] < $values['start_date'] || $values['end_date_submit_entries'] > $values['end_date'])
				{
					$form->addError(sprintf('%s is between %s and %s.',$form->getElement('start_date_submit_entries')->getlabel(),$form->getElement('start_date')->getlabel(),$form->getElement('end_date')->getlabel()));
					return;
				}

				//CHECK TIME FOR VOTING ENTRIES
				if($values['start_date_vote_entries'] < $values['start_date'] || $values['end_date_vote_entries'] > $values['end_date'])
				{
					$form->addError(sprintf('%s is between %s and %s.',$form->getElement('start_date_vote_entries')->getlabel(),$form->getElement('start_date')->getlabel(),$form->getElement('end_date')->getlabel()));
					return;
				}

				//CHECK TIME START SUBMIT < START VOTE
				if($values['start_date_submit_entries'] > $values['start_date_vote_entries'])
				{
					$form->addError(sprintf('%s must be greater than or equal to %s.',$form->getElement('start_date_vote_entries')->getlabel(),$form->getElement('start_date_submit_entries')->getlabel()));
					return;
				}

				$values['start_date'] = date('Y-m-d H:i:s', $start_date);
				$values['end_date'] = date('Y-m-d H:i:s', $end_date);
				$values['start_date_submit_entries'] = date('Y-m-d H:i:s', $start_date_submit_entries);
				$values['end_date_submit_entries'] = date('Y-m-d H:i:s', $end_date_submit_entries);
				$values['start_date_vote_entries'] = date('Y-m-d H:i:s', $start_date_vote_entries);
				$values['end_date_vote_entries'] = date('Y-m-d H:i:s', $end_date_vote_entries);
			}
			
			if(!empty($values['photo'])) {
				$file = $form -> photo -> getFileName();
				$info = getimagesize($file);
				if($info[2] > 3 || $info[2] == "") {
					$form -> getElement('photo') -> addError('The uploaded file is not supported or is corrupt.');
				}
				
				$contest -> setPhoto($form -> photo, 0);
			}
			$values['location'] = $values['location_address'];
			$values['latitude'] = $values['lat'];
			$values['longitude'] = $values['long'];

			$contest -> setFromArray($values);
			$contest -> modified_date = date('Y-m-d H:i:s');
			if($contest->approve_status == 'denied') $contest->approve_status ='new';
			$contest -> save();
			$contest->sendNotMailFollwer($viewer, 'contest_edited', array());
			$db -> commit();


		} catch( Exception $e ) {
			$db -> rollBack();
			if(APPLICATION_ENV == 'developement'){
				throw $e;
			}
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'view', 'contestId'=>$contest->contest_id), 'yncontest_mycontest', true);
	}

	public function printViewAction()
	{
		$contest_id = (int) $this->_getParam('contestId');
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_()->getItem('yncontest_contest', $contest_id);

		if(!$contest || (Engine_Api::_()->getApi('settings', 'core')->getSetting('yncontest .print', 0) == 0 && !$viewer->getIdentity()))
			return $this->_helper->requireAuth->forward();
		else
			$this->_helper->layout->disableLayout();
		$this->view->contest = $contest;
		$this->view->arrPlugins =Engine_Api::_()->yncontest()->getPlugins();
	}

	public function changeMultiLevelAction() {


		$category_id = $this->_getParam('id');
		$model_class =  $this->_getParam('model');
		$name =  $this->_getParam('name');
		$level = $this->_getParam('level');
		$model =  new $model_class;
		$item =  $model->find((string)$category_id)->current();

		if(!is_object($item)){
			return ;
		}
		if ($level != ($item->level - 1)) {
			return;
		}
		$options =  $model->getMultiOptions($item->getIndexTree($item->getLevel()));
		if(count($options)<2){
			return ;
		}

		$element = new Zend_Form_Element_Select(
				sprintf("%s_%s",$name, $level+1),
				array(
						'multiOptions'=> $options,
						'onchange'=>"en4.yncontest.changeCategory($(this),'".$name."','".$model_class."','my-contest')",
				)
		);
			
			
		echo $element->renderViewHelper();

	}
	public function serviceAction(){

		$viewer = Engine_Api::_()->user()->getViewer();
		//check authoziration
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'createcontests')->isValid()) return;
		$contest_id = $this->_getParam('id', null);

		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);

		if($contest == null || ($contest->user_id != $viewer->getIdentity() && $viewer->level_id !=1 && $viewer->level_id!=2))
			$this->_forward('requireauth', 'error', 'core');

		if($contest) {
			Engine_Api::_() -> core() -> setSubject($contest);
		}
		$this->view->form = $form = new Yncontest_Form_Contest_Publish(array(
				'contest' => $contest,
		));

		$form->setAttrib("class", "global_form_popup");

		if($contest->contest_status == 'published')
			$form->removeElement('publishC_fee');
		if($contest->featured_id == 1)
			$form->removeElement('feature_fee');
		if($contest->endingsoon_id == 1)
			$form->removeElement('endingsoon_fee');
		if($contest->premium_id == 1)
			$form->removeElement('premium_fee');

		// If not post or form not valid, return
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();

		if(!$form -> isValid($post))
			return ;
		// Process
		$table = new Yncontest_Model_DbTable_Transactions;
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {

			$values = array_merge($form -> getValues(), array('user_seller' => '1', 'user_buyer' => $viewer -> getIdentity(),'contest_id' => $contest->contest_id ));

			if($values['feature_fee']){
				$feature_fee = Engine_Api::_()->yncontest()->getFeeContest($viewer, 'featureC_fee');
				if(!$feature_fee)
					$feature_fee = 0;
			}
			if($values['premium_fee']){
				$premium_fee = Engine_Api::_()->yncontest()->getFeeContest($viewer, 'premiumC_fee');
				if(!$premium_fee)
					$premium_fee = 0;
			}
			if($values['endingsoon_fee']){
				$endingsoon_fee = Engine_Api::_()->yncontest()->getFeeContest($viewer, 'endingsoonC_fee');
				if(!$endingsoon_fee)
					$endingsoon_fee = 0;
			}

			$now = date('Y-m-d H:i:s');
			$values['transaction_date'] = $now;
			$values['transaction_status'] = 'pending';
			$values['approve_status'] = 'pending';
			$values['currency'] = Engine_Api::_()->getApi('core','yncontest')->getDefaultCurrency();
			$values['security'] = Yncontest_Api_Cart::getSecurityCode();
			$values['number'] = 1;

			if(
					(isset($values['feature_fee']) && $values['feature_fee'] == 1) ||
					(isset($values['premium_fee']) && $values['premium_fee'] == 1) ||
					(isset($values['endingsoon_fee']) && $values['endingsoon_fee'] == 1)
			){

			}
			else{
				$form->addError("Please choose fee publish");
				return;
			}


			if($feature_fee>0 && $values['feature_fee'] == 1){
				$values['option_service'] = 2;
				$values['amount'] = $feature_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}
			if($premium_fee>0 && $values['premium_fee'] == 1){
				$values['option_service'] = 3;
				$values['amount'] = $premium_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}
			if($endingsoon_fee>0 && $values['endingsoon_fee'] == 1){
				$values['option_service'] = 4;
					
				$values['amount'] = $endingsoon_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}

			$db -> commit();

		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		if($feature_fee +  $premium_fee +  $endingsoon_fee == 0){
			if($values['feature_fee'] == 1){
				$contest->featured_id = 1	;
			}
			if($values['premium_fee'] == 1){
				$contest->premium_id = 1	;
			}
			if($values['endingsoon_fee'] == 1){
				$contest->endingsoon_id = 1	;
			}
			$contest->save();
			$view = $this->_getParam('view', null);
			if($view){
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thanks to update service!')),
						'closeSmoothbox' => true,
						'parentRefresh' => true,
				));
			}
		}
		$url = "http://".$_SERVER['SERVER_NAME'].Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'method','contestId'=>$contest_id, 'id'=>$values['security']), 'yncontest_payment', true);
		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have just choosen services.')),
				'closeSmoothbox' => true,
				'parentRedirect' => $url,
		));

	}

	public function publishAction(){

		$contest_id = $this->_getParam('contest', null);

		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);

		if($contest == null || (!$contest->IsOwner($viewer)))
			$this->_forward('requireauth', 'error', 'core');


		if($contest) {
			Engine_Api::_() -> core() -> setSubject($contest);
		}

		//check authoziration
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'createcontests')->isValid()) return;
		//requrie contest_id


		$this->view->form = $form = new Yncontest_Form_Contest_Publish(array(
				'contest' => $contest,
		));


		if(!$contest->checkPublish()){
				
			$form->setDescription("The contest have not completed. Please complete before publishing.");
			$form -> removeElement('publishC_fee');
			$form -> removeElement('feature_fee');
			$form -> removeElement('premium_fee');
			$form -> removeElement('endingsoon_fee');
			$form -> removeElement('submit');
		}

		// If not post or form not valid, return
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}

		$post = $this -> getRequest() -> getPost();


		if(!$form -> isValid($post))
			return ;
		// Process
		$table = new Yncontest_Model_DbTable_Transactions;
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$values = array_merge($form -> getValues(), array('user_seller' => '1', 'user_buyer' => $contest->user_id,'contest_id' => $contest->contest_id ));
			$user = Engine_Api::_() -> user() -> getUser($contest -> user_id);
			$publish_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'publishC_fee');
			if(!$publish_fee )
				$publish_fee = 0;
			$feature_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'featureC_fee');
			if(!$feature_fee || !$values['feature_fee'])
				$feature_fee = 0;
			$premium_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'premiumC_fee');
			if(!$premium_fee || !$values['premium_fee'])
				$premium_fee = 0;
			$endingsoon_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'endingsoonC_fee');
			if(!$endingsoon_fee || !$values['endingsoon_fee'])
				$endingsoon_fee = 0;
				
			if($feature_fee ==0 && $values['feature_fee'] == 1){
				$contest->featured_id = 1	;
			}
			if($premium_fee== 0 && $values['premium_fee'] == 1){
				$contest->premium_id = 1	;
			}
			if($endingsoon_fee ==0 && $values['endingsoon_fee'] == 1){
				$contest->endingsoon_id = 1	;
			}
			
			// check fee of user. if 0 --> auto approve	& dont write transaction
			if($publish_fee + $feature_fee +  $premium_fee +  $endingsoon_fee == 0){					
				$approve = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.approval', 0);

				if($approve == 1){
					$contest->approve_status = 'approved';
					$contest->contest_status = 'published';
					$contest->approved_date = date('Y-m-d H:i:s');


					// add activity action
					$owner = Engine_Api::_() -> user() -> getUser($contest -> user_id);
					$admin = Engine_Api::_() -> user() -> getUser(1);
					$contest->sendNotMailOwner($owner, $admin, 'contest_approved', 'yncontest_new' );
				}
				else{
					$contest->approve_status = 'pending';
					$contest->contest_status = 'waiting';
				}
				$contest->save();
				$db -> commit();
					
				$view = $this->_getParam('view', null);
				if($view){
					return $this -> _forward('success', 'utility', 'core', array(
							'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thanks to publish!')),
							'closeSmoothbox' => true,
							'parentRefresh' => true,
					));
				}
				return $this -> _helper -> redirector -> gotoRoute(array('action' => 'view', 'contestId'=>$contest_id , 'slug' => $contest -> getSlug()), 'yncontest_mycontest', true);
					
			}

			$values['approve_status'] = 'pending';
			$values['transaction_status'] =  'pending';
			$values['transaction_date'] =  date('Y-m-d H:i:s');
			$values['currency'] = Engine_Api::_()->getApi('core','yncontest')->getDefaultCurrency();
			$values['security'] = Yncontest_Api_Cart::getSecurityCode();
			$values['number'] = 1;

			//publish fee transaction
			if($publish_fee>0){
				$values['option_service'] = 1;
				$values['amount'] = $publish_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}

			if($feature_fee>0 && $values['feature_fee'] == 1){
				$values['option_service'] = 2;
				$values['amount'] = $feature_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}
			if($premium_fee >0 && $values['premium_fee'] == 1){
				$values['option_service'] = 3;
				$values['amount'] = $premium_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}
			if($endingsoon_fee>0 && $values['endingsoon_fee'] == 1){
				$values['option_service'] = 4;
				$values['amount'] = $endingsoon_fee;
				$transaction = $table -> createRow();
				$transaction -> setFromArray($values);
				$transaction->save();
			}

			$contest->save();
			$db -> commit();


		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$view = $this->_getParam('view', null);
		if($view){
			$url = "http://".$_SERVER['SERVER_NAME'].Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'method','contestId'=>$contest_id, 'id'=>$values['security']), 'yncontest_payment', true);
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have just choosen services.')),
					'closeSmoothbox' => true,
					'parentRedirect' => $url,
			));
		}
			
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'method',  'contestId'=> $contest_id, 'id'=>$values['security']), 'yncontest_payment', true);

	}
	public function publishAdminAction(){

		$contest_id = $this->_getParam('contest', null);

		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);

		if($contest == null )
			$this->_forward('requireauth', 'error', 'core');


		if($contest) {
			Engine_Api::_() -> core() -> setSubject($contest);
		}

		if(!$contest->checkPublish()){
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The contest have not completed. Please complete before publishing.')),
					'closeSmoothbox' => true,
					'parentRefresh' => true,
			));
		}
		else{
			$contest->approve_status = 'approved';
			$contest->contest_status = 'published';
			$contest->approved_date = date('Y-m-d H:i:s');
			$contest->save();
		}

		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thanks to publish!')),
				//'layout' => 'default-simple',
				'closeSmoothbox' => true,
				'parentRefresh' => true,
		));


	}

	public function termAction(){
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->_forward('requireauth', 'error', 'core');
		}
		$this->view->contest = Engine_Api::_()->core()->getSubject();
	}

	public function followAction()
	{

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = (int) $this->_getParam('contestId');
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if(!$contest)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('contests', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if( $contest->checkFollow())
			{
				$follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
				$follow = $follow_table->createRow();
				$follow->contest_id = $contest->contest_id;
				$follow->user_id  = $viewer->getIdentity();
				$follow->save();
				$contest->follow_count = $contest->follow_count + 1;
				$contest->save();
				$db->commit();
				echo Zend_Json::encode(array('success'=>1));
			}

		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}
	public function unFollowAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = (int) $this->_getParam('contestId');
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if(!$contest)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('contests', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if(!$contest->checkFollow())
			{
				$follow =  Engine_Api::_()->yncontest()->getFollow($viewer->getIdentity(), $contest_id);
				$follow->delete();
				$contest->follow_count = $contest->follow_count - 1;
				$contest->save();
				$db->commit();
			}
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format'=> 'smoothbox',
					'messages' => array($this->view->translate('Unfollow successfully.'))
			));

		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}
	public function unFollowAjaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = (int) $this->_getParam('contestId');
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if(!$contest)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('contests', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if(!$contest->checkFollow())
			{
				$follow =  Engine_Api::_()->yncontest()->getFollow($viewer->getIdentity(), $contest_id);
				$follow->delete();
				$contest->follow_count = $contest->follow_count - 1;
				$contest->save();
				$db->commit();
			}
			echo Zend_Json::encode(array('success'=>1));

		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}

	public function favouriteContestAction(){
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = $this->_getParam('contestId');

		if ($viewer->getIdentity() == 0) {
			$this->view->signin = 0;
		}
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if ($contest->isFavourited($user_id)) {
			Yncontest_Api_FavouriteContests::getInstance()->deleteFavouriter($user_id, $contest_id);
			$this->view->favourite = 0;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Favorite');
		}
		else { Yncontest_Api_FavouriteContests::getInstance()->addFavouriter($user_id, $contest_id);
		$this->view->favourite = 1;
		$this->view->text = Zend_Registry::get('Zend_Translate')->_('Unfavorite');
		}
	}

	public function favouriteAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = (int) $this->_getParam('contestId');
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if(!$contest)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid()) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('contests', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if( $contest->checkFavourite())
			{
				$favourite_table = Engine_Api::_()->getItemTable('yncontest_favourite');
				$favourite = $favourite_table->createRow();
				$favourite->contest_id = $contest->contest_id;
				$favourite->user_id  = $viewer->getIdentity();
				$favourite->save();
				$contest->favourite_count = $contest->favourite_count + 1;
				$contest->save();
				$db->commit();
			}
			echo Zend_Json::encode(array('success'=>1));

		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}
	public function unFavouriteAjaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = (int) $this->_getParam('contestId');
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if(!$contest)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('contests', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if($contest)
			{
				if(!$contest->checkFavourite())
				{
					$favourite =  Engine_Api::_()->yncontest()->getFavourite($viewer->getIdentity(), $contest_id);
					$favourite->delete();
					$contest->favourite_count = $contest->favourite_count - 1;
					$contest->save();
					$db->commit();
				}
				echo Zend_Json::encode(array('success'=>1));
			}
		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}

	public function createAnnounceAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_()->getItem('contest', $this->_getParam("contestId"));
		if(!$contest->isOwner($viewer)){
			$this->renderScript("_error.tpl");
			return;
		}
		$this->view->form = $form = new Yncontest_Form_Announcement_Create();

		$announcement = Engine_Api::_()->getItem('yncontest_announcements', $this->_getParam("announce"));
		//edit announcement
		if($announcement){
			$form->populate($announcement->toArray());

			$form->setTitle("Edit Announcement");

		}



		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
			$params = $form->getValues();
			if(!$announcement){
				$params['contest_id'] = $contest->contest_id;
				$params['start_date']  = date('Y-m-d H:i:s');
				$announcement = Engine_Api::_()->getDbtable('announcements', 'yncontest')->createRow();
			}

			$announcement->setFromArray($params);
			$announcement->save();

			//send notification to members
			$members = $contest->membership()->getMembers($contest->getIdentity(), true);
			foreach($members as $member){
				$contest->sendNotMailOwner($member, $viewer, 'create_annoucement', null);
			}



			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
	}



	protected function getFriendIds(User_Model_User $user) {
		$ids = array(0);
		$friends = $user -> membership() -> getMembers();
		foreach ($user -> membership() -> getMembersInfo() as $row) {
			$ids[] = $row -> user_id;
		}
		return $ids;
	}
	protected function getUserFriends(User_Model_User $user, $name = null) {
		$friendIds = $this -> getFriendIds($user);
		$user_table = Engine_Api::_() -> getItemTable('user');

		$select = $user_table -> select()
		-> where('user_id IN (?)', $friendIds)
		-> where('displayname is NOT NULL')
		-> where('displayname <> ?', '')
		-> order('displayname ASC');

		if (!empty($name)) {
			$select -> where('displayname like ?', "%" . $name . "%");
		}
		return $user_table -> fetchAll($select);
	}

	public function inviteMembersAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
			
		//requrie contest_id
		$contest_id = $this->_getParam('contestId', null);
		if($contest_id == null)
			$this->_forward('requireauth', 'error', 'core');
			
		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> contest = $contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
			
		if(!$contest) {
			$this->_forward('requireauth', 'error', 'core');
		}
			
		// Prepare data		
		$this -> view -> friends = $friends = $this -> getUserFriends($viewer);
		// Prepare form
		$this -> view -> friend_form = $friend_form = new Yncontest_Form_Contest_InviteMembers();
		$this -> view -> friend_count = $friend_count = 0;
		
		foreach ($friends as $friend) {
			if(!$contest -> membership() -> isMember($friend, true))		
			{
				$friend_form -> friends -> addMultiOption($friend -> getIdentity(), $friend -> getTitle());
				$friend_count++;
			}
		}
		
			
		// throw notice if count = 0
		if ($friend_count == 0) {
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Currently, there are no members you can invite.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
			
		$this -> view -> friend_count = $friend_count;
		//$this -> view -> user_count = $user_count;
			
		// Not posting
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
			
		// Process
		$table = $contest -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();
			
		try {
			$friendsIds = $this -> getRequest() -> getPost('friends');
			//$usersIds = $this -> getRequest()-> getPost('users');

			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			if (!empty($friendsIds)) {
				$friends = Engine_Api::_()->getItemMulti('user',$friendsIds);
				foreach ($friends as $friend) {					
					$notifyApi -> addNotification($friend, $viewer, $contest, 'contest_invite');
				}
			}
			$db -> commit();
			if (!empty($friendsIds)) {
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited')),
						'layout' => 'default-simple',
						'parentRefresh' => true,
				));
			}
			else{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('No members invited')),
						'layout' => 'default-simple',
						'parentRefresh' => true,
				));
			}
		}
			
		catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function ajaxAction() {
		// Disable layout
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$text = $this -> _getParam('text');
		$mode = $this -> _getParam('mode');

		$contest_id = $this -> _getParam('contestId');
		$contest = Engine_Api::_() -> getItem('contest', $contest_id);

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($mode == "users") {
			$items = $this -> getMembersAll($viewer, $text, $contest_id);
		}
		else {
			$items = $this -> getUserFriends($viewer, $text);
		}	
		
		$item_arr = array();
		if (count($items) > 0) {
			foreach ($items as $item) {
				$item_arr[] = array(
						//'photo' => $item -> getPhotoUrl('thumb.icon'),
						'id' => $item -> getIdentity(),
						'title' => $item -> getTitle()
				);
			}


		}
		$this -> view -> rows = $item_arr;
		$this -> view -> total = count($item_arr);
	}

	public function editAnnounceAction()
	{
		$announce = Engine_Api::_()->getItem('yncontest_announcements', $this->_getParam("announce"));
		if(!$announce){
			return $this->_forward('requireauth', 'error', 'core');

		}

		if( !$this->_helper->requireAuth()->setAuthParams($contest, $viewer, 'editcontests')->isValid() ) {
			return;
		}



		$this->view->form = $form = new Yncontest_Form_Announcement_Edit();
		$form->populate($announce->toArray());

		// Save values
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
			$params = $form->getValues();
			$announce->setFromArray($params);
			$announce->save();
			$this->_forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}



	}

	public function deleteAnnounceAction()
	{

		$announce = Engine_Api::_()->getItem('yncontest_announcements', $this->_getParam("announce"));
		if(!$announce){
			return $this->_forward('requireauth', 'error', 'core');

		}


		// Save values
		if( $this->getRequest()->isPost() )
		{
			$announce->delete();
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh'=> 10,
					'messages' => array('Announcement is deleted successfully.')
			));
		}
	}

	public function unFavouriteAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = (int) $this->_getParam('contestId');
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if(!$contest)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('contests', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if($contest)
			{
				if(!$contest->checkFavourite())
				{
					$favourite =  Engine_Api::_()->yncontest()->getFavourite($viewer->getIdentity(), $contest_id);
					$favourite->delete();
					$contest->favourite_count = $contest->favourite_count - 1;
					$contest->save();
					$db->commit();
				}
				$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => true,
						'parentRefresh' => true,
						'format'=> 'smoothbox',
						'messages' => array($this->view->translate('Unfavourite successfully.'))
				));
			}
		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}

	public function detailAction(){

		$this->_helper->content
		->setNoRender()
		->setEnabled();
		//require user
		if( !$this->_helper->requireUser()->isValid() ) return;

		//requrie contest_id
		$contest_id = $this->_getParam('contest', null);
		if($contest_id == null)
			$this->_forward('requireauth', 'error', 'core');

		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
		if($contest == null || $contest->user_id != $viewer->getIdentity())
			$this->_forward('requireauth', 'error', 'core');;


		if($contest) {
			Engine_Api::_() -> core() -> setSubject($contest);
		}
	}

	public function favcontestAction()
	{
		if( !$this->_helper->requireUser()->isValid() ) return;
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		Zend_Registry::set('active_mini_menu','favcontest');
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	public function followcontestAction()
	{
		// Render
		if( !$this->_helper->requireUser()->isValid() ) return;
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		Zend_Registry::set('active_mini_menu','followcontest');
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	
	
	
}
