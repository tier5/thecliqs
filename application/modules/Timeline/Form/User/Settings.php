<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Timeline_Form_User_Settings extends Engine_Form
{
  protected $_item;

  public function setItem(User_Model_User $item)
  {
    $this->_item = $item;
  }

  public function getItem()
  {
    if( null === $this->_item ) {
      throw new User_Model_Exception('No item set in ' . get_class($this));
    }

    return $this->_item;
  }

  public function init()
  {
    $this->setTitle('Timeline Settings');


    $this->addElement('Select', 'usage', array(
      'label' => 'TIMELINE_Replace default profile?',
      'description' => 'TIMELINE_USER_SETTING_USAGE_ELEMENT_DESCRIPTION',
      'multiOptions' => array(
        0=>'TIMELINE_No, use default profile page',
        1=>'TIMELINE_Yes, replace',
      ),
    ));


    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}