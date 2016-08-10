<?php
class Ynresume_Model_Language extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_language';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
    public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
    	$text  = "<h3>{$this->name}</h3>";
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
