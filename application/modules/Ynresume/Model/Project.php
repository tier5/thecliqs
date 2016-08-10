<?php
class Ynresume_Model_Project extends Core_Model_Item_Abstract 
{
    protected $_type = 'ynresume_project';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
    public function getMembers()
    {
    	$userTbl = Engine_Api::_()->getItemTable('user');
    	$projectMemberTbl = Engine_Api::_()->getDbTable('projectmembers', 'ynresume');
    	$select = $projectMemberTbl 
    	-> select()
    	-> where ("project_id = ?", $this -> getIdentity());
    	$projectMembers = $projectMemberTbl -> fetchAll($select);
    	if (!count($projectMembers))
    	{
    		return array();
    	}
    	$members = array();
        foreach ( $projectMembers as $m )
        {
        	if ($m->user_id != 0)
        	{
        		$members[] = Engine_Api::_()->getItem('user', $m->user_id);
        	}
        	else 
        	{
        		$member = $userTbl -> createRow();
        		$member -> displayname = $m -> name;
        		$members[] = $member;
        	}
        }
        return $members;
    }
    
	public function getMemberObjects()
    {
    	$projectMemberTbl = Engine_Api::_()->getDbTable('Projectmembers', 'ynresume');
    	$select = $projectMemberTbl 
    	-> select()
    	-> where ("project_id = ?", $this -> getIdentity())
    	-> order ("order ASC")
    	;
        return $projectMemberTbl -> fetchAll($select);
    }
    
    public function getMember($user)
    {
    	$projectMemberTbl = Engine_Api::_()->getDbTable('projectmembers', 'ynresume');
    	$select = $projectMemberTbl 
    	-> select()
    	-> where ("project_id = ?", $this -> getIdentity())
    	-> limit (1);
    	
    	if (is_object($user))
    	{
    		$select -> where ("user_id = ?", $user -> getIdentity());
    	}
    	else if (is_string($user))
    	{
    		$select -> where ("name = ?", $user);
    	}
    	
    	return $projectMemberTbl -> fetchRow($select);
    }
    
    public function hasMember($user)
    {
    	$member = $this->getMember($user);
    	return (!is_null($member));
    }
    
    public function addMember($user)
    {
    	$projectMemberTbl = Engine_Api::_()->getDbTable('projectmembers', 'ynresume');
    	$member = $projectMemberTbl -> createRow();
    	$member -> project_id = $this -> getIdentity();
    	
    	if (!$this->hasMember($user))
		{
			if (is_object($user))
	    	{
	    		$member -> user_id = $user -> getIdentity();
	    		$member -> name = $user -> getTitle();
	    	}
	    	elseif (is_string($user))
	    	{
	    		$member -> user_id = 0;
	    		$member -> name = strip_tags($user);
	    	}
	    	$member -> save();
    	}
    	else 
    	{
    		throw new Exception("Already member!", 1);
    	}
    }
    
    public function removeMember($user)
    {
    	if ($this->hasMember($user))
		{
    		$member = $this -> getMember($user);
    		$member -> delete();
    	}
    	else 
    	{
    		throw new Exception("No member to remove!", 1);
    	}
    }
    
    public function removeAllMembers()
    {
    	$members = $this -> getMemberObjects();
    	foreach ($members as $member){
    		$member -> delete();
    	}
    }
    
    public function getMemberAsString()
    {
    	$members = $this -> getMemberObjects();
    	$arr = array();
    	foreach ($members as $member)
    	{
    		if ($member -> user_id > 0)
    		{
    			$arr[] = $member -> user_id;
    		}
    		else
    		{
				$arr[] = $member -> name;    			
    		}
    	}
    	if (count($arr) == 0)
    	{
    		return '';
    	}
    	return implode(',', $arr);
    }
    
    
	public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
    	if ($this -> occupation_type && $this -> occupation_id)
    	{
			$occupation = Engine_Api::_()->getItem($this -> occupation_type, $this -> occupation_id);
		}
    	$text  = "<h4>{$this->name}</h4>";
    	if ($occupation)
    	{
    		$text .= "<div><b>{$translate->_("Occupation")}</b> {$occupation -> title}</div>";
    	}
    	$start_month = ($this->start_month) ? $this->start_month : 1;
		$start_date = date_create($this->start_year.'-'.$start_month.'-'.'1');
		if ($this->start_month) {
			$start_time = date_format($start_date, 'M Y');
		}
		else {
			$start_time = date_format($start_date, 'Y');
		}
		if ($this->end_year) {
			$end_month = ($this->end_month) ? $this->end_month : 1;
			$end_date = date_create($this->end_year.'-'.$end_month.'-'.'1');
			if ($this->end_month) {
				$end_time = date_format($end_date, 'M Y');
			}
			else {
				$end_time = date_format($end_date, 'Y');
			}
		}
		else {
			$end_date = date_create();
			$end_time = $translate->_('Present');
		}
		$diff = date_diff($start_date, $end_date);
    	
    	$text .= <<<EOF
    <div>
    	<span>{$start_time}</span>
		<span>-</span>
		<span>{$end_time}</span>
    </div>
EOF;
    	if ($this->url)
    	{
    		$text .= "<div><b>{$translate->_("Project URL")}</b> {$this->url}</div>";
    	}
    	
    	if ($this->description)
    	{
    		$text .= "<div>{$this->description}</div>";
    	}
    	
    	$members = $this -> getMemberObjects();
    	if (count($members))
    	{
    		$text .= "<div>";
    		foreach ($members as $member)
    		{
    			if ($member->user_id > 0)
    			{
    				$member = Engine_Api::_()->getItem('user', $member->user_id);
					$memberName = $member -> getTitle();
    			}
    			else 
    			{
    				$memberName = $member -> name;
    			}
    			$text .= "<span>{$memberName}</span>";
    		}
    		$text .= "</div>";
    	}
		return $text;
    }
}
