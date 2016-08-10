<?php
class Ynresume_SkillController extends Core_Controller_Action_Standard
{
	public function saveEndorsementsAction()
	{
		$skillmapIds = $this->_getParam('skillmap_ids'); 
		$resumeId = $this->_getParam('resume_id'); 
		if (!count($skillmapIds) || !$resumeId){
			echo Zend_Json::encode(array(
				'error_code' => 1,
				'error_message' => 'invalid params'
			)); exit;
		}
		$resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			echo Zend_Json::encode(array(
				'error_code' => 2,
				'error_message' => 'please login'
			)); exit;
		}
		$table = Engine_Api::_()->getDbTable('SkillMaps', 'ynresume');
    	$adapter = $table -> getDefaultAdapter();
    	$skillmapStr = implode(',', $skillmapIds);

        /**
         * set deleted status
         */
    	$sql = "UPDATE `engine4_ynresume_skillmaps` SET `deleted` = 1 WHERE `skillmap_id` NOT IN ($skillmapStr) ";
    	$sql .= "AND `resume_id` = '$resumeId'";
    	$adapter -> query($sql);

        /**
         * unset deleted status
         */
        $sql = "UPDATE `engine4_ynresume_skillmaps` SET `deleted` = 0 WHERE `skillmap_id` IN ($skillmapStr) ";
        $sql .= "AND `resume_id` = '$resumeId'";
        $adapter -> query($sql);

		$notify = $this -> _getParam('notify');
		if (isset($notify))
		{
			$endorseNotifyTbl = Engine_Api::_() -> getDbTable('EndorseNotify', 'ynresume');
			$endorseNotifyTbl -> saveNotify($resume, $notify);
		}
	}
	
	public function endorseAction()
	{
		$skills = $newSkills = $this->_getParam('skills');
		$resumeId = $this->_getParam('resume_id'); 
		if (!count($skills) || !$resumeId){
			echo Zend_Json::encode(array(
				'error_code' => 1,
				'error_message' => 'invalid params'
			)); exit;
		}
		$resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			echo Zend_Json::encode(array(
				'error_code' => 2,
				'error_message' => 'please login'
			)); exit;
		}
		$originalSkills = Engine_Api::_()->getDbtable('skills', 'ynresume')->getSkillsByUser($resume, $viewer);
		$originalSkillTexts = array();
		$deleted = array();
		foreach ($originalSkills as $skill){
			if (!in_array($skill->text, $newSkills))
			{
				$deleted[] = $skill -> skill_id;
			}
			$originalSkillTexts[] = $skill->text;
		}
        //var_dump($deleted); exit;
		if (count($deleted))
		{
            //echo 1; exit;
			$resume -> skills() -> removeSkillMapsBySkillIds($deleted);
		}
		$notify = $this -> _getParam('notify');
		if (isset($notify))
		{
			$endorseNotifyTbl = Engine_Api::_() -> getDbTable('EndorseNotify', 'ynresume');
			$endorseNotifyTbl -> saveNotify($resume, $notify);
		}
		
		$resume -> skills() -> addSkillMaps($viewer, $skills);
		$hasNewSkill = false;
		foreach ($newSkills as $skill){
			if (!in_array($skill, $originalSkillTexts))
			{
				$hasNewSkill = true;
				break;
			}
		}
		if ($hasNewSkill)
		{
			 $db = Engine_Db_Table::getDefaultAdapter();
			 $db -> query("UPDATE engine4_ynresume_skips SET `value` = 0 WHERE `resume_id` = '1'");
		}
	}
	
	public function endorseOneAction()
	{
		$skill = $this->_getParam('skill');
		$resumeId = $this->_getParam('resume_id'); 
		if (!$skill || !$resumeId){
			echo Zend_Json::encode(array(
				'error_code' => 1,
				'error_message' => 'invalid params'
			)); exit;
		}
		$resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			echo Zend_Json::encode(array(
				'error_code' => 2,
				'error_message' => 'please login'
			)); exit;
		}
		$resume -> skills() -> addSkillMap($viewer, $skill);
	}
	
	public function unendorseOneAction()
	{
		 ini_set('display_startup_errors', 1);
		 ini_set('display_errors', 1);
		 ini_set('error_reporting', -1);
		$skill = $this->_getParam('skill'); 
		$resumeId = $this->_getParam('resume_id'); 
		if (!$skill || !$resumeId){
			echo Zend_Json::encode(array(
				'error_code' => 1,
				'error_message' => 'invalid params'
			)); exit;
		}
		$resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			echo Zend_Json::encode(array(
				'error_code' => 2,
				'error_message' => 'please login'
			)); exit;
		}
		$resume -> skills() -> removeSkillMap($viewer, $skill);
	}
	
	public function skipAction()
	{
		$resumeId = $this->_getParam('resume_id'); 
		if (!$resumeId){
			echo Zend_Json::encode(array(
				'error_code' => 1,
				'error_message' => 'invalid params'
			)); exit;
		}
		$resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			echo Zend_Json::encode(array(
				'error_code' => 2,
				'error_message' => 'please login'
			)); exit;
		}
		$resume -> setSkip($viewer);
	}
	
	public function sortAction() 
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $skillTable = Engine_Api::_()->getDbTable('Skills', 'ynresume');
        $resumeId = $this->getRequest()->getParam('resume_id');
        if (!$resumeId)
        {
        	echo Zend_Json::encode(array(
        		'error_message' => 'no resume identity.'
        	)); exit;
        }
        $resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
       	$skillMaps = $resume -> getOwnerSkillMaps();
		
        $order = explode(',', $this->getRequest()->getParam('order'));
        foreach($order as $i => $item) {
            $skillmap_id = substr($item, strrpos($item, '_') + 1);
            foreach($skillMaps as $skillmap) {
                if($skillmap->getIdentity() == $skillmap_id) {
                    $skillmap->order = $i;
                    $skillmap->save();
                }
            }
        }
    }

    public function suggestAction()
    {
    	$skills = Engine_Api::_()->getDbtable('skills', 'ynresume')->getSkillsByText($this->_getParam('text'), $this->_getParam('limit', 40));
    	$data = array();
    	$mode = $this->_getParam('struct');

    	if( $mode == 'text' )
    	{
			foreach( $skills as $skill )
			{
	      		$data[] = $skill->text;
	      	}
    	}
    	else
    	{
			foreach( $skills as $skill )
		    {
		      	$data[] = array(
		          'id' => $skill->skill_id,
		          'label' => $skill->text
		      	);
		    }
    	}

    	if( $this->_getParam('sendNow', true) )
    	{
      		return $this->_helper->json($data);
    	}
    	else
    	{
			$this->_helper->viewRenderer->setNoRender(true);
		    $data = Zend_Json::encode($data);
		    $this->getResponse()->setBody($data);
    	}
    }

    public function endorsersAction()
    {
        $skillText = $this -> _getParam('skill', '');
        $resumeId = $this -> _getParam('resume_id', '0');
        if (!$skillText || $resumeId == '0')
        {
            return $this->_helper->requireSubject()->forward();
        }
        $resume = Engine_Api::_()->getItem('ynresume_resume', $resumeId);
        $skillTbl = Engine_Api::_()->getDbtable('skills', 'ynresume');
        $skillText = $skillTbl->formatSkillText($skillText);
        $select = $skillTbl->select()->where("text = ?", $skillText);
        $skill = $skillTbl->fetchRow($select);
        if (is_null($skill))
        {
            return $this->_helper->requireSubject()->forward();
        }
        $this -> view -> endorses = $endorses = $skill -> getEndorsedUsers($resume);

    }
}