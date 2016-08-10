<?php
class Yncredit_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_settings');

		$this->view->form  = $form = new Yncredit_Form_Admin_Global();

		if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
		{
			$values = $form->getValues();

			foreach ($values as $key => $value){
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
			}
			$form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
		}
	}

}