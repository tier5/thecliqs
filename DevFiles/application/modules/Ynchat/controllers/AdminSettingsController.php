<?php
class Ynchat_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynchat_admin_main', array(), 'ynchat_admin_settings_global');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->form = $form = new Ynchat_Form_Admin_Settings_Global();
    
        if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();
            if ($values['ynchat_chatbox_userip'] && ($values['ynchat_chatbox_ipaddress'] == '')) {
                $form->addError('IP address is required!');
                return;
            }
            foreach ($values as $key => $value) {
                $settings->setSetting($key, $value);
            }
            $form->addNotice('Your changes have been saved.'); 
        }
    }
}