<?php
class Ynjobposting_Form_Company_Fields extends Fields_Form_Standard
{
  protected $_fieldType = 'ynjobposting_company';

  public $_error = array();
  
  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';
	
	
  public function init()
  {
    // custom classified fields
    if( empty($this->_item )) {
      $company = new Ynjobposting_Model_Company(array());
      $this->setItem($company);
    }
	
    parent::init();
    $this->removeElement('submit');
  }

  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ;
    }
  }
}