<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Logodatepicker.php 2012-08-16 16:35 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Element_Logodatepicker extends Zend_Form_Element_Xhtml
{
  public $helper = 'logodatepicker';


  /**
   * @return void
   */

  public function loadDefaultDecorators()
  {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }
    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
    }
  }

  public function setValue($value)
  {
    $value = $this->_prepare_date($value);
    return parent::setValue($value);
  }

  public function isValid($value, $context = null)
  {
    $this->setValue($this->_prepare_date($value));
    if (!$value && !$this->getAllowEmpty()) {
      $this->addError('Please select a date from the calendar.');
      return false;
    }
    return true;

  }

  protected function _prepare_date($date)
  {
    $result = preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $date, $matches);

    if (!$result) {
      $result = preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/', $date, $matches);
    }

    if ($result) {
      $year = $matches[1];
      $month = $matches[2];
      $day = $matches[3];
      $hour = $matches[4];
      $minute = $matches[5];
      $second = (isset($matches[6])) ? $matches[6] : 0;

      if (
        ($month > 0 || $month <= 12) ||
        ($day > 0 || $day <= 31) ||
        ($hour > 0 || $hour <= 24) ||
        ($minute > 0 || $minute <= 59) ||
        ($second > 0 || $second <= 59)
      ) {
        if ($second == 0) {
          $second = '00';
        }
        return "$year-$month-$day $hour:$minute:$second";
      }
    }

    return '';

  }

}