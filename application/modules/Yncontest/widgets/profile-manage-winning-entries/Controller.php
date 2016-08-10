<?php
class Yncontest_Widget_ProfileManageWinningEntriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{	
		//print_r($_SESSION);die;			
		$this->getElement()->removeDecorator('Title');
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		
		$contestId = $request->getParam('contestId');
		
		if(empty($contestId))
		{			
			return $this->setNoRender();
		}
		$this->view->contest = $contest = Engine_Api::_()->getItem('contest', $contestId);
		//only owner view it
		if(!$contest->IsOwner($viewer))			
			return $this->setNoRender();
		
		if($contest->contest_type == 'advalbum' || $contest->contest_type == 'ynvideo')
		{
			$this->view->height = (int)$this -> _getParam('heightadvalbum',160);
			$this->view->width = (int)$this -> _getParam('widthadvalbum',155);
		}
		else{
			$this->view->height = (int)$this -> _getParam('heightynblog',250);
			$this->view->width = (int)$this -> _getParam('widthynblog',60);
		}
		$this->view -> items_per_page = $items_per_page = (int)$this -> _getParam('number',1);		
		
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');

		$select = $table -> select() -> where('contest_id = ?', $contest->contest_id) -> where("entry_status = 'published' or entry_status = 'win'") -> where("approve_status = 'approved'")-> order('start_date DESC') ;

		$results = $table->fetchAll($select);
		
		// /print_r($_SESSION['entries']);die;
		$this->view->widgettype = 'winning_entry';
		
		$this->view->manageentries = $manageentries = Zend_Paginator::factory($results);
			
		$manageentries -> setItemCountPerPage($items_per_page);
		
		$manageentries -> setCurrentPageNumber( $request->getParam('pagewinning_entry', 1));
		if ($request->isPost()) {
			$this->getElement()->removeDecorator('Title');
				
			$params = $request->getParams();
			
			if(isset($params['save'])){
				$value = array();			
				//set entry win by owner by ajax
							
				//set entry win by vote
				if($contest->contest_status == 'close'){
					if($contest->award_number > (int)$params['award_number']){
						//send notify no_win to member and follower
						$entries = Engine_Api::_() -> getItemTable('yncontest_entries') -> getEntryByvote(array(
								'contestID' => $contest -> contest_id,
								'award_number' =>$contest->award_number,
						));
						$i = 0;
						foreach($entries as $entry){
							$i++;
							if($i>(int)$params['award_number'])	{
								$entry->entry_status = 'published';
								$entry->save();
								$user = Engine_Api::_() -> user() -> getUser($entry -> user_id);
								//send notify to entry win by vote
								$entry->sendNotMailOwner($user, $entry, 'entry_no_win_vote', null)	;
							
								$follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
								$followUsers = $follow_table->getUserFolowContest($contest->contest_id);
								foreach($followUsers as $followUser){
									//send notification
									if($user->user_id != $followUser -> user_id){
										$f_user = Engine_Api::_() -> user() -> getUser($followUser -> user_id);
										$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
										$notifyApi -> addNotification($f_user,$entry, $contest, 'entry_no_win_vote_f');
									}
								}
							}
						}
					}
				}

				$contest->award_number = $params['award_number'];
				$contest->vote_desc = $params['vote_desc'];
				$contest->reason_desc = $params['reason_desc'];
				$contest->save();
				unset($_SESSION['entries']);
				Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoRoute(array('action' => 'view', 'contestId'=> $contest->contest_id), 'yncontest_mycontest', true);
			}
		}

	}


}