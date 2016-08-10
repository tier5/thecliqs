<?php
/**
*
* @category   Application_Ynmobile
* @package    Ynmobile
* @copyright  Copyright 2014 YouNet Company
* @license    http://socialengine.younetco.com
* @author     LongL
*/

class Ynmobile_Service_Poll extends Ynmobile_Service_Base
{
    
    protected $module = 'poll';
    protected $mainItemType = 'poll';
    
        /**
     * form add
     */
    public function formadd($aData) {

        $response = array(
            'viewOptions' => $this -> viewOptions(),
            'commentOptions' => $this -> commentOptions(),
            'iMaxOptions' => Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.maxoptions', 15),
        );

        return $response;
    }
    
	/**
	 * CREATING POLL BY MOBILE APIs
	 * @param array $aData with 
	 * + sTitle
	 * + sDescription
	 * + aOptions
	 * @return array $result
	 */	
	public function create($aData)
	{
		extract($aData);
		if (!isset($sTitle) || empty($sTitle))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll title")
			);
		}
		
		if (!isset($aOptions) || !count($aOptions))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll options")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.maxoptions', 15);
		// Check options
		$options = (array) $aOptions;
		$options = array_filter(array_map('trim', $options));
		$options = array_slice($options, 0, $max_options);
		$this->view->options = $options;
		
		if( empty($options) || !is_array($options) || count($options) < 2 ) 
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You must provide at least two possible answers.")
			);
		}
		
		foreach( $options as $index => $option ) 
		{
			if( strlen($option) > 80 ) 
			{
				$options[$index] = Engine_String::substr($option, 0, 80);
			}
		}
		
		// Process
		$pollTable = Engine_Api::_()->getItemTable('poll');
		$pollOptionsTable = Engine_Api::_()->getDbtable('options', 'poll');
		$db = $pollTable->getAdapter();
		$db->beginTransaction();
		
		try {
			$values = array(
				'title' => $sTitle,
				'description' => $sDescription,
				'search' => $iSearch,
				'user_id' => $viewer->getIdentity(),
				'auth_view' => $auth_view,
				'auth_comment' => $auth_comment,
			);
		
			// Create poll
			$poll = $pollTable->createRow();
			$poll->setFromArray($values);
			$poll->save();
		
			// Create options
			$censor = new Engine_Filter_Censor();
			$html = new Engine_Filter_Html(array('AllowedTags'=> array('a')));
			foreach( $options as $option ) 
			{
				$option = $censor->filter($html->filter($option));
				$pollOptionsTable->insert(array(
						'poll_id' => $poll->getIdentity(),
						'poll_option' => $option,
				));
			}
		
			// Privacy
			$auth = Engine_Api::_()->authorization()->context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		
			if( empty($values['auth_view']) ) 
			{
				$values['auth_view'] = array('everyone');
			}
			if( empty($values['auth_comment']) ) 
			{
				$values['auth_comment'] = array('everyone');
			}
		
			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
		
			foreach( $roles as $i => $role ) 
			{
				$auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
			}
		
			$auth->setAllowed($poll, 'registered', 'vote', true);
		
			$db->commit();
		} 
		catch( Exception $e ) 
		{
			$db->rollback();
			return array(
					'error_code' => 2,
					'error_message' => $e->getMessage(),
			);
		}
		
		// Process activity
		$db = Engine_Api::_()->getDbTable('polls', 'poll')->getAdapter();
		$db->beginTransaction();
		try 
		{
			$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(Engine_Api::_()->user()->getViewer(), $poll, 'poll_new');
			if( $action ) 
			{
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $poll);
			}
			$db->commit();
		} 
		catch( Exception $e ) 
		{
			$db->rollback();
			return array(
					'error_code' => 3,
					'error_message' => $e->getMessage(),
			);
		}
		
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Created poll successfully."),
				'iPollId' => $poll->getIdentity()
		);
	}
	
	public function edit($aData)
	{
		extract($aData);
		if (!isset($iPollId) || empty($iPollId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll identity.")
			);
		}
		$poll = Engine_Api::_()->getItem("poll", $iPollId);
		if (!is_object($poll))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get("Zend_Translate")->_("This poll is not existed.")
			);	
		}
        
		$viewer = Engine_Api::_()->user()->getViewer();
		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		
		try 
		{
			$values = array(
				'auth_view' => $auth_view,
				'auth_comment' => $auth_comment,
				// 'search' => $iSearch, // un-check.
			);
		
			// CREATE AUTH STUFF HERE
			if( empty($values['auth_view']) ) 
			{
				$values['auth_view'] = 'everyone';
			}
			
			if( empty($values['auth_comment']) ) 
			{
				$values['auth_comment'] = 'everyone';
			}
			
			$auth = Engine_Api::_()->authorization()->context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			
			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
		
			foreach( $roles as $i => $role ) 
			{
				$auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
			}
		
			// $poll->search = (bool) $values['search'];
			$poll->save();
			$db->commit();
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage(),
			);
		}
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try 
		{
			// Rebuild privacy
			$actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
			foreach( $actionTable->getActionsByObject($poll) as $action ) 
			{
				$actionTable->resetActivityBindings($action);
			}
			$db->commit();
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
					'error_code' => 2,
					'error_message' => $e->getMessage(),
			);
		}
		
		return array(
				'error_code' => 0,
				'message' => Zend_Registry::get("Zend_Translate")->_("Edited poll privacy successfully"),
				'iPollId' => $poll->getIdentity()
		);
	}
	
	public function detail($aData)
	{
		extract($aData);
        
        $iPollId = intval($iPollId);
        
        if(empty($fields)){
            $fields = 'id,type,title,desc,stats,options,user,canView,result';
        }
        
        $fields  = explode(',', $fields);
        
        $translate = Zend_Registry::get("Zend_Translate");
        $table = $this->getWorkingTable('polls','poll');
        
		$poll = $table->findRow($iPollId);
        
		if (!$poll){
			return array(
				'error_code' => 1,
				'error_message' => $translate->_("This poll is not existed.")
			);
		} 
		
		$owner = $poll->getOwner();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$owner->isSelf($viewer) ) 
		{
			$poll->view_count++;
			$poll->save();
		}
        
        return Ynmobile_AppMeta::_export_one($poll, $fields);
	}
	
	public function delete($aData)
	{
		extract($aData);
		if (!isset($iPollId) || empty($iPollId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll identity."),
			);
		}
		$poll = Engine_Api::_()->getItem("poll", $iPollId);
		if (!is_object($poll))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This poll is not existed.")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !Engine_Api::_()->authorization()->isAllowed($poll, null, 'delete')) 
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have any permission to delete this poll.")
			);
		}
	
		$db = $poll->getTable()->getAdapter();
		$db->beginTransaction();
	
		try 
		{
			$poll->delete();
			$db->commit();
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
					'error_code' => 2,
					'error_message' => $e->getMessage()
			);
		}
	
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Deleted poll successfully.")
		);
	}
	
	public function fetch($aData)
	{
		extract($aData);
        
        $iPage = $iPage?intval($iPage):1;
        
        $iLimit= $iLimit?intval($iLimit):10;
        
        if(empty($fields)){
            $fields = 'id,type,title,desc,stats,user';    
        }
        
        $fields = explode(',', $fields);
        
		$viewer = Engine_Api::_()->user()->getViewer();
		$values = array();
		
        if($sView == 'my'){
            $values['user_id'] =  $viewer->getIdentity();
        } 
		else
		{
			$values['browse'] = 1;
		}
		
		/*
		 * $sSearch: search text
		 */
		if (isset($sSearch) && !empty($sSearch))
		{
			$values['search'] = $sSearch;
		}
		
		/*
		 * $sOrder
		 * recent: order by creation date
		 * popular: order by vote_count, view_count
		 */
		if (isset($sOrder) && in_array("$sOrder", array("recent", "popular")))
		{
			$values['order'] = $sOrder;
		}
		else
		{
			$values['order'] = "recent";
		}
		
		// Make paginator
		$select = $this->getWorkingTable('polls','poll')-> getPollSelect($values);
        
        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
	}
	
	public function close($aData)
	{
		extract($aData);
		if (!isset($iPollId) || empty($iPollId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll identity."),
			);
		}
		$poll = Engine_Api::_()->getItem("poll", $iPollId);
		if (!is_object($poll))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This poll is not existed.")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		// @todo convert this to post only
	
		$table = $poll->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try 
		{
			$poll->closed = 1;
			$poll->save();
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Closed poll successfully.")
			);
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}

	public function open($aData)
	{
		extract($aData);
		if (!isset($iPollId) || empty($iPollId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll identity."),
			);
		}
		$poll = Engine_Api::_()->getItem("poll", $iPollId);
		if (!is_object($poll))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This poll is not existed.")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		// @todo convert this to post only
	
		$table = $poll->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$poll->closed = 0;
			$poll->save();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get("Zend_Translate")->_("Opened poll successfully.")
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage()
			);
		}
	}
	
	/**
	 * all data needed for event edit form.
	 */
	public function formedit($aData){
	
		return array_merge(
				$this->detail($aData),
				$this->formadd(array())
		);
	}
	
	public function vote($aData)
	{
		extract($aData);
		if (!isset($iPollId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing poll identity."),
			);
		}
		$poll = Engine_Api::_()->getItem("poll", $iPollId);
		if (!is_object($poll))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This poll is not existed.")
			);
		}
		if (!isset($iOptionId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing option id."),
			);
		}
		$option_id = $iOptionId;
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!Engine_Api::_()->authorization()->isAllowed($poll, null, 'view'))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have any permission to view this poll.")
			);
		}
		if(!Engine_Api::_()->authorization()->isAllowed($poll, null, 'vote'))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have any permission to vote this poll.")
			);
		}
		
		$canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);
	
		if( $poll->closed ) 
		{
			return array(
					'error_code' => 3,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This poll is closed.")
			);
		}
	
		if( $poll->hasVoted($viewer) && !$canChangeVote ) 
		{
			return array(
					'error_code' => 3,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You have already voted on this poll, and are not permitted to change your vote.")
                    
			);
		}
	
		$db = Engine_Api::_()->getDbtable('polls', 'poll')->getAdapter();
		$db->beginTransaction();
		try 
		{
			$poll->vote($viewer, $option_id);
			$db->commit();
			
			return array(
			 'error_code'=>0,
			 'message'=>'Voted successfully.',
    		 'result'=>Ynmobile_AppMeta::_export_one($poll,explode(',','voters,stats,options'))
			);
		} 
		catch( Exception $e ) 
		{
			$db->rollback();
			return array(
					'error_code' => 4,
					'error_message' => $e->getMessage(),
			);
		}
	}

    function get_user_vote($poll, $user_id){
        $table =  $this->getWorkingTable('votes','poll');
        $select = $table->select()
            ->where('poll_id=?',$poll->getIdentity())
            ->where('user_id=?', intval($user_id));
            
        return $table->fetchRow($select);
    }

    function get_voter_select($poll)
    {
        $votesTable  = $this->getWorkingTable('votes','poll');
        $userTable =  $this->getWorkingTable('users','user');
        
        $select =  $userTable->select()
            ->setIntegrityCheck(false)
            ->from(array('users'=>$userTable->info('name')))
            ->join(array('votes'=>$votesTable->info('name')),'users.user_id=votes.user_id')
            ->where('votes.poll_id=?', $poll->getIdentity())
            ;
            
        return $select;
    }
    
    function voters($aData){
        extract($aData);
        $iPollId  = intval($iPollId);
        $iPage = $iPage?intval($iPage): 1;
        $iLimit  = $iLimit?intval($iLimit):20;
        
        
        $fields  = explode(',', $fields);
        
        $poll =  $this->getWorkingItem('poll', $iPollId);
        
        if(!$poll){
            return array(
                'error_code'=>1,
                'error_message'=> 'This poll not found',
            );
        }
        
        $select = $this->get_voter_select($poll, $iPage, $iLimit);
        
        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, array('simple_array'));
    }
}