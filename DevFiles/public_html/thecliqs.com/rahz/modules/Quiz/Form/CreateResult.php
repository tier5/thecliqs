<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CreateResult.php 2010-07-02 19:47 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_CreateResult extends Engine_Form
{
  public $_error = array();
  
  public function init()
  {
    $module_path = Engine_Api::_()->getModuleBootstrap('quiz')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this->setTitle('Create quiz result')
      ->setDescription('quiz_Create Result Form Description')
      ->setAttrib('name', 'quiz_create_result');
      
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;
    
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'quiz', 'auth_html');
    
    $this->addElement('Hidden', 'quiz_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 900
    ));
    
    $this->addElement('Text', 'title', array(
      'label' => 'quiz_Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
      new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '100'))
      )));

    $this->addElement('TinyMce', 'description', array(
      'disableLoadDefaultDecorators' => true,
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags' => $allowed_html))),
    ));

    $translate = Zend_Registry::get('Zend_Translate');
    $this->description->addDecorator('QuizDescription', array('label' => $translate->_('quiz_Result Description')));
    
    $this->addElement('File', 'photo', array(
      'label' => 'quiz_Upload a Picture',
      'class' => 'resultPhoto',
      'description' => 'This is very important! It will make your quiz more popular!',
      'validators' => array(
        array('Extension', false, 'jpg,jpeg,png,gif')
      ),
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Quiz Result',
      'type' => 'submit',
    ));
  }
}