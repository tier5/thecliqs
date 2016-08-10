<?php
class Socialbridge_Form_Admin_Lisettings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Linkedin Api Settings')
     ->setDescription('SOCIALBRIDGE_ADMIN_SETTINGS_LINKEDIN_DESCRIPTION');
     
	$description = $this->getTranslator()->translate('SOCIALBRIDGE_ADMIN_SETTINGS_LINKEDIN_DESCRIPTION');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $description = vsprintf($description, array(
      'https://www.linkedin.com/secure/developer',
    ));
	$this->setDescription($description);


    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	 
    $this->addElement('Text', 'key', array(
          'label' => 'Linkedin Key',
          'size'=>80,
          'style'=>'width:400px'
    ));
     $this->addElement('Text', 'secret', array(
          'label' => 'Linkedin Secret',
          'size'=>80,
          'style'=>'width:400px'
    ));
	
    // Add submit button
    $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
    ));
  }
}