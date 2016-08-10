<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Duplicate.php 07.02.12 11:51 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Duplicate extends Engine_Form
{
  public function init()
  {
    $this->setAttribs(
      array(
        'class' => 'global_form_box hegift_duplicate_gift_form'
      )
    );

    $this->setTitle('Duplicate Gift')
      ->setDescription('HEGIFT_DUPLICATE_GIFT_DESC');

    $this->addElement('Text', 'title', array(
      'label' => 'Gift Title',
      'disabled' => true
    ));

    $this->addElement('Text', 'credits', array(
      'label' => 'Credit',
      'disabled' => true
    ));

    $this->addElement('Text', 'amount', array(
      'label' => 'Amount',
      'disabled' => true
    ));

    $this->addElement('File', 'photo', array(
      'label' => 'Photo',
      'description' => 'HEGIFT_Photo is required for success creation of this gift',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('Count', false, 1),
        array('Extension', false, 'jpg,jpeg,png,gif,bmp'),
      )
    ))->getElement('photo')->getDecorator("Description")->setOption("placement", "append");

    /**
     * @var $table Hegift_Model_DbTable_Categories
     */
    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $categories = $table->getCategoriesArray();

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => $categories,
      'disabled' => true
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Create Gift',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'hegift'), 'admin_default', true),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'execute',
        'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}
