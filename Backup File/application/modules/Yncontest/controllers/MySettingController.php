<?php

class Yncontest_MySettingController extends Core_Controller_Action_Standard
{
	private function _buildPermissionForContest($contest) {
		// Authorization set up
		$auth = Engine_Api::_() -> authorization() -> context;
			
		$setting = Engine_Api::_()->getDbTable('settings', 'yncontest')
		->getSettingByContest($contest->getIdentity());

		$organizerList = $contest->getOrganizerList();

		$values = array();
		$values['auth_view'] = 'everyone';
		$values['auth_createentries'] = 'member';
		$values['auth_voteentries'] = 'member';
		$values['auth_editcontest'] = 'owner';
		$values['auth_deletecontest'] = 'owner';
		$values['comment'] = 'owner';

		$roles = array('owner', 'yncontest_list', 'member', 'registered', 'everyone');

		$viewMax = array_search($values['auth_view'], $roles);
		$createentries = array_search($values['auth_createentries'], $roles);
		$voteentries = array_search($values['auth_voteentries'], $roles);
		$editMax = array_search($values['auth_editcontest'], $roles);
		$deleteMax = array_search($values['auth_deletecontest'], $roles);

		//  Allow members to comment on my contest
		if ($setting->comment == 1) {
			$values['comment'] = 'member';
		}

		//  Allow members to comment on entries.
		$entries = Engine_Api::_()->getDbTable('entries', 'yncontest')->getEntriesContest2(array('contestID' => $contest->getIdentity()));
		if (!empty($entries)) {
			foreach ($entries as $entry) {
				$auth->setAllowed($entry, 'parent_member', 'comment', ($setting->comment_entries == 1));
				$auth->setAllowed($entry, $organizerList, 'comment', ($setting->comment_entries == 1));
			}
		}
		$commentContestMax = array_search($values['comment'], $roles);

		foreach( $roles as $i => $role ) {
			if( $role === 'yncontest_list' ) {
				$role = $organizerList;
			}
	
			$auth->setAllowed($contest, $role, 'view', ($i <= $viewMax));
			$auth->setAllowed($contest, $role, 'editcontests', ($i <= $editMax));
			$auth->setAllowed($contest, $role, 'deletecontests', ($i <= $deleteMax));
			$auth->setAllowed($contest, $role, 'createentries', ($i <= $createentries));
			$auth->setAllowed($contest, $role, 'voteentries', ($i <= $voteentries));
			$auth->setAllowed($contest, $role, 'comment', ($i <= $commentContestMax));

		}
	
	}

	public function init(){

		//require user
		if( !$this->_helper->requireUser()->isValid() ) return;

		//requrie contest_id
		$contest_id = $this->_getParam('contest', null);
		if($contest_id == null)
			return;

		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);	
		if($contest == null || !$contest->checkIsOwner())
			return;

	
		Zend_Registry::set('active_menu', 'yncontest_main_create_contest');

		if($contest) {
			Engine_Api::_() -> core() -> setSubject($contest);
		}
	}

	public function indexAction()
	{
		// Render
		//$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	// create rule information
	public function createContestSettingAction(){
		//check authoziration
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'createcontests')->isValid()) return;
		
		
		
		if( !$this->_helper->requireSubject()->isValid() ) return;

		//get $levelOptions
		$levelOptions = array();
	    foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level ) {
	      $levelOptions[$level->level_id] = $level->getTitle();
	    }
		$this->view->warning = $this->view->translate("The number of entries which a member can post depends on both your setting & admin's setting for each member level. The lower number will be more prioritive.Currently, Admin is setting:").'<br><ul>' ;
		foreach ($levelOptions as $key => $title) {
				
			$db     = Engine_Db_Table::getDefaultAdapter();
			$select = "SELECT `engine4_authorization_permissions`.value FROM `engine4_authorization_permissions` WHERE `level_id` = $key AND `name` = 'max_entries' AND `type` = 'contest' ";
			
			$item = $db->query( $select)->fetch();

			
			if(isset($item['value']) && !empty($item['value']))
			{
				$this->view->warning .= '<li>'.$this->view->translate(array("+ %s can post only %s entry", "+ %s can post only %s entries",$title, $item['value']),$title, $item['value']).'</li>';
			}
			elseif(isset($item['value']) && empty($item['value'])){
				$this->view->warning .= '<li>'.$this->view->translate("+ %s can post up to unlimited",$title).'</li>';
			}
		}
		$this->view->warning .= '</ul>';
	
		
		$contest = Engine_Api::_()->core() ->getSubject();
		$this->view->contest_id = $contest->contest_id;
		
		$this->view->form = $form = new Yncontest_Form_Setting_Create();		
		
	
		$settings = Engine_Api::_() -> getDbTable('settings', 'yncontest') -> getSettingByContest($contest->contest_id);
		if($settings){
			//edit contest settings
			$array = $settings->toArray();			
			$form->populate($array);
		}
		
		// If not post or form not valid, return
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}
		
		$post = $this -> getRequest() -> getPost();
		
		if(!$form -> isValid($post))
			return ;
		
		// Process
		$table = new Yncontest_Model_DbTable_Settings;
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		
		try {
			//process table
			$values = array_merge($form -> getValues(),array('contest_id' =>  $contest->getIdentity()));
			if(!$settings){
				$settings = $table -> createRow();
				
				$values['user_id'] = $viewer -> getIdentity();
			}
			$settings -> setFromArray($values);
			$settings->save();
			$this->_buildPermissionForContest($contest);
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		if($contest->isOwner($viewer) && $contest->contest_status == 'draft'  &&  $contest->approve_status == 'new')
			return $this->_helper->redirector->gotoRoute(array('action' => 'publish', 'contest' => $contest->contest_id), 'yncontest_mycontest', true);
		return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'contestId' => $contest->contest_id), 'yncontest_mycontest', true);
	}	
	
}
