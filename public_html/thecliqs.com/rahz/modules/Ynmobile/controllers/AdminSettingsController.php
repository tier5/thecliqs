<?php

class Ynmobile_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynmobile_admin_main', array(), 'ynmobile_admin_main_settings');

		$this->view->form  = $form = new Ynmobile_Form_Admin_Global();

		if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
		{
			$values = $form->getValues();

			foreach ($values as $key => $value){
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
			}
			$form->addNotice('Your changes have been saved.');
		}
	}

}