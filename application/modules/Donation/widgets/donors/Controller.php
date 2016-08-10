<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       03.09.12
 * @time       11:50
 */
class Donation_Widget_DonorsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $table = Engine_Api::_()->getItemTable('transaction');
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    $select = $table->select()
      ->from(array('t' => $table->info('name')), array('t.*', new Zend_Db_Expr('SUM(t.amount) AS amounted')))
      ->where('t.user_id IN (?)', $table->select()->from($table->info('name'), array($table->info('name') . '.user_id')))
      ->where('t.user_id > ?', 0)
      ->where('t.state = ?', 'completed')
      ->group('t.user_id')
      ->order(new Zend_Db_Expr('SUM(t.amount) DESC'))
    ;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage($settings->getSetting('donation.donors_page_count', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }
}
