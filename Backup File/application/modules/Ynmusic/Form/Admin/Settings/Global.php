<?php
class Ynmusic_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
    $this
       ->setAttrib('id', 'ynmusic_settings_form')
      ->setTitle('Global Settings');
	
	
    $this->addElement('Integer', 'ynmusic_songsPerPage', array(
      'label' => 'Songs Per Page',
      'description' => 'How many Songs will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_songsPerPage', 10),
	  'validators' => array(
            array('Between',true,array(1,999)),
        ),
	));
	
	$this->addElement('Integer', 'ynmusic_albumsPerPage', array(
      'label' => 'Albums Per Page',
      'description' => 'How many Albums will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_albumsPerPage', 8),
	  'validators' => array(
            array('Between',true,array(1,999)),
        ),
	));
	
	$this->addElement('Integer', 'ynmusic_playlistsPerPage', array(
      'label' => 'Playlists Per Page',
      'description' => 'How many Playlists will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_playlistsPerPage', 8),
	  'validators' => array(
            array('Between',true,array(1,999)),
        ),
	));
	
	$this->addElement('Integer', 'ynmusic_artistsPerPage', array(
      'label' => 'Artists Per Page',
      'description' => 'How many Artists will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_artistsPerPage', 8),
	  'validators' => array(
            array('Between',true,array(1,999)),
        ),
	));
	
	//Add terms and conditions
	$this->addElement('tinyMce','ynmusic_terms',array(
		'label'=>'Terms And Conditions',
		'filters'=>array('StringTrim'),
		'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_terms', ""),
		'allowEmpty' => true,
		'required' => false
	)); 
	
	$settings = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('Radio', 'ynmusic_player_display', array(
      'label' => 'Player display',
      'multiOptions' => array(
        0 => 'Use mini player as default',
        1 => 'Use bottom player. This player will always be at the bottom of the site. Other bars from different plugins will be laid above this player.',
      	2 => 'Use bottom player. This player will override other bottom bars from different plugins.',
 	  ),
      'value' => $settings->getSetting('ynmusic_player_display', 0),
    ));
	
    $this->addElement('Radio', 'ynmusic_player_setting', array(
      'label' => 'Player settings',
      'multiOptions' => array(
        0 => 'Show this player on Social Music plugin. This player will be hidden when browsing on other plugins.',
        1 => 'Show this player on all pages of this site',
      ),
      'value' => $settings->getSetting('ynmusic_player_setting', 0),
    ));
	
	$this -> addElement('dummy', 'placement', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_drag_drop.tpl',
			)
		)), 
	));  
	
	//Client ID
	$this->addElement('Text','ynmusic_sound_clientid',array(
		'label'=>'SoundCloud Client Id',
		'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientid', ""),
		'allowEmpty' => true,
		'required' => false
	)); 
	
	//Client Secret
	$this->addElement('Text','ynmusic_sound_clientsecret',array(
		'label'=>'SoundCloud Client Secret',
		'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientsecret', ""),
		'allowEmpty' => true,
		'required' => false
	)); 
	
	//Add this public id
    $this->addElement('Text', 'ynmusic_addthis_pubid', array(
    	'label' => 'AddThis - Profile ID',
    	'required' => true,
    	'value' => $settings->getSetting('ynmusic_addthis_pubid', 'younet'),
    ));
	
	
    // Add submit button
    $this->addElement('Button', 'submit_btn', array(
      'label' => 'Save Changes',
      'onclick' => "checkColor();",
      'type' => 'button',
      'ignore' => true
    ));
  }

}
