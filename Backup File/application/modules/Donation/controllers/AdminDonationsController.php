<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminIndexController
 *
 * @author adik
 */
class Donation_AdminDonationsController extends Core_Controller_Action_Admin
{

  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_admin_main', array(), 'donation_admin_main_donations');
  }

  public function indexAction()
  {
    /**
     * @var $select Zend_Db_Select
     * @var $table Donation_Model_DbTable_Donations
     */
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $page = $this->_getParam('page', 1);
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('donations', 'donation');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('d' => $table->info('name')))
      ->join(array('u' => $prefix . 'users'), "u.user_id = d.owner_id", array('username'))
      ->where('d.status <> ?', 'cancelled')
    ;

    $this->view->filterForm = $filterForm = new Donation_Form_Admin_Donations_Filter();

    $categories = Engine_Api::_()->getDbtable('categories', 'donation')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $key => $values) {
      $categoryOptions[$key] = $values;
    }
    $filterForm->category_id->setMultiOptions($categoryOptions);

    if ($filterForm->isValid($this->_getAllParams())) {
      $values = $filterForm->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'donation_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'donation_id') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

    if (!empty($values['title'])) {
      $select->where('title LIKE ?', '%' . $values['title'] . '%');
    }

    if (!empty($values['owner'])) {
      $select->where('u.displayname LIKE ?', '%' . $values['owner'] . '%');
    }

    if (!empty($values['target_sum'])) {
      $select->where('d.target_sum < ' . $values['target_sum']);
    }
    if( !empty($values['category_id']) && $values['category_id'] != -1){
      $select->where('d.category_id = ' . $values['category_id']);
    }

    if (!empty($values['type'])  && $values['type']) {
      $select->where('d.type = ?', $values['type']);
    }
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    $count = !empty($values['ipp']) ? $values['ipp'] : 20;

    $paginator->setItemCountPerPage($count);

    $paginator->setCurrentPageNumber($page);
//        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('donation')->getDonationsPaginator();
  }

  public function multiModifyAction()
  {
    $ids = $this->_getParam('modify');
    if (!empty($ids) && !_ENGINE_ADMIN_NEUTER) {
      $donationsTable = Engine_Api::_()->getDbTable('donations', 'donation');
      $donationsTable->deleteDonations($ids);
    }
    $this->_redirectCustom($this->view->url(array('module' => 'donation', 'controller' => 'donations', 'action' => 'index'), 'admin_default', true));
  }
  public function deleteAction()
  {
    $donation_id = $this->_getParam('donation_id', 0);

    $donation = Engine_Api::_()->getItem('donation',$donation_id);

    $donation->deleteDonation();

    $this->_redirectCustom($this->view->url(array('module' => 'donation', 'controller' => 'donations', 'action' => 'index'), 'admin_default', true));
  }

  public function approveAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $donation_id = (int)$this->_getParam('donation_id');
    $value = $this->_getParam('value');

    if ($donation_id) {
      $donation = Engine_Api::_()->getItem('donation', $donation_id);
      $donation->approvedStatus($value);
    }

    $this->redirect();
  }

  private function redirect()
  {
    return $this->_redirectCustom($this->view->url(array('module' => 'donation','controller' => 'donations'), 'admin_default', true));
  }
}