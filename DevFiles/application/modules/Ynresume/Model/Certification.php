<?php
class Ynresume_Model_Certification extends Core_Model_Item_Abstract 
{
    protected $_type = 'ynresume_certification';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
	public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$text  = "<h4>{$this->name}</h4>";
    	
    	if ($this->authority)
    	{
    		$text .= "<div><b>{$translate->_("Certification Authority")}</b> {$this->authority}</div>";
    	}
    	
    	if ($this->url)
    	{
    		$text .= "<div><b>{$translate->_("Certification URL")}</b> {$this->url}</div>";
    	}
    	
    	if ($this->license_number)
    	{
    		$text .= "<div><b>{$translate->_("License Number")}</b> {$this->license_number}</div>";
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
    	$text .= <<<EOF
    	<div>
    		<span class="start-time">{$start_time}</span>
            <span>-</span>
            <span class="end-time">{$end_time}</span>
		</div>
EOF;
		return $text;
    }
}
