<?php
class Ynresume_Form_Admin_ColorSettings extends Engine_Form {
    protected $_theme = 'theme_1';
    protected $_params = array();
    
    public function getTheme() {
        return $this -> _theme;
    }
    
    public function setTheme($theme) {
        $this -> _theme = $theme;
    }
    
    public function getParams() {
        return $this -> _params;
    }
    
    public function setParams($params) {
        $this -> _params = $params;
    }
    
    public function init() {
        $this->setTitle('Color Settings'); 
        $params = $this->getParams(); 
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Select', 'theme', array(
            'label' => 'Resume Theme',
            'multiOptions' => array(
                'theme_1' => 'Theme 1',
                'theme_2' => 'Theme 2',
                'theme_3' => 'Theme 3',
                'theme_4' => 'Theme 4',
            ),
            'ignore' => true,
            'value' => $this->getTheme()
        ));
        
        $this->addElement('Image', 'icon', array(
            'src' => Engine_Api::_()->ynresume()->getThemeIconLink($this->getTheme()),
            'onclick' => 'event.preventDefault();',
            'ignore' => true,
        ));
        
        $headings = Engine_Api::_()->getApi('fields', 'ynresume')->getHeading();
        
        $sections = Engine_Api::_()->ynresume()->getAllSections();
        if (isset($sections['photo'])) unset($sections['photo']);
        
        foreach ($headings as $heading) {
            $sections['field_'.$heading->field_id] = $heading->label;
        }
        
        foreach ($sections as $key => $value) {
            $id = $this->getTheme().'_'.$key.'_color';
            $color = $settings->getSetting('ynresume_'.$id, Engine_Api::_()->ynresume()->getDefaultThemeColor($this->getTheme()));
            if (isset($params[$id])) {
                $color = $params[$id];
            }
            $this->addElement('Heading', 'ynresume_'.$id, array(
                'label' => $value.' Section',
                'value' => '<input value="'.$color.'" type="color" id="'.$id.'" name="'.$id.'"/>'
            ));
        }
        
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));        
    }
}