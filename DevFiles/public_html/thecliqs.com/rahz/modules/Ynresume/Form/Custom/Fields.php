<?php
class Ynresume_Form_Custom_Fields extends Ynresume_Form_Custom_Standard
{
  protected $_fieldType = 'ynresume_resume';

  public $_error = array();
  
  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';
	
	
  public function init()
  {
    // custom classified fields
    if( empty($this->_item )) {
      $idea = new Ynresume_Model_Resume(array());
      $this->setItem($idea);
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