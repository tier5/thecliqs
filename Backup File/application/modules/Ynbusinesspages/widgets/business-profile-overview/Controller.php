<?php
class Ynbusinesspages_Widget_BusinessProfileOverviewController extends Engine_Content_Widget_Abstract {
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
        
		//getpackage 
		$this -> view -> package = $package = $subject -> getPackage();
		
		$view = $this -> view;
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> business = $business = $subject;

		$coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
		$this -> view -> covers = $covers = $coverTbl -> getCoverByBusiness($business);
		
		//founder
		$tableFounder = Engine_Api::_() -> getDbTable('founders', 'ynbusinesspages');
		$founders = $tableFounder -> getFoundersByBusinessId($subject -> getIdentity());
		$this -> view -> founders = $founders;
		
		//location
		$tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
		$locations = $tableLocation -> getLocationsByBusinessId($subject -> getIdentity());
		$this -> view -> locations = $locations;
		
		if(($package -> getIdentity() > 0) && ($package -> allow_owner_add_customfield))
		{
			//additional infos
			$tableBusinessInfo = Engine_Api::_() -> getDbTable('businessinfos', 'ynbusinesspages');
			$businessInfos = $tableBusinessInfo -> getRowsInfoByBusinessId($subject -> getIdentity());
			$this -> view -> businessInfos = $businessInfos;
		}
		
		//categories
		$tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
		$categoryMaps = $tableCategoryMap -> getCategoriesByBusinessId($subject -> getIdentity());
		$this -> view -> categoryMaps = $categoryMaps;
	}
}
	