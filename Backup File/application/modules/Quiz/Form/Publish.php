<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Publish.php 2010-07-02 19:44 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Publish extends Engine_Form
{
  public $_error = array();
  
  public function init()
  {
    $this->setTitle('Publish quiz')
      ->setDescription('Quiz Form Description')
      ->setAttrib('name', 'publish_quiz');
    
    $this->addElement('Hidden', 'quiz_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 901
    ));
    
    $this->addElement('Hidden', 'published', array(
      'allowEmpty' => false,
      'required' => true,
      'value' => 0,
      'order' => 902
    ));
    
    $this->addElement('Button', 'publish', array(
      'label' => 'quiz_Publish',
      'type' => 'button',
      'order' => 903
    ));
    
    $this->addElement('Button', 'unpublish', array(
      'label' => 'quiz_UnPublish',
      'type' => 'button',
      'order' => 904
    ));
  }
}