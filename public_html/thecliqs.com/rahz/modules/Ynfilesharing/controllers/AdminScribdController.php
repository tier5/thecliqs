<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */

class Ynfilesharing_AdminScribdController extends Core_Controller_Action_Admin {
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('ynfilesharing_admin_main', array(), 'ynfilesharing_admin_main_scribd');

		// Make form
		$this->view->form = $form = new Ynfilesharing_Form_Admin_Scribd();
		// get settings
		$settings = Engine_Api::_()->getApi('settings', 'core');
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