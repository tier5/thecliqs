<?php
class Ynlistings_Form_Topic_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
      ))
       ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));

    $this->addElement('Hidden','page');
    $this->addElement('Text', 'search', array(
      'label' => 'Search Topics',
      'alt' => 'Search topics',
    ));

    $this->addElement('Select', 'closed', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => 'All Topics',
        '0' => 'Only Open Topics',
        '1' => 'Only Closed Topics',
      ),
    ));

    $this->addElement('Select', 'view', array(
      'label' => 'View',
      'multiOptions' => array(
        '0' => 'All Members Topics',
        '1' => 'Only My Topics',
      ),
    ));
    
    $this->addElement('Select', 'order', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'recent' => 'Latest',
        'last_reply' => 'Latest Replied',
        'view' => 'Most Viewed',
        'reply' => 'Most Replied',
        'no_reply' => 'No Replied',
    ),
    ));

     // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
    ));
  }
}