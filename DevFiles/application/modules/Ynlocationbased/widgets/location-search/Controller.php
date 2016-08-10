<?php
class Ynlocationbased_Widget_LocationSearchController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance ()->getRequest();
		$params = $request -> getParams();
		$this -> view -> hasWithIn = isset($params['within']);

		$module_name = $request -> getModuleName();
		$supportModule = Engine_Api::_() -> getDbTable('modules', 'ynlocationbased') -> getModule($module_name);
		if($supportModule && !$supportModule -> enabled)
			return $this -> setNoRender();
	}

}
