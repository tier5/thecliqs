<?php
class Ynjobposting_Form_Company_Status extends Engine_Form
{
  	protected $_label;
	
	public function getLabel()
	{
	return $this -> _label;
	}
	
	public function setLabel($label)
	{
	$this -> _label = $label;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	$title = $this ->_label ." ". $view -> translate('Company');
	$description = "";
	switch ($this -> _label) {
		case 'Publish':
			$description = $view -> translate('Are you sure that you want to publish this company? It will force the company to be available.');
			break;
		case 'Delete':
			$description = $view -> translate('Are you sure that you want to delete this company? All of the information and the jobs belongs to this company will be erased.');
			break;
		case 'Close':
			$description = $view -> translate('Are you sure that you want to close this company? It will force the company to be hidden.');
			break;	
	}
    $this
      ->setTitle($title)
	  ->setDescription($description)
      ->setAttrib('class', 'global_form_popup')
      ;
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $this ->_label,
      'type' => 'submit',
      'onclick' => 'removeSubmit()',
      'ignore' => true,
      'decorators' => array('ViewHelper')
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
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}

