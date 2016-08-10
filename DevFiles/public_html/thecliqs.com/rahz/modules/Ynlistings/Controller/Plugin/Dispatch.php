<?php

class Ynlistings_Controller_Plugin_Dispatch extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();

		$key = 'ynlistings_predispatch_url:' . $module . '.' . $controller . '.' . $action;
		if (isset($_SESSION[$key]) && $_SESSION[$key]) {
			$url = $_SESSION[$key];
			header('location:' . $url);
			unset($_SESSION[$key]);
			@session_write_close();
			exit ;
		}
	}
}
