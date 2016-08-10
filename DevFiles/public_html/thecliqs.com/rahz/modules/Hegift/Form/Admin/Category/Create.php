<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Create.php 03.02.12 17:29 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Category_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;

    $this
      ->setAttribs(array(
        'id' => 'category_form',
        'class' => 'global_form_box',
      ));

    $this->setTitle('Add Category')
      ->setDescription('Add Category for Gifts');

    $this->addElement('Text', 'title', array(
      'label' => 'Category',
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', true, array(4, 50)),
        array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'hegift_categories', 'title'))
      ),
    ));

    $this->title->getValidator('NotEmpty')->setMessage('Please enter a valid category.', 'isEmpty');
    $this->title->getValidator('Db_NoRecordExists')->setMessage('You has already picked this category, please use another one.', 'recordFound');

    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
        array('HtmlTag2', array('tag' => 'div'))
      )
    ));
  }
}
