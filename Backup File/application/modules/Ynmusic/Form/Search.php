<?php
class Ynmusic_Form_Search extends Engine_Form {
	
  	public function init() {
		$view = Zend_Registry::get('Zend_View');
		$this->setAttribs(array( 'id' => 'filter_form',
          	'class' => 'global_form_box',
           'method' => 'GET',
    	));
					
	$this -> addElement('Text', 'keyword', array(
		'label' => 'Search Music',
	));
	$this -> addElement('Select', 'type', array ( 
		'label' => 'Type',
		'multiOptions' => array(
			'all' => 'All',
			'song' => 'Song',
			'album' => 'Album',
			'playlist' => 'Playlist',
			'artist' => 'Artist'
		),
		'value' => 'all'
	));
	
	$this -> addElement('Text', 'owner', array(
		'label' => 'Post by'
	));
	
	$this -> addElement('Text', 'genre', array(
		'label' => 'Genre'
	));
	
	$this -> addElement('Select', 'browse_by', array(
		'label' => 'Browse By',
		'multiOptions' => array(
			'recently_created' => 'Recently Created',
			'most_liked' => 'Most Liked',
			'most_viewed' => 'Most Viewed',
			'most_played' => 'Most Played',
			'a_z' => 'A - Z',
			'z_a' => 'Z - A'
		),
		'value' => 'recently_created'
	));
	
    // Created from
    $created_from = new Ynmusic_Form_YnCalendarSimple('created_from');
    $created_from -> setLabel("Created from");
    $created_from -> setAllowEmpty(true);
    $this -> addElement($created_from);
	
	// Created to
    $created_to = new Ynmusic_Form_YnCalendarSimple('created_to');
    $created_to -> setLabel("Created to");
    $created_to -> setAllowEmpty(true);
    $this -> addElement($created_to);
          
    // Buttons
    $this->addElement('Button', 'submit_button', array(
	      'value' => 'submit_button',
	      'label' => 'Search',
	      'type' => 'submit',
	      'ignore' => true,
    ));
	
  }
}
