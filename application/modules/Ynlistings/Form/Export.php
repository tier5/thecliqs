<?php
class Ynlistings_Form_Export extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
	$id = $user -> level_id;
    
    // Init form
    $this
      ->setTitle('Export Listings')
      ->setAttrib('name', 'listings_export')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
	
	// Init path
    $this->addElement('Select', 'type_export', array(
      'label' => 'Which type of file do you want to export?',
      'multiOptions' => array('xls' => 'XLS','csv'=>'CSV')
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Export Listings',
      'type'  => 'submit',
      'onclick' => 'submitExport()',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	$this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
