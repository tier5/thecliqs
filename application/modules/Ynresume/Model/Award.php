<?php
class Ynresume_Model_Award extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_award';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
    public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$month = array('Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    	$text  = "<h4>{$this->title}</h4>";
    	if ($this->occupation_type && $this->occupation_id)
    	{
    		$occupation = Engine_Api::_()->ynresume()->getPosition2($this->occupation_type, $this->occupation_id);
    		$text .= "<div><b>{$occupation[0]}</b>  {$occupation[1]}</div>";
    	}
    	if ($this->issuer)
    	{
    		$text .= "<div><b>{$this->issuer}</b></div>";
    	}
    	if ($this->date_year)
    	{
    		$text .= "<div>";
    		if ($this->date_month)
    		{
	    		$text .= "<span>{$translate->_($month[$this->date_month])}</span>"; 
    		}
    		$text .= "<span>{$translate->_($this->date_year)}</span>"; 
    		$text .= "</div>";
    	}
		return $text;
    }
}
