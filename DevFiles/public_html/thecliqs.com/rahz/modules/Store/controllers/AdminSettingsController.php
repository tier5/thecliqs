<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminSettingsController extends Store_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_settings');

    $this->view->form = $form = new Store_Form_Admin_Settings();

    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $values                     = array();
    $values['request_amt']      = $settings->getSetting('store.request.amount', 100);
    $values['download_count']   = $settings->getSetting('store.download.count', 10);
    $values['minimum_price']    = (double)$settings->getSetting('store.minimum.price', 0.15);
    $values['commission_fixed'] = (double)$settings->getSetting('store.commission.fixed', '0.00');
    $values['show_cart']        = (int)$settings->getSetting('store.show.cart', 2);
    $values['show_mini_cart']   = (int)$settings->getSetting('store.show.mini.cart', 2);
    $values['digital_product']  = (int)$settings->getSetting('store.digital.product', 1);
    $values['browse_mode']      = $settings->getSetting('store.browse.mode', 'icons');
    $values['payment_mode']     = $settings->getSetting('store.payment.mode', 'client_site_store');
    $values['commission_percentage'] = (int)$settings->getSetting('store.commission.percentage', 0);

    $form->populate($values);

    // Check method/valid
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values          = array_merge($values, $form->getValues());

    $settings->__set('store.request.amount', $values['request_amt']);
    $settings->__set('store.download.count', $values['download_count']);
    $settings->__set('store.minimum.price', $values['minimum_price']);
    $settings->__set('store.commission.fixed', $values['commission_fixed']);
    $settings->__set('store.commission.percentage', $values['commission_percentage']);
    $settings->__set('store.show.cart', $values['show_cart']);
    $settings->__set('store.show.mini.cart', $values['show_mini_cart']);
    $settings->__set('store.digital.product', $values['digital_product']);
    $settings->__set('store.browse.mode', $values['browse_mode']);
    $settings->__set('store.payment.mode', $values['payment_mode']);

    if ($values['payment_mode'] == 'client_store') {
      /**
       * @var $gatewaysTbl Store_Model_DbTable_Gateways
       * @var $apisTbl Store_Model_DbTable_Apis
       */
      $gatewaysTbl = Engine_Api::_()->getDbTable('gateways', 'store');

      // Disable 2Checkout gateway
      $G2CO = $gatewaysTbl->fetchRow(array('title = ?' => '2Checkout'));
      if ($G2CO && $G2CO->enabled) {
        $G2CO->enabled = 0;
        $G2CO->save();
      }

      // Disable PayPal gateway
      $PayPal = $gatewaysTbl->fetchRow(array('title = ?' => 'PayPal'));
      if ($PayPal && $PayPal->enabled && !$PayPal->email) {
        $PayPal->enabled = 0;
        $PayPal->save();
      }

      // Disable APIs
      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
        $apisTbl = Engine_Api::_()->getDbTable('apis', 'store');

        // 2Checkout
        if ($G2CO) {
          // Disable all 2Checkout gateways
          $G2COs = $apisTbl->fetchAll(array('gateway_id = ?' => $G2CO->gateway_id, 'enabled = ?' => 1));
          if ($G2COs->count()) {
            foreach ($G2COs as $item) {
              $item->enabled = 0;
              $item->save();
            }
          }
        }

        // PayPal
        if ($PayPal) {
          // Disable all PayPal gateways
          $PayPals = $apisTbl->fetchAll(array('gateway_id = ?' => $PayPal->gateway_id, 'enabled = ?' => 1, 'email = ?' => ''));
          if ($PayPals->count()) {
            foreach ($PayPals as $item) {
              $item->enabled = 0;
              $item->save();
            }
          }
        }
      }
    }

    $form->addNotice('Your changes have been saved.');
  }
}