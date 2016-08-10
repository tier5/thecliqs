<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photos.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Page_Products_Photos extends Engine_Form
{
  public function init()
  {
    $this
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Radio', 'cover', array(
      'label' => 'Main photo',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}