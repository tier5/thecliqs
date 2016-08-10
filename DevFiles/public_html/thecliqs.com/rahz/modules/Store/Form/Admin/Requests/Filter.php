<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: FilterRequest.php 5/15/12 5:36 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Requests_Filter extends Engine_Form
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
    $store = new Zend_Form_Element_Text('store');
    $store
      ->setLabel('Store')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag'       => null,
                                    'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));


    /**
     * Element: status
     *
     * @var $table Store_Model_DbTable_Requests
     */
    $table = Engine_Api::_()->getDbtable('requests', 'store');
    $options    = (array)$table->select()
      ->from($table->info('name'), 'status')
      ->where('status !=? ', 'cancelled')
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (!empty($options)) {
      $options = array_combine(
        array_values($options),
        array_map('ucfirst', array_map('str_replace_map', array_values($options)))
      );
      // array_combine() will return false if the array is empty
      if (false === $options) {
        $options = array();
      }
    }
    $options = array_merge(array('' => ''), $options);
    $status = new Zend_Form_Element_Select('status');
    $status
      ->setMultiOptions($options)
      ->setLabel('Status')
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
      $store,
      $status,
      $submit,
    ));
  }
}

function str_replace_map($value) {
  return str_replace('_', ' ', $value);
}
