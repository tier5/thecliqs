<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Edit.php 06.02.12 14:31 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Edit extends Engine_Form
{
  public function init()
  {
    $this->setAttribs(
      array(
        'class' => 'global_form_box hegift_edit_gift_form'
      )
    );

    $this->setTitle('Edit Gift')
      ->setDescription('HEGIFT_EDIT_GIFT_DESC');

    $this->addElement('Text', 'title', array(
      'label' => 'Gift Title',
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Text', 'credits', array(
      'label' => 'Credit',
      'description' => 'HEGIFT_Gift will be free if you types zero',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(-1)),
      ),
      'value' => 0
    ))->getElement('credits')->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Select', 'limit', array(
      'label' => 'Gift Limit',
      'description' => 'HEGIFT_Choose what kind of gift want to create: limitly or ...?',
      'multiOptions' => array(
        0 => 'HEGIFT_unlimit',
        1 => 'HEGIFT_limit'
      ),
      'onchange' => "updateAmountField()",
      'value' => 0
    ));

    $this->addElement('Text', 'amount', array(
      'description' => 'HEGIFT_Maximum amount is 100 and minimum is 1, you can leave empty',
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
        array('LessThan', true, 101)
      )
    ))->getElement('amount')->getDecorator("Description")->setOption("placement", "append");

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(true);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(true);
    $this->addElement($end);

    /**
     * @var $table Hegift_Model_DbTable_Categories
     */
    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $categories = $table->getCategoriesArray();

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => $categories,
      'allowEmpty' => false,
      'required' => true,
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
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
