<?php
class Ynresume_AdminIndustriesMappingController extends Core_Controller_Action_Admin {
	
	public function init() {
		$checkModuleJob = Engine_Api::_() -> hasModuleBootstrap('ynjobposting');
		if(empty($checkModuleJob))
		{
			return $this->_helper->requireSubject()->forward();
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_industries');
	}

	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('industries', 'ynresume');
	}

	public function indexAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> industries = $node -> getChilren();
		$this -> view -> industry  =  $node;
	}
	
	public function saveMappingAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$tableIndustrymaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynresume');
		$idResume = $this ->_getParam('idResume');
		$idJob = $this ->_getParam('idJob');
		$row = $tableIndustrymaps -> getRowByIndustryId($idResume);
		if(!empty($row))
		{
			$row -> job_industry_id = $idJob;
			$row -> save();
		}
		else
		{
			$row = $tableIndustrymaps -> createRow();
			$row -> industry_id = $idResume;
			$row -> job_industry_id = $idJob;
			$row -> save();
		}
		
		echo Zend_Json::encode(array('error_code' => 0));
		exit ;
	}
}
