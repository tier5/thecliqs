<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 06.08.12 17:34 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Global Settings');

    // Elements
    $this->addElement('Radio', 'sort_by', array(
      'label' => 'CREDIT_Sort Ordering',
      'description' => 'CREDIT_Choose Sort Ordering Description',
      'multiOptions' => array(
        '1' => 'By Current Credits',
        '0' => 'By Earned Credits'
      ),
    ));
    $this->sort_by->getDecorator("Description")->setOption("placement", "prepend");

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
