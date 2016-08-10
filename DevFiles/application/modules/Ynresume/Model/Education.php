<?php
class Ynresume_Model_Education extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_education';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
    public function renderText()
    {
    	$view = Zend_Registry::get('Zend_View');
    	$resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
    	$translate = Zend_Registry::get("Zend_Translate");
    	$degree = Engine_Api::_()->getDbTable('degrees', 'ynresume')->getDegreeById($this->degree_id);
        $degree = ($degree) ? $degree->name : $view->translate('Unknown');
        
    	$text  = "<h4>{$this->title}</h4>";
    	$text .= "<div><b>{$this->study_field}</b></div>";
    	$text .= "<div>{$this->attend_from} - {$this->attend_to}</div>";
    	$text .= "<div><b>{$translate->_("Degree")}</b> {$degree}</div>";
    	
    	if ($this->grade)
    	{
    		$text .= "<div><b>{$translate->_("Grade")}</b> {$this->grade}</div>";
    	}
    	
    	if ($this->activity)
    	{
    		$text .= "<div><b>{$translate->_("Activities & Societies")}</b> {$this->activity}</div>";
    	}
    	
    	if ($this->description)
    	{
    		$text .= "<div>{$this->description}</div>";
    	}
    	
    	$recommendations = Engine_Api::_()->ynresume()->getShowRecommendationsOfOccupation('education', $this->getIdentity(), $resume->user_id);
		if (count($recommendations))
		{
            $text .= "<div>";
            foreach ($recommendations as $recommendation)
            {
            	$giver = $recommendation->getGiver();
            	$text .= <<<EOF
	<div>
		<div><b>{$giver->getTitle()}</b></div>
		<div>{$recommendation->content}</div>
	</div>
EOF;
            }
            $text .= "</div>";
        }
		return $text;
    }
}
