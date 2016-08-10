<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: ChangeStatus.php 5/14/12 5:21 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Statistics_ChangeStatus extends Engine_Form
{
  /**
   * @var $_item Store_Model_Orderitem
   */
  protected $_item;

  public function setItem( Store_Model_Orderitem $item)
  {
    $this->_item = $item;
  }

  public function init()
  {
    $this->setTitle('Change Status')
      ->setDescription('Are you sure you want to change status of this item?')
      ->setAttrib('class', 'global_form_popup')
      ;
    $this->loadDefaultDecorators();


    $this->addElement('hidden', 'orderitem_id', array(
      'value' => $this->_item->getIdentity(),
      'required' => true,
    ));

    if($this->_item->isItemDigital()){
      $options = array(
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
      );
    } else {
      $options = array(
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
      );
    }

    $this->addElement('select', 'status', array(
      'Label' => 'Change to',
      'required'=>true,
      'multiOptions' => $options,
      'validators' => array(
        new Zend_Validate_InArray(array('completed', 'delivered', 'cancelled'))
      )
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}
