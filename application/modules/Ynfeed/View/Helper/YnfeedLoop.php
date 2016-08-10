<?php
class Ynfeed_View_Helper_YnfeedLoop extends Ynfeed_View_Helper_Ynfeed {
	public function ynfeedLoop($actions = null, array $data = array()) 
	{
		if (null == $actions) {
			return '';
		}
		if(!is_array($actions))
		{
			$tmp_actions = array();
			foreach ($actions as $action) 
			{
				$tmp_actions[] = $action;
			}
			$actions = $tmp_actions;
		}
		if(!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract))
		{
			return '';
		}

		$form = new Activity_Form_Comment();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$activity_moderate = "";
		$group_owner = "";
		$group = "";
		try {
			if(Engine_Api::_() -> core() -> hasSubject('group'))
				$group = Engine_Api::_() -> core() -> getSubject('group');
		} catch( Exception $e) {
		}
		if ($group) 
		{
			$table = Engine_Api::_() -> getItemtable('group');
			$select = $table -> select() -> where('group_id = ?', $group -> getIdentity()) -> limit(1);
			$row = $table -> fetchRow($select);
			$group_owner = $row['user_id'];
		}
		if ($viewer -> getIdentity()) {
			$activity_moderate = Engine_Api::_() -> getDbtable('permissions', 'authorization') -> getAllowed('user', $viewer -> level_id, 'activity');
		}
		$data = array_merge($data, array(
    		'actions' => $actions, 
    		'commentForm' => $form, 
    		'user_limit' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity_userlength'), 
    		'allow_delete' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity_userdelete'), 
    		'activity_group' => $group_owner, 
    		'activity_moderate' => $activity_moderate, 
    		'allowSaveFeed' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeed_savefeed', true) 
            ));
		if (Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment()) 
		{
            $data = array_merge($data, array('replyForm' => new Yncomment_Form_Reply()));
            if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.commentreverseorder', false)) 
            {
                return $this->view->partial('_yncomment_activityText.tpl', 'yncomment', $data);
            } 
            else 
            {
                return $this->view->partial('_yncomment_activityText_reverse_chronological.tpl', 'yncomment', $data);
            }
        } 
        else 
        {
	       return $this -> view -> partial('_activityText.tpl', 'ynfeed', $data);
        }
	}

}
