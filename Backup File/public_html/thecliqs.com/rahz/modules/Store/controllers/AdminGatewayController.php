<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminGatewayController.php 9069 2011-07-20 20:41:47Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_AdminGatewayController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->menu       = $this->_getParam('action');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_gateways');
  }

  public function indexAction()
  {
    // Test curl support
    if (!function_exists('curl_version') ||
      !($info = curl_version())
    ) {
      $this->view->error = $this->view->translate('The PHP extension cURL ' .
        'does not appear to be installed, which is required ' .
        'for interaction with payment gateways. Please contact your ' .
        'hosting provider.');
    }
    else if (!($info['features'] & CURL_VERSION_SSL) ||
      !in_array('https', $info['protocols'])
    ) {
      $this->view->error = $this->view->translate('The installed version of ' .
        'the cURL PHP extension does not support HTTPS, which is required ' .
        'for interaction with payment gateways. Please contact your ' .
        'hosting provider.');
    }

    // Make paginator
    $select = Engine_Api::_()->getDbtable('gateways', 'store')
      ->select()
      ->where('`plugin` != ?', 'Store_Plugin_Gateway_Testing')
      ->where('`plugin` != ?', 'Store_Plugin_Gateway_Credit')
    ;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function editAction()
  {
    /**
     * Get gateway
     *
     * @var $gateway Store_Model_Gateway
     */
    $this->view->gateway = $gateway = Engine_Api::_()->getDbtable('gateways', 'store')
      ->find($this->_getParam('gateway_id'))
      ->current();

    $mode = Engine_Api::_()->store()->getPaymentMode();

    if ($mode == 'client_store' && $gateway && $gateway->getTitle() == '2Checkout') {
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    // Make form
    $this->view->form = $form = $gateway->getPlugin()->getAdminGatewayForm(array('isTestMode' => $gateway->test_mode));

    // Populate form
    $form->populate($gateway->toArray());
    if (is_array($gateway->config)) {
      $form->populate($gateway->config);
    }

    if (!$gateway->email) {
      $form->populate(array('email' => $gateway->getEmail()));
    }

    // if demoadmin
    if (_ENGINE_ADMIN_NEUTER) {
      $form->getElement('email')->setValue('******************');
      $form->getElement('password')->setValue('******************');
      $form->getElement('username')->setValue('******************');
      $form->getElement('signature')->setValue('******************');
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
      $gatewayObject = $gateway->getGateway();

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
      $values = $gateway->getPlugin()->processAdminGatewayForm($values);
    } catch (Exception $e) {
      $message = $e->getMessage();
      $values  = null;
    }

    if (null !== $values) {
      $gateway->setFromArray(array(
        'email'   => $email,
        'enabled' => $enabled,
        'config'  => $values,
      ));
      $gateway->save();

      $form->addNotice('Changes saved.');
    } else {
      $form->addError($message);
    }
  }

  public function buttonAction()
  {
    /**
     * Get gateway
     *
     * @var $gateway Store_Model_Gateway
     */
    $this->view->gateway = $gateway = Engine_Api::_()->getDbtable('gateways', 'store')
      ->find($this->_getParam('gateway_id'))
      ->current();

    $mode = Engine_Api::_()->store()->getPaymentMode();

    if ($mode == 'client_store' && $gateway && $gateway->getTitle() == '2Checkout') {
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $gateway->button_url = $this->getRequest()->getParam('gateway-button');
    $this->view->status  = $gateway->save();
  }
}