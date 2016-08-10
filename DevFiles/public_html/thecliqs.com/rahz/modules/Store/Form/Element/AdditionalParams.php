<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FancyUpload.php 7371 2010-09-14 03:33:35Z john $
 */

/**
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_Form_Element_AdditionalParams extends Zend_Form_Element_Xhtml
{

  public function render(Zend_View_Interface $view = null)
  {
    if (null !== $view) {
        $this->setView($view);
    }

    $content = '';
    foreach ($this->getDecorators() as $decorator) {
          $decorator->setElement($this);
          $content = $decorator->render($content);
    }
    return $content;
  }

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
      $this->addDecorator('FormAdditionalParams');
      Engine_Form::addDefaultDecorators($this);
    }
  }

  /**
   * Override isValid()
   *
   * Ensure that validation error messages mask password value.
   *
   * @param  array $value
   * @param  mixed $context
   * @return bool
   */
  public function isValid($value, $context = null)
  {
    if ( !parent::isValid($value, $context)) {
      return false;
    }

    $values = $this->getValue();

    if (!is_array($values)){
      $this->addError('STORE_Wrong value has been assigned!');
      return false;
    }

    $empty = false;
    foreach($values as $value){
      if (!empty($value['label']) && empty($value['options'])){
        $empty = true;
      }
    }

    if ( $empty ){
      $this->addError( 'STORE_Some params were not set!');
      return false;
    }

    return true;
  }

  /**
   * Set element value
   *
   * @param  mixed $value
   * @return Zend_Form_Element
   */
  public function setValue($value)
  {
    if (!is_array($value)) return false;

    $options = array();

    if (isset($value['options']) && is_array($value['options'])){
      $options = $value['options'];
      unset($value['options']);
    }

    $new_val = array();

    foreach($value as $key=>$val){
      if ( is_string($val) && strlen(trim($val)) >0 ){
        $new_val[$key]['label'] = trim($val);
      }elseif(isset($val['label']) && is_string($val['label']) && strlen(trim($val['label'])) >0){
        $new_val[$key]['label'] = trim($val['label']);
      }

      if (isset($new_val[$key]['label'])){
        if (is_array($val) && isset($val['options'])){
          $new_val[$key]['options'] = $val['options'];
        } else {
          $new_val[$key]['options'] = (isset($options[$key]))?$options[$key]:null;
        }

        if (isset($new_val[$key]['options']) && strlen($new_val[$key]['options']) > 0){
          $tmp = explode(',', $new_val[$key]['options']);
          $opts = array();
          foreach($tmp as $opt){
            $opts[] = trim($opt);
          }

          $new_val[$key]['options'] = implode(',', $opts);
        }
      }
    }

    $this->_value = $new_val;
    return $this;
  }

  /**
   * Retrieve error messages and perform translation and value substitution
   *
   * @return array
   */
  protected function _getErrorMessages()
  {
    $translator = $this->getTranslator();
    $messages   = $this->getErrorMessages();
    $value      = $this->getValue();
    foreach ($messages as $key => $message) {
        if (null !== $translator) {
            $message = $translator->translate($message);
        }
        $messages[$key] = str_replace('%value%', $value, $message);
    }
    return $messages;
  }
}
