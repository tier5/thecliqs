<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: SettingsController.php 4/12/12 1:11 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_SettingsController extends Store_Controller_Action_User
{
  public function init()
  {
    /**
     * @var $page Page_Model_Page
     */
    if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
      Engine_Api::_()->core()->setSubject($page);
    }

    // Set up requires
    $this->_helper->requireSubject('page')->isValid();

    $this->view->page = $page = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //he@todo check admin settings
    if (
      !$page->isAllowStore() ||
      !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }

    /**
     * @var $api Store_Api_Page
     */
    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page, 'settings');
  }

  public function gatewayAction()
  {
    /**
     * @var $table    Payment_Model_DbTable_Gateways
     * @var $paypal   Payment_Model_Gateway
     * @var $settings Core_Api_Settings
     * @var $api      Store_Model_Api
     */
    // Make paginator
    $select = Engine_Api::_()->getDbtable('gateways', 'store')->select()
      ->where('`plugin` != ?', 'Store_Plugin_Gateway_Testing')
      ->where('`plugin` != ?', 'Store_Plugin_Gateway_Credit');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->page_api = Engine_Api::_()->getApi('page', 'store');
  }

  public function gatewayEditAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->core()->getSubject('page');
    $gateway_id = $this->_getParam('gateway_id', false);

    if (!$gateway_id) {
      $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
    }
    /**
     * @var $api Store_Model_Api;
     */
    if (null == ($api = Engine_Api::_()->getDbTable('apis', 'store')->getApi($page->getIdentity(), $gateway_id))) {
      $api = Engine_Api::_()->getDbTable('apis', 'store')->createRow(array(
        'page_id' => $page->getIdentity(),
        'gateway_id' => $gateway_id,
      ));
      $api->save();
    }

    $mode = Engine_Api::_()->store()->getPaymentMode();
    $G2CO = Engine_Api::_()->getDbTable('gateways', 'store')->fetchRow(array('title = ?' => '2Checkout'));

    if ($mode == 'client_store' && $api && $G2CO && $api->gateway_id == $G2CO->gateway_id) {
      $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
    }

    $plugin = $api->getPlugin();
    /**
     * @var $form Engine_Form;
     */
    $this->view->form = $form = $plugin->getAdminGatewayForm(array('isTestMode' => $api->test_mode));
    $form->cancel->href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit',
      'page_id' => $page->getIdentity()), 'page_team');

    // Populate form
    $form->populate($api->toArray());
    if (is_array($api->config)) {
      $form->populate($api->config);
    }

    if (!$api->email) {
      $form->populate(array('email' => $api->getEmail()));
    }

    // Check method/valid
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();

    $enabled = (bool)$values['enabled'];
    $email = $values['email'];
    unset($values['enabled']);
    unset($values['email']);

    // Validate gateway config
    if ($enabled) {
      $gatewayObject = $api->getGateway();

      try {
        $gatewayObject->setConfig($values);
        $response = $gatewayObject->test();
      } catch (Exception $e) {
        $enabled = false;
        $form->populate(array('enabled' => false));
        $form->addError(sprintf('Gateway login failed. Please double check ' .
          'your connection information. The gateway has been disabled. ' .
          'The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
      }
    } else {
      $form->addError('Gateway is currently disabled.');
    }

    // Process
    $message = null;
    try {
      $values = $api->getPlugin()->processAdminGatewayForm($values);
    } catch (Exception $e) {
      $message = $e->getMessage();
      $values = null;
    }

    if (null !== $values) {
      $api->setFromArray(array(
        'email' => $email,
        'enabled' => $enabled,
        'config' => $values,
      ));
      $api->save();

      $form->addNotice('Changes saved.');
    } else {
      $form->addError($message);
    }
  }
}
