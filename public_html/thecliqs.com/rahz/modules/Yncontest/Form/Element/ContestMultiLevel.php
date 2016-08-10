<?php

class Yncontest_Form_Element_ContestMultiLevel extends Zend_Form_Element_Text
{
	
  public $helper = 'formContestMultiLevel';
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
}
