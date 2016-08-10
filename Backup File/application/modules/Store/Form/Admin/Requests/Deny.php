<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Deny.php 5/14/12 11:19 AM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Requests_Deny extends Engine_Form
{
  /**
   * @var Store_Model_Request
   */
  protected $_request;

  public function setRequest(Store_Model_Request $request)
  {
    $this->_request = $request;
  }

  public function init()
  {
    /**
     * @var $page    Page_Model_Page
     * @var $plugin  Store_Plugin_Gateway_PayPal
     * @var $gateway Experts_Payment_Gateway_PayPal
     */
    $request = $this->_request;
    $page    = $request->getOwner('page');

    $this->setTitle('STORE_Deny Request')
      ->setDescription(Zend_Registry::get('Zend_View')->translate("STORE_Are you sure you want to deny this money request from %1s?", $page->__toString()))
      ->setAttrib('class', 'global_form_popup')
      ->setMethod('POST');
    ;
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('textarea', 'response_message', array(
      'Label' => 'Message',
      'value' => $request->response_message,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label'      => 'STORE_Deny Request',
      'type'       => 'submit',
      'ignore'     => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label'       => 'cancel',
      'link'        => true,
      'prependText' => ' or ',
      'href'        => '',
      'onclick'     => 'parent.Smoothbox.close();',
      'decorators'  => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }
}
