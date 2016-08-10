<?php

class Yncontest_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function init()
	{
		parent::init();
		Zend_Registry::set('admin_active_menu', 'yncontest_admin_main_settings');
	}

	public function indexAction()
	{
		$this -> view -> form = $form = new Yncontest_Form_Admin_Global();		
				
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
		{
			$values = $form -> getValues();

			foreach ($values as $key => $value)
			{				
				if ($value < 0)
				{
					$value = 0;
				}
				if($key != "yncontest_currency")
					Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, round($value, 2));

			}						
						
			
			$form -> addNotice('Your changes have been saved.');

		}

	}

}
