<?php
class Ynbusinesspages_Widget_BusinessProfileOperatingHoursController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		
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
        
		//operation hours
		$tableOperationHour = Engine_Api::_() -> getDbTable('operatinghours', 'ynbusinesspages');
		$operationHours = $tableOperationHour -> getHoursByBusinessId($subject -> getIdentity());
		if(!count($operationHours))
		{
			return $this -> setNoRender();
		}
		$this -> view -> operationHours = $operationHours;
		
		
	}
}
	