<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: PayPal.php 5/10/12 3:35 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Requests_Response extends Engine_Form
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

    $this
      ->setTitle('Send Money')
      ->setDescription('STORE_Send the requested money to the account provided by store')
      ->loadDefaultDecorators();

    $this->addElement('text', 'amount', array(
      'Label'    => 'Amount',
      'disabled' => true,
      'value'    => $this->getView()->toCurrency($request->amt)
    ));

    $this->addElement('textarea', 'response_message', array(
      'Label' => 'Message',
      'value' => $request->response_message,
    ));
    $this->addElement('hidden', 'request_id', array(
      'value' => $request->getIdentity(),
    ));
    $this->addElement('hidden', 'gateway_id');

    /**
     * Buttons
     * @var $table   Store_Model_DbTable_Apis
     * @var $api Store_Model_Api
     */
    $table = Engine_Api::_()->getDbTable('apis', 'store');
    $buttons = array();
    foreach ($table->getEnabledGateways($page->getIdentity()) as $api) {
      $buttons[] = 'submit_' . $api->gateway_id;
      $this->addElement('Button', 'submit_' . $api->gateway_id, array(
        'label'      => $this->getView()->translate('Send with %1s', $api->getTitle()),
        'type'       => 'submit',
        'onclick' => "$('gateway_id').set('value', " . $api->gateway_id. "); return true",
        'ignore'     => true
      ));
    }

    $this->addElement('Cancel', 'cancel', array(
      'label'       => 'deny',
      'link'        => true,
      'prependText' => ' or ',
      'href'        => $this->getView()->url(array('action'=> 'deny')),
      'class'       => 'smoothbox',
      'decorators'  => array(
        'ViewHelper'
      )
    ));
//    $this->addDisplayGroup(array_merge($buttons, array('cancel')), 'buttons');
//    $button_group = $this->getDisplayGroup('buttons');
  }
}
