<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EditResult.php 2010-07-02 19:45 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_EditResult extends Quiz_Form_CreateResult
{
  public $_error = array();
  
  public function init()
  {    
    parent::init();
    
    $this->addElement('Hidden', 'result_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 901
    ));
  }
}