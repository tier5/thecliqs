<?php

class Ynfbpp_AdminSettingsController extends Core_Controller_Action_Admin
{
    
    public function indexAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') 
        -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_settings');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Global();

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
    
    public function userAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') 
        -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_user');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_User();

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
    
    public function groupAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_group');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Group();

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
    
    public function eventAction()
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_event');

        $this -> view -> form = $form = new Ynfbpp_Form_Admin_Settings_Event();

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
