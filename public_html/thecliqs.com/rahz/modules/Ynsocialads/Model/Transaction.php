<?php
 
class Ynsocialads_Model_Transaction extends Core_Model_Item_Abstract
{
	public function getAdHref() {
        $ad = Engine_Api::_()->getItem('ynsocialads_ad', $this->ad_id);
		if($ad)
        	return $ad->getHref();
		else {
			return NULL;
		}
    }
}