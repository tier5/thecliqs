<?php
class Yncontest_Widget_ListingOrganizerController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$this -> view -> form = $form = new Yncontest_Form_SearchMember;
		$params['member_type'] = 2;
		$params['contest_id'] = $request->getParam('contest_id');		
		$params['user_id'] = $request->getParam('user_id');
		$params['user_name'] = $request->getParam('user_name');
		$params['status'] = $request->getParam('status');
		$params['gender'] = $request->getParam('gender');
		$params['from'] = $request->getParam('from');
		$params['to'] = $request->getParam('to');
		
		if($params['from'] != "" && $params['to'] !="" && $params['from'] > $params['to']){
			$tmp = $params['from'];
			$params['from'] = $params['to'];
			$params['to'] = $tmp;
		}
		
		$form->populate($params);
		if(isset($_POST['btnapprove'])){
			foreach($_POST['delete'] AS $key => $value){
				$member = Engine_Api::_() -> getItem('yncontest_members', $value);			
				$contest = Engine_Api::_() ->getItemTable('contest')->find($request->getParam('contest_id'))->current();		
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
				}				
			}
		}			
				
		// Process form
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'yncontest')->getMemberPaginator($params);
		$items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
	}

}
