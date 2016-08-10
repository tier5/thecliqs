<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Category.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Admin_Category extends Engine_Form
{
  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box');

    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('quiz_Category Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');

    $id = new Zend_Form_Element_Hidden('id');

    $this->addElements(array(
      $label,
      $id
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'quiz_Add Category',
      'type' => 'submit',
      'ignore' => true
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField(Quiz_Model_Category $category)
  {
    $this->label->setValue($category->category_name);
    $this->id->setValue($category->category_id);
    $this->submit->setLabel('quiz_Edit Category');
  }
}