<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Take.php 2010-07-02 19:45 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Take extends Engine_Form
{
  public $_error = array();
  
  public function init()
  {
    $this->setAttrib('name', 'quiz_take');
    
    $module_path = Engine_Api::_()->getModuleBootstrap('quiz')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this->addElement('Hidden', 'quiz_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 901
    ));
  }
}