<?php

class Yncontest_Model_Award extends Core_Model_Item_Abstract {
	
		
	public function getTitle(){
	
		if(isset($this->award_name)){
			return $this->award_name;
		}
		return null;
	}
}
