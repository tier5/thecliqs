<?php
class Ynbusinesspages_Widget_BusinessProfileContactInfoController extends Engine_Content_Widget_Abstract {
	public function indexAction() {

		$params = $this ->_getAllParams();
		if(isset($params['phone'])
			&& isset($params['fax']) 
			&& isset($params['email']) 
			&& isset($params['website'])
			&& isset($params['facebook'])
			&& isset($params['twitter'])
			&& !$params['phone'] 
			&& !$params['fax'] 
			&&  !$params['email'] 
			&&  !$params['website']
			&&  !$params['facebook']
			&&  !$params['twitter'])
		{
			return $this -> setNoRender();
		}

		if(!isset($params['phone']))
		{
			$params['phone'] = 1;
		}
		if(!isset($params['fax']))
		{
			$params['fax'] = 1;
		}
		if(!isset($params['email']))
		{
			$params['email'] = 1;
		}
		if(!isset($params['website']))
		{
			$params['website'] = 1;
		}
		if(!isset($params['facebook']))
		{
			$params['facebook'] = 1;
		}
		if(!isset($params['twitter']))
		{
			$params['twitter'] = 1;
		}

		$this -> view -> params = $params;
		
	 	 // Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject('ynbusinesspages_business');
        if (!$subject -> isViewable()) {
            return $this -> setNoRender();
        }
        
		if($subject -> is_claimed)
		{
			return $this -> setNoRender();
		}
		//get business
		$this -> view -> business = $business = $subject;
		if(!$business -> phone && !$business -> fax && !$business -> email && !$business -> web_address)
		{
			return $this -> setNoRender();
		}
	}
}