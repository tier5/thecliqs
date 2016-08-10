<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminPaymentsController.php 18.01.12 13:25 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminPaymentsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_payments');

    /**
     * @var $table Credit_Model_DbTable_Payments
     * @var $settings Core_Model_DbTable_Settings
     */

    $table = Engine_Api::_()->getDbTable('payments', 'credit');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $table->setPrice();

    $this->view->form = $form = new Credit_Form_Admin_Payments_Add();

    $this->view->prices = $prices = $table->getPrices();
    $price = empty($prices[0]->credit) ? null : $prices[0];
    $this->view->credits_for_one_unit = ($price) ? (float)($price->credit/(float)$price->price) : 0; // credits for one unit
    $this->view->currency = $settings->getSetting('payment.currency', 'USD');

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $values = $this->getRequest()->getPost();

    if (empty($values['price']) || empty($values['credit'])) {
      return ;
    }

    if ($table->checkPrice($values)) {
      return ;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $row = $table->createRow();
      $row->setFromArray($values);
      $row->save();
      $db->commit();
    } catch(Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_redirectCustom($this->view->url(array('module' => 'credit', 'controller' => 'payments', 'action' => 'index'), 'admin_default', true));
  }

  public function deleteAction()
  {
    $payment_id = $this->_getParam('payment_id', 0);
    $table = Engine_Api::_()->getDbTable('payments', 'credit');
    if (!_ENGINE_ADMIN_NEUTER) {
      $table->deletePrice($payment_id);
    }
    $this->_redirectCustom($this->view->url(array('module' => 'credit', 'controller' => 'payments', 'action' => 'index'), 'admin_default', true));
  }
}
