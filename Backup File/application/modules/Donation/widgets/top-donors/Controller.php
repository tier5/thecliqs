<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 12:31
 * To change this template use File | Settings | File Templates.
 */
class Donation_Widget_TopDonorsController extends Engine_Content_Widget_Abstract
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

    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
    $paginator->setItemCountPerPage($settings->getSetting('donation.donors.count', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function getCacheKey()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    return $viewer->getIdentity() . $translate->getLocale();
  }

  public function getCacheSpecificLifetime()
  {
    return 120;
  }
}
