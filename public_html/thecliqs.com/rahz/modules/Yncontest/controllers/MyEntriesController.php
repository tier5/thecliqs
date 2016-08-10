<?php

class Yncontest_MyEntriesController extends Core_Controller_Action_Standard
{
	public function init(){
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireAuth() -> setAuthParams('contest', $viewer, 'view') -> isValid())
			return;

		if (!$this -> _helper -> requireAuth() -> setAuthParams('contest', $viewer, 'viewentries') -> isValid())
			return;
		$entry = Engine_Api::_() -> getItem('yncontest_entries', $this -> _getParam('id', null));

		if($entry) {			
			Engine_Api::_() -> core() -> setSubject($entry);
		}
	}

	public function indexAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function submitAction(){


		$entries = Engine_Api::_()->getItemTable('yncontest_entries')->find($this->_getParam('id', null))->current();


		$setting = Engine_Api::_()->getDbtable('settings', 'yncontest')->getSettingByContest($entries->contest_id);

		if($setting->entries_approve  == 1){
			$entries->approve_status = 'approved';
			$entries->entry_status = 'published';
			//$setting->max_entries--;
			$setting->save();
			$entries->save();
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thank you for your submission.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
		else{
			$entries->entry_status = 'pending';
			$entries->save();
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thank you for your submission. Please waiting approve.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}


	}

	public function approveEntryAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);


		if (!$entries)
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Save values


		$entries->entry_status='published';
		$entries->approve_status='approved';
		$entries->approved_date  = date('Y-m-d H:i:s');
		$entries -> save();

		//$viewer = Engine_Api::_()->user()->getViewer();
		$user = Engine_Api::_() -> user() -> getUser($entries -> user_id);
		
		$entries-> sendNotMailOwner($user, $user, 'submit_entry', null);

		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Entry is approved.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));

	}
	public function denyEntryAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);


		if (!$entries)
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Save values


		if ($this -> getRequest() -> isPost())
		{
			//$entries->entry_status='draft';
			//$entries->approve_status='new';
				
			$entries->approve_status='denied';
			$entries -> save();
				
			//send notification & mail
			$user = Engine_Api::_() -> user() -> getUser($entries -> user_id);
			$entries->sendNotMailOwner($user, $entries, 'entry_denied', null );
				
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Entry is denied.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}



	}
	public function ajaxEntriesAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$contestType = $request -> getParam('type');

		$params = array();
		$params['item'] = $request -> getParam('id');
		if ($contestType == 'ynblog')
		{
			$this -> ajaxEntriesBlogAction($params);
		}
		elseif ($contestType == 'advalbum')
		{
			$this -> ajaxEntriesPhotoAction($params);
		}
		elseif ($contestType == 'ynvideo')
		{
			$this -> ajaxEntriesVideoAction($params);
		}
		else
		{
			//null
		}

	}

	public function favouriteAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'viewentries')->isValid()) {
			return;
		}


		$entry_id = (int) $this->_getParam('entry_id');
		$entry = Engine_Api::_()->getItem('yncontest_entry', $entry_id);
		if(!$entry)
			return $this->_helper->requireAuth->forward();

		$db = Engine_Api::_()->getDbtable('entriesfavourites', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if( $entry->checkFavourite($entry->contest_id))
			{
				$favourite_table = Engine_Api::_()->getItemTable('yncontest_entriesfavourites');
				$favourite = $favourite_table->createRow();
				$favourite->entry_id = $entry->entry_id;
				$favourite->contest_id = $entry->contest_id;
				$favourite->user_id  = $viewer->getIdentity();
				$favourite->save();
				$entry->favourite_count = $entry->favourite_count + 1;
				$entry->save();
				$db->commit();
			}
			echo Zend_Json::encode(array('success'=>1));
				
		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}
	public function followAction()
	{

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$entry_id = (int) $this->_getParam('entry_id');
		$entry = Engine_Api::_()->getItem('yncontest_entry', $entry_id);
		if(!$entry)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'viewentries')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('entries', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
				
			if( $entry->checkFollow($entry->contest_id))
			{
				$follow_table = Engine_Api::_()->getItemTable('yncontest_entriesfollows');
				$follow = $follow_table->createRow();
				$follow->entry_id =$entry_id;
				$follow->contest_id = $entry->contest_id;
				$follow->user_id  = $viewer->getIdentity();
				$follow->save();
				$entry->follow_count = $entry->follow_count + 1;
				$entry->save();
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
		$entry_id= (int) $this->_getParam('entry_id');
		$entry = Engine_Api::_()->getItem('yncontest_entry', $entry_id);
		if(!$entry)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'viewentries')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('entries', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if(!$entry->checkFollow($entry->contest_id))
			{
				$follow =  Engine_Api::_()->yncontest()->getEntryFollow($viewer->getIdentity(), $entry->contest_id, $entry_id);
				$follow->delete();
				$entry->follow_count = $entry->follow_count - 1;
				$entry->save();
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
		$entry_id= (int) $this->_getParam('entry_id');
		$entry = Engine_Api::_()->getItem('yncontest_entry', $entry_id);
		if(!$entry)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'viewentries')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('entries', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
			if(!$entry->checkFollow($entry->contest_id))
			{
				$follow =  Engine_Api::_()->yncontest()->getEntryFollow($viewer->getIdentity(), $entry->contest_id, $entry_id);
				$follow->delete();
				$entry->follow_count = $entry->follow_count - 1;
				$entry->save();
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
		$entry_id = (int) $this->_getParam('entry_id');
		$entry = Engine_Api::_()->getItem('yncontest_entry', $entry_id);
		if(!$entry)
			return $this->_helper->requireAuth->forward();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'viewentries')->isValid() ) {
			return;
		}
		$db = Engine_Api::_()->getDbtable('entriesfavourites', 'yncontest')->getAdapter();
		$db->beginTransaction();
		try {
				
			if(!$entry->checkFavourite($entry->contest_id))
			{
				$favourite =  Engine_Api::_()->yncontest()->getFavouriteEntry($viewer->getIdentity(), $entry->contest_id,$entry_id);
				$favourite->delete();
				$entry->favourite_count = $entry->favourite_count - 1;
				$entry->save();
				$db->commit();
			}
			echo Zend_Json::encode(array('success'=>1));
				
		} catch (Exception $e) {
			$db->rollback();
			$this->view->success = false;
			throw $e;
		}
	}


	public function deleteAction()
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if (!$entries)
			return $this -> _helper -> requireAuth -> forward();
		// Get subject and check auth

		if (!$this -> _helper -> requireAuth() -> setAuthParams('contest', $viewer, 'deleteentries') -> isValid())
		{
			return;
		}
		if ($this -> getRequest() -> isPost())
		{

			$entries -> delete();

			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Entry is deleted successfully.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));

		}

	}

	public function favouriteEntriesAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = $this -> _getParam('entriesId');

		if ($viewer -> getIdentity() == 0)
		{
			$this -> view -> signin = 0;
		}
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if ($entries -> isFavourited($user_id))
		{
			Yncontest_Api_FavouriteEntries::getInstance() -> deleteFavouriter($user_id, $entry_id);
			$this -> view -> favourite = 0;
			$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _('Favorite');
		}
		else
		{
			Yncontest_Api_FavouriteEntries::getInstance() -> addFavouriter($user_id, $entry_id);
			$this -> view -> favourite = 1;
			$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _('Unfavourite');
		}
	}

	public function ajaxSearchEntriesAction(){
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//List params for getting new blogs
		$recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}
		$recentCol = $recentType . '_date';
		
		$entry_id = $request -> getParam('id');
		$contest_id = $request -> getParam('contest_id');
		
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');		
		$select = $table -> select() -> where('entry_id =?', $entry_id) ;
		
		$paginator = Zend_Paginator::factory($select);
		$items_per_page  = 12;
		$paginator -> setItemCountPerPage($request -> getParam($items_per_page));
		$paginator -> setCurrentPageNumber($request -> getParam(1));
		$view = Zend_Registry::get('Zend_View');
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_entries.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'contestId' => $contest_id,
				'tab' => 1,
		));		
	}
	public function ajaxSearchWinEntriesAction(){
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();
	
		//List params for getting new blogs
		$recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}
		$recentCol = $recentType . '_date';
	
		$entry_id = $request -> getParam('id');
		$contest_id = $request -> getParam('contest_id');
	
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$select = $table -> select() -> where('entry_id =?', $entry_id) ;
	
		$paginator = Zend_Paginator::factory($select);
		$items_per_page  = 12;
		$paginator -> setItemCountPerPage($request -> getParam($items_per_page));
		$paginator -> setCurrentPageNumber($request -> getParam(1));
		$view = Zend_Registry::get('Zend_View');
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_win_entries.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'contestId' => $contest_id,
				'tab' => 'tab21',
		));
	}

	public function ajaxTabEntriesAction($params = array())
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> recentType = $recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}
		$recentCol = $recentType . '_date';
		$contest_id = $request -> getParam('contestId');

		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');

		$tab = $request -> getParam('tab');

		if($tab == '1'){
			$select = $table -> select() -> where('contest_id =?', $contest_id) -> where("entry_status = 'published' or entry_status = 'win'") -> where("approve_status = 'approved'") -> order('start_date');
		}
		elseif($tab == '2'){
			$select = $table -> select() -> where('contest_id =?', $contest_id) -> where("approve_status = 'pending' ")-> order('start_date');
		}

		else{
			$select = $table -> select() -> where('contest_id =?', $contest_id) -> where("approve_status = 'denied'") -> order('start_date');
		}




		$paginator = Zend_Paginator::factory($select);

		$paginator -> setItemCountPerPage($request -> getParam('itemCountPerPage', 12));
		$paginator -> setCurrentPageNumber($request -> getParam('page', 1));
		$view = Zend_Registry::get('Zend_View');
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_entries.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'contestId' => $contest_id,
				'tab' =>$tab,
		));
	}

	public function ajaxWinEntryByOwnerAction(){
		//Get params
		$entry_id = $this->_getParam('entry_id');
		$status = $this->_getParam('status');


		$entries = Engine_Api::_()->getItem('yncontest_entries', $entry_id);

			
		//Set service
		if($entries){
			
			$contest = Engine_Api::_()->getItem('contest', $entries->contest_id);
			
			
			if($status) {
				if($contest->contest_status == 'close'){
					
					$entries->entry_status = 'win';
					$user = Engine_Api::_() -> user() -> getUser($entries -> user_id);
					//send notify to members
					$entries->sendNotMailOwner($user, $entries, 'entry_win_vote', null, array('vote_desc' => $contest->reason_desc))	;
						
					//send notify to follower
					$follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
					$followUsers = $follow_table->getUserFolowContest($contest->contest_id);
					foreach($followUsers as $followUser){
						//send notification
						if($user->user_id != $followUser -> user_id){
							$f_user = Engine_Api::_() -> user() -> getUser($followUser -> user_id);
							$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
							$notifyApi -> addNotification($f_user,$entries, $contest, 'entry_win_vote_f');
						}
					}	
				}
				$entries->waiting_win = 1;
			
			}
			else {
				if($contest->contest_status == 'close'){
					$entries->entry_status = 'published';
					$user = Engine_Api::_() -> user() -> getUser($entries -> user_id);
					//send notify to members
					$entries->sendNotMailOwner($user, $entries, 'entry_no_win_vote', null, array('vote_desc' => $contest->reason_desc))	;
					
					//send notify to follower
					$follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
					$followUsers = $follow_table->getUserFolowContest($contest->contest_id);
					foreach($followUsers as $followUser){
						//send notification
						if($user->user_id != $followUser -> user_id){
							$f_user = Engine_Api::_() -> user() -> getUser($followUser -> user_id);
							$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
							$notifyApi -> addNotification($f_user,$entries, $contest, 'entry_no_win_vote_f');
						}
					}	
				}
				$entries->waiting_win = 0;
			}
				
			
				

			$entries->save();
		}
	}

	public function ajaxWinEntriesAction($params = array())
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> recentType = $recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}
		$recentCol = $recentType . '_date';
		$contest_id = $request -> getParam('contestId');

		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');

		$tab = $request -> getParam('tab');

		$select = $table -> select() -> where('contest_id = ?', $contest_id) -> where("entry_status = 'published' or entry_status = 'win'") -> where("approve_status = 'approved'") -> order('start_date');
		
		//echo $select;die;
		
		$paginator = Zend_Paginator::factory($select);

		$paginator -> setItemCountPerPage($request -> getParam('itemCountPerPage', 12));
		$paginator -> setCurrentPageNumber($request -> getParam('page', 1));
		$view = Zend_Registry::get('Zend_View');
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_win_entries.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'contestId' => $contest_id,
				'tab' =>$tab,
		));
	}

	public function ajaxParticipantsAction($params) {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$id = $this->_getParam('id');
		$contestId = $this->_getParam('contest_id');

		$table = Engine_Api::_()->getItemTable('user');
		$select = $table->select()->where('user_id= ?', $id);
		$member = $table->fetchRow($select);

		$contest = Engine_Api::_()->getItem('contest', $contestId);

		$view = Zend_Registry::get('Zend_View');
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_participant.tpl'),
				array('member' => $member, 'contest' => $contest));
	}

	public function ajaxEntriesPhotoAction($params = array())
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//List params for getting new blogs
		$recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}
		$recentCol = $recentType . '_date';
		
		$albumPlugin = Engine_Api::_()->yncontest()->getPluginsAlbum();
		$table = Engine_Api::_() -> getDbtable('photos', $albumPlugin);
		$atable = Engine_Api::_() -> getDbtable('albums', $albumPlugin);
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> where("$aName.owner_id =?", $viewer -> getIdentity());
		if (isset($params['item']) && $params['item'] != null)
		{
			$select -> where("$Name.photo_id =?", $params['item']);
		}
		if (!isset($_SESSION['advalbum']))
		{
			$_SESSION['advalbum'] = null;
		}
		$paginator = Zend_Paginator::factory($select);

		$paginator -> setItemCountPerPage($request -> getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($request -> getParam('page', 1));
		$view = Zend_Registry::get('Zend_View');
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_entries_photo.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'id' => $_SESSION['advalbum']
		));
	}


	public function giveAwardAction()
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if (!$entries)
			return $this -> _helper -> requireAuth -> forward();
		$contest = Engine_Api::_() -> getItem('contest', $entries -> contest_id);

		if (!$this -> _helper -> requireAuth() -> setAuthParams($contest, $viewer, 'or_edit_entries') -> isValid())
		{
			return;
		}

		$awards = Engine_Api::_() -> getDbtable('awards', 'yncontest') -> getAwardByContest($contest -> contest_id);
		$arrAward = array();
		foreach ($awards as $award)
		{
			if ($award -> quantities - $award -> numbers  > 0)
				$arrAward[$award -> award_id] = $award -> award_name;
		}

		$this -> view -> form = $form = new Yncontest_Form_Entries_Giveaward( array('award' => $arrAward, ));

		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$post = $this -> getRequest() -> getPost();

		if (!$form -> isValid($post))
			return;

		// Process
		$table = new Yncontest_Model_DbTable_Entries;
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$values = array_merge($form -> getValues(), array(
					'user_id' => $viewer -> getIdentity(),
					'contest_id' => $contest_id
			));
				
			$award = Engine_Api::_() -> getDbTable('awards', 'yncontest') -> find($values['award_type']) -> current();
				
			$entries -> award_id = $values['award_type'];
			if($contest->user_id == $viewer->getIdentity()){
				$award -> numbers ++;
				$entries -> entry_status = 'win';
				$entries->give_award_status = 1;
			}
			else{
				$entries->give_award_status = 2;
			}
				

			$entries -> save();

				
			$contest->sendNotMailFollwer($contest, 'entry_win');

			$award -> save();

			$db -> commit();

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have just successfully given award')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));

	}

	public function approveGiveAwardAction(){

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if (!$entries)
			return $this -> _helper -> requireAuth -> forward();
		$contest = Engine_Api::_() -> getItem('contest', $entries -> contest_id);

		$awards = Engine_Api::_() -> getDbtable('awards', 'yncontest') -> getAwardByContest($contest -> contest_id);


		// Process
		$table = new Yncontest_Model_DbTable_Entries;
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
				
			$award = Engine_Api::_() -> getDbTable('awards', 'yncontest') -> find($entries->award_id) -> current();
				
			$award -> numbers ++;
			$entries -> entry_status = 'win';
			$entries->give_award_status = 1;

			$entries -> save();
			$award -> save();

			$db -> commit();

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You have just successfully approved this award')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));
	}

	public function editAction()
	{

		//$this->_helper->layout->disableLayout();
		//$this->_helper->viewRenderer->setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if (!$entries)
			return $this -> _helper -> requireAuth -> forward();
		$contest = Engine_Api::_() -> getItem('contest', $entries -> contest_id);


		if (!$this -> _helper -> requireAuth() -> setAuthParams('contest', $viewer, 'editentries') -> isValid() && !$this -> _helper -> requireAuth() -> setAuthParams($contest, $viewer, 'or_edit_entries') -> isValid())
		{
			return;
		}


		$this -> view -> form = $form = new Yncontest_Form_Entries_Edit();
		$form->setAttrib("class", "global_form_popup");
		$form -> populate($entries -> toArray());

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$post = $this -> getRequest() -> getPost();

		if (!$form -> isValid($post))
			return;

		// Process
		// Process
		$table = new Yncontest_Model_DbTable_Entries;
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create store
			$values = array_merge($form -> getValues());

			$entries -> setFromArray($values);
			$entries -> save();

			$db -> commit();

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thanks for you editing.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));

	}

	public function voteAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if (!$entries)
			return $this -> _helper -> requireAuth -> forward();

		$contest = Engine_Api::_() -> getItem('contest', $entries -> contest_id);

		if (!$this -> _helper -> requireAuth() -> setAuthParams('contest', $viewer, 'voteentries') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('entries', 'yncontest') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if ($entries -> checkVote())
			{
				$favourite_table = Engine_Api::_() -> getItemTable('yncontest_votes');
				$favourite = $favourite_table -> createRow();
				$favourite -> entry_id = $entries -> entry_id;
				$favourite -> user_id = $viewer -> getIdentity();
				$favourite -> save();
				$entries -> vote_count = $entries -> vote_count + 1;
				$entries -> save();
				
				
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
					
				
				$notifyApi -> addNotification($entries->getOwner(),$viewer, $entries, 'new_vote');
				
				$db -> commit();
			}

		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Thank you for your voting')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));
	}

	public function viewAction()
	{
		$view = Zend_Registry::get('Zend_View');		
		$headLink  = new Zend_View_Helper_HeadLink();
		$headLink  -> appendStylesheet($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/styles/mp3_music.css');
			
		if(Zend_Registry::isRegistered('active_menu')){
			$active_menu =  Zend_Registry::get('active_menu');
		}else{
			$active_menu = null;
		}
		
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'view')->isValid()) return;

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('yncontest_main',array(), $active_menu);

		
		$this->view->item = $entry = Engine_Api::_() -> getItem('yncontest_entries', $this -> _getParam('id', null));
		
				
		//hide campaign with inactivated without user
		if(!$entry->getParent()->isOwner($viewer) && !$entry->isOwner ( $viewer ) && empty($entry->activated))
			 return $this->_helper->requireAuth->forward ();
		
		$entriesTable = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		
		if ( !isset($_SESSION['entry_'.$entry->getIdentity()]))
		{			
			$_SESSION['entry_'.$entry->getIdentity()] = true;
			$entriesTable -> update(array('view_count' => new Zend_Db_Expr('view_count + 1'), ), array('entry_id = ?' => $entry -> getIdentity(), ));
		}

		$this->view->contest = $contest =  Engine_Api::_()->getItem('contest', $entry->contest_id);
		$this->view->nextEntry = $entry->getNextEntry($entry, $contest, $viewer);
    	$this->view->previousEntry = $entry->getPreviousEntry($entry, $contest, $viewer);
		//only contest onwner & entry owner will entry detail
		$user = Engine_Api::_() -> user() -> getUser($contest -> user_id);		
		
		if(($entry->approve_status == 'denied' || $entry->approve_status == 'pending'  )  && !$entry->IsOwner($viewer) && !$contest->isOwner($viewer) ) {
			return $this->_forward('requireauth', 'error', 'core');
		}		
		
		$this->view->member = Engine_Api::_() -> user() -> getUser($entry -> user_id);

		$this->_helper->content
		//->setNoRender()
		->setEnabled()
		;
	}

	public function ajaxEntriesBlogAction($params = array())
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//List params for getting new blogs
		$recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}
		$recentCol = $recentType . '_date';
		
		//$blogPlugin = $this->getPluginsBlog();		
		$table = Engine_Api::_() -> getItemTable('blog');;
		$select = $table -> select() -> where('search = ?', 1) -> where('owner_id =?', $viewer -> getIdentity()) -> where('draft = ?', 0) -> order('creation_date');
		if (isset($params['item']) && $params['item'] != null)
		{
			$select -> where('blog_id =?', $params['item']);
		}

		if (isset($_SESSION['advalbum']))
		{
			$_SESSION['advalbum'] = null;
		}

		$paginator = Zend_Paginator::factory($select);

		$paginator -> setItemCountPerPage($request -> getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($request -> getParam('page', 1));
		$view = Zend_Registry::get('Zend_View');
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_entries_blog.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'id' => $_SESSION['ynblog']
		));
	}


	public function ajaxEntriesVideoAction($params = array())
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$recentType = $request -> getParam('recentType', 'creation');
		if (!in_array($recentType, array(
				'creation',
				'modified'
		)))
		{
			$recentType = 'creation';
		}

		$recentCol = $recentType . '_date';

		$videoPlugin = Engine_Api::_()->yncontest()->getPluginsVideo();
		$table = Engine_Api::_() -> getItemTable($videoPlugin);
		$select = $table -> select() -> where('search = ?', 1) -> where('owner_id =?', $viewer -> getIdentity()) -> where('status = ?', 1);

		if (isset($params['item']) && $params['item'] != null)
		{
			$select -> where('video_id =?', $params['item']);
		}
		if ($recentType == 'creation')
		{
			// using primary should be much faster, so use that for creation
			$select -> order('video_id DESC');
		}
		else
		{
			$select -> order($recentCol . ' DESC');
		}

		if (isset($_SESSION['advalbum']))
		{
			$_SESSION['advalbum'] = null;
		}

		$paginator = Zend_Paginator::factory($select);

		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($request -> getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($request -> getParam('page', 1));
		$view = Zend_Registry::get('Zend_View');
		$this -> _childCount = $paginator -> getTotalItemCount();
		//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
		// =>$recentCol ));
		echo $view -> partial(Yncontest_Api_Core::partialViewFullPath('_list_entries_video.tpl'), array(
				'paginator' => $paginator,
				'recentType' => $recentType,
				'recentCol' => $recentCol,
				'id' => $_SESSION['ynvideo']
		));

	}

	public function getValueAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$optionType = $request -> getParam('optionType');
		switch ($optionType)
		{
			case 'ynblog' :
				$_SESSION['ynblog'] = $request -> getParam('id');
				break;
			case 'ynvideo' :
				$_SESSION['ynvideo'] = $request -> getParam('id');
				break;
			case 'mp3music' :
				$_SESSION['mp3music'] = $request -> getParam('id');
				$_SESSION['music_type'] = $request -> getParam('music_type');
				break;
			case 'advalbum' :
				$_SESSION['advalbum'] = $request -> getParam('id');				
				break;
			case 'ynmusic' :
				$_SESSION['ynmusic'] = $request -> getParam('id');
				break;
			case 'ynultimatevideo' :
				$_SESSION['ynultimatevideo'] = $request -> getParam('id');
				break;
		}

	}

	public function suggestEntryAction()
	{

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$text = $request->getParam('value');
		$contest_id = $request -> getParam('contest_id');

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			$data = null;
		}
		else
		{
			$data = array();


			$table = Engine_Api::_() -> getItemTable('yncontest_entry');
			$select = $table -> select() ->where("contest_id =?",$contest_id)->  where("entry_status = 'published' ") -> order('start_date');
				
			$select->where($table -> info('name').'.entry_name LIKE ?', '%' . $text . '%');
			
			$ids = array();
			foreach ($table->fetchAll($select) as $item)
			{

				$data[] = array(
						'type' => 'user',
						'id' => $item -> getIdentity(),
						'guid' => $item -> getGuid(),
						'label' => $item -> getTitle(),
						//'photo' => $video->view->itemPhoto($user, 'thumb.icon'),
						'url' => $item -> getHref(),
				);
				$ids[] = $item -> getIdentity();

			}
		}
		//echo $select;
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}

	public function suggestAction()
	{

		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$text = $request->getParam('value');
		
		$contestType = $request -> getParam('contest');

		$viewer = Engine_Api::_() -> user() -> getViewer();
		

		if (!$viewer -> getIdentity())
		{
			$data = null;
		}
		else
		{
			$user_id = $viewer -> getIdentity();	
			$data = array();

			if ($contestType == 'ynblog')
			{
				//$blogPlugin = $this->getPluginsBlog();		
				$table = Engine_Api::_() -> getItemTable('blog');;
				$select = $table -> select() -> where('search = ?', 1) -> where('owner_id =?', $viewer -> getIdentity()) -> where('draft = ?', 0) -> order('creation_date');
				$select->where($table -> info('name').'.title LIKE ?', '%' . $text . '%');
			}
			elseif ($contestType == 'ynvideo')
			{
				//$videoPlugin = Engine_Api::_()->yncontest()->getPluginsVideo();
				$table = Engine_Api::_() -> getItemTable('video');
				$select = $table -> select() -> where('search = ?', 1) -> where('owner_id =?', $viewer -> getIdentity()) -> where('status = ?', 1);
				$select -> order('video_id DESC');
				$select->where($table -> info('name').'.title LIKE ?', '%' . $text . '%');

			}
			elseif ($contestType == 'advalbum')
			{
				//null
			}
			elseif ($contestType == 'ynmusic')
			{
				$table = Engine_Api::_() -> getItemTable('ynmusic_song');
				$select = $table -> select() -> where('search = ?', 1) -> where('user_id =?', $viewer -> getIdentity());
				$select -> order('song_id DESC');
				$select->where($table -> info('name').'.title LIKE ?', '%' . $text . '%');
			}
			elseif ($contestType == 'ynultimatevideo')
			{
				$table = Engine_Api::_() -> getItemTable('ynultimatevideo_video');
				$select = $table -> select()
					-> where('search = ?', 1)
					-> where('status = ?', 1)
					-> where('owner_id =?', $viewer -> getIdentity());
				$select -> order('video_id DESC');
				$select->where($table -> info('name').'.title LIKE ?', '%' . $text . '%');
			}
			else
			{
				$table = Engine_Db_Table::getDefaultAdapter();	
				//check module music
				$select = "SELECT * FROM engine4_core_modules WHERE name = 'music'";
				$music = $table->fetchRow($select);
				
				$select = "SELECT * FROM engine4_core_modules WHERE name = 'mp3music'";
				$mp3music = $table->fetchRow($select);
				
				if(!empty($mp3music['enabled']) && !empty($music['enabled']))
				{
					$select = "
						SELECT 
							`engine4_mp3music_album_songs`.song_id,
							'mp3music_album_song' as resource_type
						FROM engine4_mp3music_album_songs
							LEFT JOIN engine4_mp3music_albums ON `engine4_mp3music_album_songs`.album_id = `engine4_mp3music_albums`.album_id
						 	WHERE `engine4_mp3music_album_songs`.title LIKE '%$text%' AND `engine4_mp3music_albums`.user_id = $user_id 
						UNION
						SELECT 
							`engine4_music_playlist_songs`.song_id,
							'music_playlist_song' as resource_type
						FROM engine4_music_playlist_songs
						 	LEFT JOIN engine4_music_playlists ON `engine4_music_playlist_songs`.playlist_id = `engine4_music_playlists`.playlist_id
 							WHERE `engine4_music_playlist_songs`.title LIKE '%$text%' AND `engine4_music_playlists`.owner_id = $user_id 
						ORDER BY song_id DESC	
					";
				}
				elseif(!empty($mp3music['enabled']))
				{
					$select = "
						SELECT 
							`engine4_mp3music_album_songs`.song_id,
							'mp3music_album_song' as resource_type		
						 FROM engine4_mp3music_album_songs	
						 	LEFT JOIN engine4_mp3music_albums ON `engine4_mp3music_album_songs`.album_id = `engine4_mp3music_albums`.album_id
						 	WHERE `engine4_mp3music_album_songs`.title LIKE '%$text%' AND `engine4_mp3music_albums`.user_id = $user_id		
						ORDER BY song_id DESC	
					"; 
				}
				else{
					$select = "				
						SELECT 
							`engine4_music_playlist_songs`.song_id,
							'music_playlist_song' as resource_type					
						 FROM engine4_music_playlist_songs
						 	LEFT JOIN engine4_music_playlists ON `engine4_music_playlist_songs`.playlist_id = `engine4_music_playlists`.playlist_id
 							WHERE `engine4_music_playlist_songs`.title LIKE '%$text%' AND `engine4_music_playlists`.owner_id = $user_id 
						ORDER BY song_id DESC
					";
				}	
			}	
			//echo $select;die;					
			if ($contestType == 'ynblog')
			{
				foreach ($table->fetchAll($select) as $item)
				{
	
					$data[] = array(
							'type' => 'user',
							'id' => $item -> getIdentity(),
							'guid' => $item -> getGuid(),
							'label' => $item -> getTitle(),	
							'photo' => $this -> view -> itemPhoto($item->getOwner(), 'thumb.icon'),
							'url' => $item -> getHref(),
					);				
	
				}
			}
			elseif ($contestType == 'mp3music')
			{
				foreach ($table->fetchAll($select) as $item)
				{				
					$temp = Engine_Api::_()->getItemTable($item['resource_type'])->find($item['song_id'])->current();
					
		    		if($item['resource_type'] == 'music_playlist_song')
		    		{	            			
		    			$album = Engine_Api::_()->getItemTable('music_playlist')->find($temp->playlist_id)->current();	            			
		    		}
		    		else{
		    			$album = Engine_Api::_()->getItemTable('mp3music_album')->find($temp->album_id)->current();
		    		}
						
					    
					$data[] = array(
							'type' => 'user',
							'id' => $item['resource_type'].$temp -> getIdentity(),
							'guid' => $temp -> getGuid(),
							'label' => $temp -> getTitle(),	
							'photo' => $this -> view -> itemPhoto($album, 'thumb.icon'),
							'url' => $temp -> getHref(),
					);				
	
				}
			}
			elseif ($contestType == 'ynmusic')
			{
				foreach ($table->fetchAll($select) as $item)
				{
					$data[] = array(
						'type' => 'user',
						'id' => $item -> getIdentity(),
						'guid' => $item -> getGuid(),
						'label' => $item -> getTitle(),
						'photo' => $this -> view -> itemPhoto($item, 'thumb.icon'),
						'url' => $item -> getHref(),
					);
				}
			}
			elseif ($contestType == 'ynmusic')
			{
				foreach ($table->fetchAll($select) as $item)
				{
					$data[] = array(
						'type' => 'user',
						'id' => $item -> getIdentity(),
						'guid' => $item -> getGuid(),
						'label' => $item -> getTitle(),
						'photo' => $this -> view -> itemPhoto($item, 'thumb.icon'),
						'url' => $item -> getHref(),
					);
				}
			}
			else{
				foreach ($table->fetchAll($select) as $item)
				{
	
					$data[] = array(
							'type' => 'user',
							'id' => $item -> getIdentity(),
							'guid' => $item -> getGuid(),
							'label' => $item -> getTitle(),	
							'photo' => $this -> view -> itemPhoto($item, 'thumb.icon'),
							'url' => $item -> getHref(),
					);				
	
				}
			}
		}
	
		//echo $select;
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}

	public function getEntriesCompareAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$entry_id = $request -> getParam('entry_id');

		$entry = Engine_Api::_() -> yncontest() -> getEntriesById($entry_id);

		if (is_object($entry))
		{
			$this -> view -> entry_name = $this -> view -> htmlLink($entry -> getHref(), $entry -> entry_name);
			$this -> view -> start_date =  $this -> view ->locale()->toDateTime( $entry -> start_date, array('size' => 'short'));//date('m/d/Y', strtotime($entry -> start_date));
			$this -> view -> vote = $entry -> vote_count;
			$this -> view -> like = $entry -> like_count;
			$this -> view -> owner = $entry -> getOwner() -> toString();
			$this -> view -> entry_id = $entry -> getIdentity();
			$this -> view -> url = $entry -> getHref();
			$this -> view -> photo = $entry -> getPhotoUrl('');
			$this -> view -> item_id = $entry -> item_id;

			//$photo = Engine_Api::_() -> yncontest() -> getEntryThumnail($entry -> entry_type, $entry -> item_id);

			$this -> view -> itemHTML = $this -> view -> htmlLink($entry -> getHref(),$this -> view -> itemPhoto($photo, ''),array('class'=>'thumbs_photo'));
		}
		else
		{
			$this -> view -> entry_name = "";
			$this -> view -> start_date = "";
			$this -> view -> vote = "";
			$this -> view -> like = "";
			$this -> view -> user_id = "";
			$this -> view -> photo = "";
		}

	}

	public function entriesCompareAction()
	{

		$entry_id = $this -> _getParam('entry_id');

		$this -> view -> entry_id1 = $entry_id[0];
		$this -> view -> entry_id2 = $entry_id[1];

		$this -> view -> entry1 = $entry1 = Engine_Api::_() -> yncontest() -> getEntriesById($this -> view -> entry_id1);
		$this -> view -> entry2 = $entry2 = Engine_Api::_() -> yncontest() -> getEntriesById($this -> view -> entry_id2);

		if (isset($_POST['submit']))
		{

			$entry = Engine_Api::_() -> getItem('yncontest_entries', $entry_id[0]);
			$entry -> vote_count += 1;
			$entry -> save();

			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh'=> 10,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Vote Successfully.'))
			));
		}

		if (isset($_POST['submit2']))
		{
			$entry = Engine_Api::_() -> getItem('yncontest_entries', $entry_id[1]);
			$entry -> vote_count += 1;
			$entry -> save();

			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh'=> 10,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Vote Successfully.'))
			));
		}

		$this -> view -> form1 = $form1 = new Yncontest_Form_Vote();
		$this -> view -> form2 = $form2 = new Yncontest_Form_Vote2();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$contest1 = Engine_Api::_() -> getDbTable('contests', 'yncontest') -> find($entry1 -> contest_id) -> current();
		$member = Engine_Api::_() -> getItemTable('yncontest_members') -> getMemberContest2(array(
				'contestId' => $entry1 -> contest_id,
				'user_id' => $entry1 -> user_id
		));

		$flag = Engine_Api::_() -> yncontest() -> checkRule(array(
				'contestId' => $entry1 -> contest_id,
				'key' => 'voteentries',
		));
		$organizerList = $contest1 -> getOrganizerList();
		$contest2 = Engine_Api::_() -> getDbTable('contests', 'yncontest') -> find($entry2 -> contest_id) -> current();

		if ($flag && $member -> member_status == 'approved' && !$entry1 -> isOwner($viewer) && $entry1 -> checkVote() && !$organizerList -> has($viewer) && !$contest1 -> authorization() -> isAllowed($viewer, 'voteentries'))
		{
			$form1 -> removeElement('submit');
		}
		if (!$contest2 -> authorization() -> isAllowed($viewer, 'voteentries'))
		{
			$form2 -> removeElement('submit2');
		}

		//$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function entriesVotingAction()
	{

		$this -> _helper -> content -> setNoRender() -> setEnabled();
		/*
		 $this->_forward('success', 'utility', 'core', array(
		 		'smoothboxClose' => 10,
		 		'parentRefresh'=> 10,
		 		'messages' => array(Zend_Registry::get('Zend_Translate')->_('Vote Successfully.'))
		 ));
		*/
		return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You are already a member of this contest.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));
	}
		
}
