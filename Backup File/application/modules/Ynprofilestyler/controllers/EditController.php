<?php

class Ynprofilestyler_EditController extends Core_Controller_Action_Standard
{
	public function myStyleAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		$route = Zend_Controller_Front::getInstance() -> getRouter();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $viewer -> getIdentity();
		if (isset($viewer -> username))
		{
			$id = $viewer -> username;
		}
		$url  = $route -> assemble(array('id' => $id), 'user_profile');
		$url .= '?edit-style=1';
		header('location: '. $url);
	}
}