<?php

class Ynmobile_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $this->addElement('Text', 'ynmobile_google_api', array(
	      'label' => 'Google API key',
	      'value' => $settings->getSetting('ynmobile.google.api', ''),
    ));

    $this->addElement('Text', 'ynmobile_apple_pass', array(
    		'label' => 'iPhone - Apple password phase',
    		'value' => $settings->getSetting('ynmobile.apple.pass', ''),
    ));
    
    $this->addElement('Text', 'ynmobile_apple_cert_filepath', array(
    		'label' => 'iPhone - Apple cert file path',
    		'value' => $settings->getSetting('ynmobile.apple.cert.filepath', ''),
    ));
    
    $this->addElement('Text', 'ynmobile_apple_ipad_pass', array(
    		'label' => 'iPad - Apple password phase',
    		'value' => $settings->getSetting('ynmobile.apple.ipad.pass', ''),
    ));
    
    $this->addElement('Text', 'ynmobile_apple_ipad_cert_filepath', array(
    		'label' => 'iPad - Apple cert file path',
    		'value' => $settings->getSetting('ynmobile.apple.ipad.cert.filepath', ''),
    ));
	
	// Element: view
    $this->addElement('Radio', 'ynmobile_chat', array(
      'label' => 'Allow Chat?',
      'multiOptions' => array(
        'ynchat' => 'YN Chat',
        'cometchat' => 'Comet Chat',
        'chat' => 'Chat',
        '' => 'No Chat',
      ),
      'value' => $settings->getSetting('ynmobile.chat', ''),
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}