<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EditQuestion.php 2010-07-02 19:46 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_EditQuestion extends Quiz_Form_CreateQuestion
{
  public $_error = array();
  
  public function init()
  {    
    parent::init();
    
    $this->addElement('Hidden', 'question_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 902
    ));
  }
}