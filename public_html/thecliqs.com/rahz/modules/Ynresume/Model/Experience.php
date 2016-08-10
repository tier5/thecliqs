<?php
class Ynresume_Model_Experience extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_experience';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
	public function renderText()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
        $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
    	$business = null; 
		if ($this->business_id) {
			$business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $this->business_id) : null;
		}
		$companyName = ($business && !$business->deleted) ? $business->getTitle() : $this->company;
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
		$period = $diff->format('%y')*12 + $diff->format('%m');
		
    	$text  = "<h4>{$this->title}</h4>";
    	$text .= "<div><b>{$companyName}</b></div>";
    	$text .= <<<EOF
    <div>
    	<span>{$start_time}</span>
		<span>-</span>
		<span>{$end_time}</span>
		<span class="location">{$this->location}</span>
    </div>
EOF;
		
    	
    	if ($this->description)
    	{
    		$text .= "<div>{$this->description}</div>";
    	}
    	
    	$recommendations = Engine_Api::_()->ynresume()->getShowRecommendationsOfOccupation('experience', $this->getIdentity(), $resume->user_id);
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
