<?php

class Ynmediaimporter_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') 
        -> getNavigation('ynmediaimporter_admin_main', array(), 'ynmediaimporter_admin_main_settings');

        $this -> view -> form = $form = new Ynmediaimporter_Form_Admin_Settings_Global();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }
    
    public function providersAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') 
        -> getNavigation('ynmediaimporter_admin_main', array(), 'ynmediaimporter_admin_main_providers');

        $this -> view -> form = $form = new Ynmediaimporter_Form_Admin_Settings_Provider();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
        {
            $values = $form -> getValues();

            foreach ($values as $key => $value)
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice('Your changes have been saved.');
        }
    }
}
