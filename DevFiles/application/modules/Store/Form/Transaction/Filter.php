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
class Store_Form_Transaction_Filter extends Engine_Form
{
  /**
   * @var $_page Page_Model_Page
   */
  protected $_page;

  public function setPage(Page_Model_Page $page)
  {
    $this->_page = $page;
  }

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
    $this->addElement('Text', 'name', array(
      'label' => 'Product',
    ));

    $this->addElement('Text', 'member', array(
      'label' => 'Member',
    ));

    /**
     * @var $orderitemsTable Store_Model_DbTable_Orderitems
     */
    $orderitemsTable = Engine_Api::_()->getDbtable('orderitems', 'store');
    $multiOptions    = (array)$orderitemsTable->select()
      ->from($orderitemsTable->info('name'), 'status')
      ->where("status IN('completed','shipping', 'delivered')")
      ->where('page_id = ?', $this->_page->getIdentity())
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    // array_combine() will return false if the array is empty
    if (false === $multiOptions) {
      $multiOptions = array();
    }
    $multiOptions = array_merge(array('' => ''), $multiOptions);
    $this->addElement('Select', 'status', array(
      'label'        => 'Status',
      'multiOptions' => $multiOptions,
    ));

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

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Search',
      'type'  => 'submit',
      'style' => 'padding: 2px'
    ));
  }
}