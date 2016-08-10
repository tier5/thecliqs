<?php
class Ynresume_AdminColorController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_color');
        
        $theme = $this->_getParam('theme', 'theme_1');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $params = array();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
        }
        $this->view->form = $form = new Ynresume_Form_Admin_ColorSettings(array('theme' => $theme, 'params' => $params));
         
        if ($this->getRequest()->isPost()) {
            $headings = Engine_Api::_()->getApi('fields', 'ynresume')->getHeading();
        
            $sections = Engine_Api::_()->ynresume()->getAllSections();
            if (isset($sections['photo'])) unset($sections['photo']);
            
            foreach ($headings as $heading) {
                $sections['field_'.$heading->field_id] = $heading->label;
            }
            
            foreach ($sections as $key => $value) {
                $id = $theme.'_'.$key.'_color';
                $settings->setSetting('ynresume_'.$id, $params[$id]);
            }
            
            $form->addNotice('Your changes have been saved.'); 
        }
    }
}