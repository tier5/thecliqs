<?php
/**
 * SocialEngine
 *
 * @category   Experts
 * @package    Experts_Service_Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Credit.php 04.01.12 13:44 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Experts
 * @package    Experts_Service_Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Experts_Service_Credit extends Zend_Service_Abstract
{
  /**
   * Constructor
   *
   * @param array $options
   */
  public function __construct(array $options)
  {
    $this->setOptions($options);
  }

  public function setOptions(array $options)
  {
    foreach( $options as $key => $value ) {
      $property = '_' . $key;
      if( property_exists($this, $property) ) {
        $this->$property = $value;
      }
    }
  }
}