<?php

class Ynresume_Model_SkillMap extends Core_Model_Item_Abstract
{
	protected $_searchTriggers = false;

	public function getTitle()
	{
		return $this->getSkill()->getTitle();
	}

	public function getDescription()
	{
		return $this->getSkill()->getDescription();
	}

	public function getHref($params = array())
	{
		return $this->getSkill()->getHref($params);
	}

	public function getSkill()
	{
		return Engine_Api::_()->getItem('ynresume_skill', $this->skill_id);
	}

	public function getUser()
	{
		return Engine_Api::_()->getItem('user', $this->user_id);
	}

	public function getResume()
	{
		return Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
	}
	
	public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
    	$text  = "<h4>{$this->title}</h4>";
    	$proficiencyArr = array(
			'' => 'Choose...', 
			'elementary' => $translate->_('Elementary'), 
			'limited working' => $translate->_('Limited Working'), 
			'professional working' => $translate->_('Professional Working'), 
			'fill working' => $translate->_('Fill Working'), 
			'native or bilingual' => $translate->_('Native or Bilingual')
		);
		if ($this->proficiency)
		{
			$text .= "<div><b>{$translate->_("Proficiency")}</b> {$this->proficiency}</div>";
		}
		return $text;
    }
}