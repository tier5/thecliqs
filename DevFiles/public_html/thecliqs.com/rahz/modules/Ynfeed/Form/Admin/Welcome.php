<?php
class Ynfeed_Form_Admin_Welcome extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Add New Welcome Content')
      ->setDescription('Please compose your new welcome content below.')
      ->setAttrib('id', 'welcome_create');     
    
	// Add title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
    ));
    
	// Add display limitation
    $this->addElement('Radio', 'display_limit', array(
      'label' => 'Display Limitation',
      'multiOptions' => array(
	  		0 => 'None, Always show this block to users in the Welcome Tab',
	  		1 => 'Number of days since signup. (Below you will be able to enter the value)',
	  		2 => 'Number of friends. (Below you will be able to enter the value)'
			),
      'description' => 'Limitation for the custom block view',
      'required' => false,
      'allowEmpty' => true,
      'value' => 2
    ));
	
	// Add number of limitation
    $this->addElement('Integer', 'number_of_limit', array(
      'label' => 'Number of Limitation',
      'required' => false,
      'allowEmpty' => true,
      'value' => 5
    ));
	
    $this->addElement('TinyMce', 'body', array(
      'label' => 'Content',
      'required' => true,
      'editorOptions' => array(
        'bbcode' => true,
        'html' => true,
      ),
      'allowEmpty' => false,        
    ));
	
	// Prepare Member levels
    $levelOptions = array();
    foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level ) 
    {
    	if($level -> type != 'public')
      		$levelOptions[$level->level_id] = $level->getTitle();
    }
    // Select Member Levels
    $this->addElement('multiselect', 'member_levels', array(
      'label' => 'Member Levels',
      'multiOptions' => $levelOptions,
      'description' => 'Specify which member levels will be shown this custom block. To show this block to all member levels, leave them all selected. Use CTRL-click to select or deselect multiple levels.',
      'required' => false,
      'allowEmpty' => true,
    ));
	
    // Prepare Network options
    $networkOptions = array();
    foreach( Engine_Api::_()->getDbtable('networks', 'network')->fetchAll() as $network) {
      $networkOptions[$network->network_id] = $network->getTitle();
    }   
    
    // Select Networks
    $this->addElement('multiselect', 'networks', array(
      'label' => 'Networks',
      'multiOptions' => $networkOptions,
      'description' => 'Specify which networks will be shown this custom block. To show this block to all networks, leave them all selected. Use CTRL-click to select or deselect multiple networks. To show this block to all users, remove them all selected.',
      'required' => false,
      'allowEmpty' => true,
    ));   
    
	// Add enable contactimporter
    $this->addElement('Radio', 'enabled_contact', array(
      'label' => 'Enable Contact Importer Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Contact Importer Block',
	  		0 => 'No, disable Contact Importer Block'
			),
      'required' => false,
      'allowEmpty' => true,
      'value' => 1
    ));
	
	// Add enable friend requests
    $this->addElement('Radio', 'enabled_friend', array(
      'label' => 'Enable Friend Requests Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Friend Requests Block',
	  		0 => 'No, disable Friend Requests Block'
			),
      'required' => false,
      'allowEmpty' => true,
       'value' => 1
    ));
	
	// Add number of items
    $this->addElement('Integer', 'number_of_friend', array(
      'label' => 'Number of friends will be shown',
      'required' => false,
      'allowEmpty' => true,
      'value' => 4
    ));
    
	// Add enable search friend
    $this->addElement('Radio', 'enabled_search_fr', array(
      'label' => 'Enable Search Friends Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Search Friends Block',
	  		0 => 'No, disable Search Friends Block'
			),
      'required' => false,
      'allowEmpty' => true,
       'value' => 1
    ));
	
	// Add enable member suggestion
    $this->addElement('Radio', 'enabled_member_sug', array(
      'label' => 'Enable Member Suggestion Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Member Suggestion Block',
	  		0 => 'No, disable Member Suggestion Block'
			),
      'required' => false,
      'allowEmpty' => true,
       'value' => 1
    ));
	
	// Add number of items
    $this->addElement('Integer', 'number_of_member', array(
      'label' => 'Number of members will be shown',
      'required' => false,
      'allowEmpty' => true,
      'value' => 4
    ));
	
	// Add enable group suggestion
    $this->addElement('Radio', 'enabled_group_sug', array(
      'label' => 'Enable Group Suggestion Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Group Suggestion Block',
	  		0 => 'No, disable Group Suggestion Block'
			),
      'required' => false,
      'allowEmpty' => true,
       'value' => 1
    ));
	
	// Add number of items
    $this->addElement('Integer', 'number_of_group', array(
      'label' => 'Number of groups will be shown',
      'required' => false,
      'allowEmpty' => true,
      'value' => 4
    ));
	
	// Add enable event suggestion
    $this->addElement('Radio', 'enabled_event_sug', array(
      'label' => 'Enable Event Suggestion Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Event Suggestion Block',
	  		0 => 'No, disable Group Suggestion Block'
			),
      'required' => false,
      'allowEmpty' => true,
       'value' => 1
    ));
	
	// Add number of items
    $this->addElement('Integer', 'number_of_event', array(
      'label' => 'Number of events will be shown',
      'required' => false,
      'allowEmpty' => true,
      'value' => 6
    ));
	
	// Add enable most like
    $this->addElement('Radio', 'enabled_most_like', array(
      'label' => 'Enable Most Liked Items Block',
      'multiOptions' => array(
	  		1 => 'Yes, enable Most Liked Items Block',
	  		0 => 'No, disable Most Liked Items Block'
			),
      'required' => false,
      'allowEmpty' => true,
       'value' => 1
    ));
	
	// Add number of items
    $this->addElement('Integer', 'number_of_like', array(
      'label' => 'Number of items will be shown',
      'required' => false,
      'allowEmpty' => true,
      'value' => 4
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Content',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'ynfeed', 'controller' => 'welcome', 'action' => 'index'), 'admin_default', true),
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}