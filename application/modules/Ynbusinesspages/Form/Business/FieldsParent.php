<?php
class Ynbusinesspages_Form_Business_FieldsParent extends Fields_Form_Standard
{
  protected $_fieldType = 'ynbusinesspages_business';

  public $_error = array();

  protected $_name = 'fieldsParent';

  protected $_elementsBelongTo = 'fieldsParent';

  public function init()
  {
    // custom classified fields
    if( !$this->_item ) {
      $business = new Ynbusinesspages_Model_Business(array());
      $this->setItem($business);
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