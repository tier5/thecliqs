<?php
class Url_Validator extends Zend_Validate_Abstract
{
	const INVALID_URL = 'invalidUrl';

	protected $_messageTemplates = array(self::INVALID_URL => "'%value%' is not a valid URL.", );

	public function isValid($value)
	{
		$valueString = (string)$value;
		$this -> _setValue($valueString);

		if (!Zend_Uri::check($value))
		{
			$this -> _error(self::INVALID_URL);
			return false;
		}
		return true;
	}

}
class Yncontest_Form_Photo_Manage extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Manage Photos')
      ->setAttribs(array(
      'style' => 'width: 700px'))
      ;
  	$this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));
	
   	$this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'yncontest_mycontest', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
	 $this->addDisplayGroup(array(
      'submit',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}
