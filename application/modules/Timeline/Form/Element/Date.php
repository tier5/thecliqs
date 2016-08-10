<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Date.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Timeline_Form_Element_Date extends Engine_Form_Element_Date
{
  public function isValid($value, $context = null)
  {
    if ((empty($value['day']) || empty($value['month'])) && $this->isRequired()) {
      $this->_messages[] = "Timeline must include a month and a date.";
      return false;
    }
    return parent::isValid($value, $context);
  }

  public function getYearMax()
  {
    // Default is this year
    if (is_null($this->_yearMax)) {
      $date = new Zend_Date();
      $this->_yearMax = (int)$date->get(Zend_Date::YEAR);
    }
    return $this->_yearMax;
  }
}