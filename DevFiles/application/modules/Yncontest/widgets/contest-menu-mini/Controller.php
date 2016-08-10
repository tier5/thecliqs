<?php
class Yncontest_Widget_ContestMenuMiniController extends Engine_Content_Widget_Abstract
{
	
	
	public function indexAction()

	{

// 		$viewer = Engine_Api::_() -> user() -> getViewer();
//         if(!is_object($viewer)) {
//         	$this->setScriptPath('application/modules/Yncontest/views/scripts/');
//         	return;
//         }		

		if (Zend_Registry::isRegistered('active_mini_menu'))
		{
			$active_menu = Zend_Registry::get('active_mini_menu');
		}
		else
		{
			$active_menu = null;
		}
		
		$this -> view -> active_menu = @$active_menu;
		
	}

}
