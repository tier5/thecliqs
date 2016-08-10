<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Purchases.php 5/18/12 2:44 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Panel_PurchaseFilter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag'   => 'div',
                                      'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag'   => 'div',
                                       'class' => 'clear'));


    $this
      ->setAttribs(array(
      'id'    => 'filter_form',
      'class' => 'global_form_box',
    ))
      ->setMethod('GET');

    // Element: query
    $this->addElement('Text', 'ukey', array(
      'label' => 'Order Key',
    ));

    /**
     * @var $orderitemsTable Store_Model_DbTable_Orderitems
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $orderitemsTable = Engine_Api::_()->getDbtable('orders', 'store');
    $multiOptions    = (array)$orderitemsTable->select()
      ->from($orderitemsTable->info('name'), 'status')
      ->where("status NOT IN('initial')")
      ->where('user_id = ?', $viewer->getIdentity())
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    // array_combine() will return false if the array is empty
    if (false === $multiOptions) {
      $multiOptions = array();
    }
    $options = array();
    foreach($multiOptions as $value){
      $options[$value] = ucfirst($value);
    }
    $options = array_merge(array('' => ''), $options);
    $this->addElement('Select', 'status', array(
      'label'        => 'Status',
      'multiOptions' => $options,
    ));

    // Element: order
    $this->addElement('Hidden', 'order', array(
      'value' => 'order_id',
      'order' => 1004
    ));


    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'value' => 'DESC',
      'order' => 1005
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Search',
      'type'  => 'submit',
    ));
  }
}
