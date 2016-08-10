<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Form_Admin_Transaction_Filter extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
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

    // Element: member
    $member = new Zend_Form_Element_Text('member');
    $member
      ->setLabel('Member')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag'       => null,
                                    'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $ukey = new Zend_Form_Element_Text('ukey');
    $ukey
      ->setLabel('Order Key')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag'       => null,
                                    'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));


    /**
     * Element: item_type
     *
     * @var $orderitemsTable Store_Model_DbTable_Orderitems
     */
    $orderitemsTable = Engine_Api::_()->getDbtable('transactions', 'store');
    $itemTypeOptions    = (array)$orderitemsTable->select()
      ->from($orderitemsTable->info('name'), 'item_type')
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (!empty($itemTypeOptions)) {
      $itemTypeOptions = array_combine(
        array_values($itemTypeOptions),
        array_map('ucfirst', array_map('str_replace_map', array_values($itemTypeOptions)))
      );
      // array_combine() will return false if the array is empty
      if (false === $itemTypeOptions) {
        $itemTypeOptions = array();
      }
    }
    $itemTypeOptions = array_merge(array('' => ''), $itemTypeOptions);

    $item_type = new Zend_Form_Element_Select('item_type');
    $item_type
      ->setMultiOptions($itemTypeOptions)
      ->setLabel('Item Type')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag'       => null,
                                    'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    // Element: gateway
    $gatewayOptions = array(0=> '');
    foreach (Engine_Api::_()->getDbTable('gateways', 'store')->getEnabledGateways() as $g) {
      $gatewayOptions[$g->getIdentity()] = $g->getTitle();
    }
    $gateway = new Zend_Form_Element_Select('gateway_id');
    $gateway
      ->setMultiOptions($gatewayOptions)
      ->setLabel('Gateway')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag'       => null,
                                    'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    //Element: state
    $stateOptions    = (array)$orderitemsTable->select()
      ->from($orderitemsTable->info('name'), 'state')
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (!empty($stateOptions)) {
      $stateOptions = array_combine(
        array_values($stateOptions),
        array_map('ucfirst', array_values($stateOptions))
      );
      // array_combine() will return false if the array is empty
      if (false === $stateOptions) {
        $stateOptions = array();
      }
    }
    $stateOptions = array_merge(array('' => ''), $stateOptions);

    $state = new Zend_Form_Element_Select('state');
    $state
      ->setMultiOptions($stateOptions)
      ->setLabel('State')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag'       => null,
                                    'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    // Element: execute
    $submit = new Zend_Form_Element_Button('execute', array('type' => 'submit',
                                                            'style'=> 'padding:2px;'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag'   => 'div',
                                      'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    // Element: order
    $this->addElement('Hidden', 'order', array(
      'value' => 't.timestamp',
      'order' => 10004,
    ));


    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'value' => 'DESC',
      'order' => 10005,
    ));

    $this->addElements(array(
      $member,
      $ukey,
      $item_type,
      $gateway,
      $state,
      $submit,
    ));
  }
}

function str_replace_map($value) {
  return str_replace('_', ' ', $value);
}