<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetGatewayState.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_View_Helper_GetGatewayState extends Zend_View_Helper_Abstract
{
  public function getGatewayState()
  {
    /**
     * @var $table Store_Model_DbTable_Gateways
     * @var $gateway Store_Model_Api
     * @var $storeApi Store_Api_Core
     */

    $storeApi = Engine_Api::_()->store();
    $mode = $storeApi->getPaymentMode();
    $table = Engine_Api::_()->getDbTable('gateways', 'store');

    if ($mode == 'client_store') {
      $PayPal = $table->fetchRow(array('title = ?' => 'PayPal'));
      $enabled = ($PayPal) ? (boolean)$PayPal->enabled : false;
      $message = 'STORE_Enable your PayPal gateway to view and sell your products on Browse Products Page.';
    } else {
      $enabled = ((int)Engine_Api::_()->getDbTable('gateways', 'store')->getEnabledGatewayCount() > 0) ? true : false;
      $message = 'STORE_Enable your gateway to view and sell your products on Browse Products Page.';
    }

    $linkTitle = 'STORE_Gateway Settings';
    $link = array('route' => 'admin_default', 'module' => 'store', 'controller' => 'gateway');

    return $this->view->partial('admin/_gatewayState.tpl', 'store', array(
      'enabled' => $enabled,
      'link' => $link,
      'message' => $message,
      'linkTitle' => $linkTitle
    ));
  }
}