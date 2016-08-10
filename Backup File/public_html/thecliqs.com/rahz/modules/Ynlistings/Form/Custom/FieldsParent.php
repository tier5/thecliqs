<?php
class Ynlistings_Form_Custom_FieldsParent extends Fields_Form_Standard
{
  protected $_fieldType = 'ynlistings_listing';

  public $_error = array();

  protected $_name = 'fieldsParent';

  protected $_elementsBelongTo = 'fieldsParent';

  public function init()
  {
    // custom classified fields
    if( !$this->_item ) {
      $listing = new Ynlistings_Model_Listing(array());
      $this->setItem($listing);
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