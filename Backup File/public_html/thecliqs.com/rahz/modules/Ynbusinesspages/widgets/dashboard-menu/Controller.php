<?php
class Ynbusinesspages_Widget_DashboardMenuController extends Engine_Content_Widget_Abstract 
{
    public function indexAction()
    {
    	$request = Zend_Controller_Front::getInstance()->getRequest();
    	$this -> view -> active = '';
    	if ($request->getActionName())
    	{
    		$this -> view -> active = $request->getActionName();	
			$this -> view -> controller = $request -> getControllerName();
    	}
    }
}
