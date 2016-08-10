<?php
class Ynbusinesspages_Widget_BusinessManageMenuController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
     	if(!$viewer -> getIdentity())
		{
			$this -> setNoRender();
		}
		$request = Zend_Controller_Front::getInstance() -> getRequest();    
		$this -> view -> action = $request -> getActionName();
	}
}
