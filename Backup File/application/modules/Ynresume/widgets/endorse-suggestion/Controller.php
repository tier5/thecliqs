<?php
class Ynresume_Widget_EndorseSuggestionController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        ini_set('error_reporting', -1);
		$this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
		if (is_null($resume))
		{
			return $this->setNoRender();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
        $canEndorse = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'endorse')->checkRequire();
        if (!$canEndorse)
        {
            return $this->setNoRender();
        }
		if ($viewer -> getIdentity() == 0)
		{
			return $this -> setNoRender();
		}
		if ($resume -> skipedByUser($viewer))
		{
			return $this -> setNoRender();
		}
		$this->view->owner = $owner = $resume -> getOwner();
		$totalSkills = Engine_Api::_()->getDbtable('skills', 'ynresume')->getSkillsByUser($resume, $owner);
		$endorsedSkills = Engine_Api::_()->getDbtable('skills', 'ynresume')->getSkillsByUser($resume, $viewer);
		
		$endorsed = array();
		foreach ($endorsedSkills as $skill)
		{
			$endorsed[] = $skill -> skill_id;
		}
		
		$new = array();
		foreach ($totalSkills as $skill)
		{
			if (in_array($skill->skill_id, $endorsed))
				continue;
			$new[] = $skill;
		}
		
		$this -> view -> userSkills = $userSkills = $new;
		if (!count($userSkills))
		{
			return $this->setNoRender();
		}
		
	}
}
