<?php

class Yncontest_MyMembersController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $this -> _getParam('contestId', null));

		if (count($contest) > 0)
		{
			Engine_Api::_() -> core() -> setSubject($contest);
		}
		
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		
	}
	public function suggestAction()
	{
	
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$full_name = $request->getParam('value');
		$contest_id = $request -> getParam('contestId');
		$type =  $request -> getParam('type');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if (!$viewer -> getIdentity())
		{
			$data = null;
		}
		else
		{
			$data = array();
	
			
			$t_user = Engine_Api::_() -> getItemTable('user');
			$userName = $t_user -> info('name');
			
			$t_membership =  Engine_Api::_() -> getDbTable('membership', 'yncontest');
			$userMembership = $t_membership -> info('name');

			$select = $t_user -> select() -> from($userName, "$userName.*") -> setIntegrityCheck(false);	
			
			$select -> joinLeft("$userMembership", "$userMembership.user_id = $userName.user_id", "");
			
			$select -> where("$userMembership.resource_id =?", $contest_id);
			$select -> where("$userName.displayname Like ?", "%$full_name%");
			
			
			//$select = $table -> select() -> where('contest_id =?', $contest_id) -> where('member_type = ?', $type) -> where('member_status = ?', 'approved') -> order('approve_date');
			
			//$select->where($table -> info('name').'.full_name LIKE ?', '%' . $full_name . '%');
			$ids = array();
			
			
			foreach ($t_user->fetchAll($select) as $item)
			{
	
				$data[] = array(
						'type' => 'user',
						'id' => $item -> getIdentity(),
						'guid' => $item -> getGuid(),
						'label' => $item -> displayname,
// 						'photo' => $video->view->itemPhoto($user, 'thumb.icon'),
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

	public function indexAction()
	{
		// Render
		//$this -> _helper -> content -> setNoRender() -> setEnabled();
		 if (!$this -> _helper -> requireUser() -> isValid())
    		return;
	}

	public function editAction()
	{
		$announce = Engine_Api::_() -> getItem('yncontest_members', $this -> _getParam("id"));
		if (!$announce)
		{
			return $this -> _forward('requireauth', 'error', 'core');

		}

		$this -> view -> form = $form = new Yncontest_Form_Member_Edit();

		$form -> populate($announce -> toArray());

		// Save values
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$params = $form -> getValues();
			$announce -> setFromArray($params);
			$announce -> save();
			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh' => 10,
					'messages' => array('')
			));
		}

	}

	public function memberAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
    		return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function participateAction()
	{
		Zend_Registry::set('active_mini_menu','member');		
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function organizerAction()
	{
		Zend_Registry::set('active_mini_menu','member');
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function ruleAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function statictisAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		Zend_Registry::set('active_mini_menu','statictis');
	    $this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function banMemberAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$form = $this -> view -> form = new Yncontest_Form_Member_Ban();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$member = Engine_Api::_() -> getItem('yncontest_members', $this -> _getParam("id"));
		if (!$member)
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Save values
		if ($this -> getRequest() -> isPost())
		{
			$member -> member_status = 'banned';
			$member -> save();
			//close all entries
			$entries = Engine_Api::_() -> getItemTable('yncontest_entries') -> getEntriesContest(array(
					'contestID' => $member -> contest_id,
					'user_id' => $member -> user_id
			));
			foreach ($entries as $entry)
			{
				$entry -> entry_status = 'close';
				$entry -> save();
			}
			try
			{
			//$contest = Engine_Api::_() -> getItem('yncontest_contest', $this->_getParam('contestId', null));
		

				$params["contest_link"] = "";


				Engine_Api::_() -> getApi('mail', 'yncontest') -> send($member -> email, 'ban_participant', $params);

			}
			catch( Exception $e )
			{

			}

			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh' => 10,
					'messages' => array('Memmber is banned.')
			));
		}

		//Output
		$this -> renderScript('member/ban.tpl');

	}

	public function approveMemberAction()
	{
		
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		//if (!$this -> _helper -> requireSubject() -> isValid())
			//return;

		//$contest = Engine_Api::_() -> core() -> getSubject();
		$conetst_id = $this->_getParam('contest_id');
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $conetst_id);
		//$contest = Engine_Api::_() ->getItemTable('contest')->find($request->getParam('contest_id'))->current();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$member = Engine_Api::_() -> getItem('yncontest_members', $this -> _getParam("id"));

		if (!$member)
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Save values

		$member -> member_status = 'approved';
		$member -> approve_date = date('Y-m-d H:i:s');
		$member -> save();	
		$user = Engine_Api::_() -> user() -> getUser($member -> user_id);
		if(!$contest -> membership() -> isMember($user, true)){
			$contest -> membership() -> addMember($user) -> setUserApproved($user) -> setResourceApproved($user);
			if ($member -> member_type == 2)
			{
				$organizerList = $contest -> getOrganizerList();
				//add ownwer as organizer
				if (!$organizerList -> has($user))
					$organizerList -> add($user);
			}
			
			//$params["contest_link"] = "";
			//Engine_Api::_() -> getApi('mail', 'yncontest') -> send($member -> email, 'ban_participant', $params);
		}		

		
		
		$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('Memmber is approved.')
		));

	}

	public function denyMemberAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$form = $this -> view -> form = new Yncontest_Form_Member_Deny();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$member = Engine_Api::_() -> getItem('yncontest_members', $this -> _getParam("id"));
		if (!$member)
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Save values
		if ($this -> getRequest() -> isPost())
		{
			$member->member_status = 'denied';
			$member->save();
			//$member -> delete();
			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh' => 10,
					'messages' => array('Memmber is denied.')
			));
		}

		//Output
		$this -> renderScript('member/deny.tpl');
	}

	public function deleteMemberAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$form = $this -> view -> form = new Yncontest_Form_Member_Delete();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$member = Engine_Api::_() -> getItem('yncontest_members', $this -> _getParam("id"));
		if (!$member)
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Save values
		if ($this -> getRequest() -> isPost())
		{
			$member -> delete();
			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh' => 10,
					'messages' => array('Memmber is denied.')
			));
		}

		//Output
		$this -> renderScript('member/delete.tpl');
	}

}
