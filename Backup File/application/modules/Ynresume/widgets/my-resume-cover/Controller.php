<?php
class Ynresume_Widget_MyResumeCoverController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		
	    $viewer = Engine_Api::_()->user()->getViewer();
     	if(!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
		$resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
		$this -> view -> resume = $resume = $resumeTable -> getResume($viewer -> getIdentity());
		if(empty($resume))
		{
			return $this -> setNoRender();
		}
		$isEdit = $this ->_getParam('isEdit', false);
		if(!$resume -> active)
		{
			$isEdit = true;
		}
		if($isEdit)
		{
			if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages'))
			{
				$tableBusiness = Engine_Api::_()-> getItemTable('ynbusinesspages_business');
				$select = $tableBusiness -> getBusinessesSelect(array('status' => 'published'));
				$businesses = $tableBusiness -> fetchAll($select);
				$this -> view -> businesses = $businesses;
			}
		}
		$this -> view -> isEdit = $isEdit;
	}
}
