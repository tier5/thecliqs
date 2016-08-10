<?php

class Yncontest_Form_Element_MaxEntries extends Zend_Form_Element_Text
{
	
  public $helper = 'formMaxEntries';
  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators()
  {
  	
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
    }
  }
  
  //public function setValue($value)
}
