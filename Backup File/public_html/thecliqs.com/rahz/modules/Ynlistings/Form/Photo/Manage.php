<?php
class Ynlistings_Form_Photo_Manage extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $translate = Zend_Registry::get('Zend_Translate');
	
	$this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'button',
      'onclick' => 'this.form.submit(); removeSubmit()',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
	 // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'ynlistings_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
};
