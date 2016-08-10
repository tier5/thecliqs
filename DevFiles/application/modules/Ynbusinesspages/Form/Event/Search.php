<?php
class Ynbusinesspages_Form_Event_Search extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get("Zend_Translate");
    $this
      ->setMethod('get')
      ->setAttrib('class', 'global_form f1')
			 ->setAttrib('id', 'filter_form')
      ;

    $this->addElement('Text', 'text', array(
      'label' => 'Search:',
      'alt' => $translate->translate('Search events')
    ));

	$this -> addElement('Select', 'order', array(
		'label' => 'List By:',
		'multiOptions' => array(
			'starttime ASC' => 'Start Time',
			'creation_date DESC' => 'Recently Created',
			'member_count DESC' => 'Most Popular',
		),

		'value' => 'creation_date DESC',
	));
	
		 // Buttons
    $this->addElement('Button', 'search', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}