<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Balances.php 10.01.12 18:26 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_DbTable_Balances extends Engine_Db_Table
{
  protected $_rowClass = 'Credit_Model_Balance';

  public function getMembers($values)
  {
    $usersTbl = Engine_Api::_()->getDbTable('users', 'user');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $this->info('name')))
      ->joinLeft(array('u' => $usersTbl->info('name')), 'u.user_id = c.balance_id');

    if (!empty($values['displayname'])) {
      $select->where('u.displayname LIKE ?', '%' . $values['displayname'] . '%');
    }

    if ($values['order'] == 'balance_id') {
      $values['order'] = 'c.balance_id';
    }

    $select->order(( !empty($values['order']) ? $values['order'] : 'c.balance_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($values['page']);
    return $paginator;
  }

  public function getTopUsersSelect($page = 1)
  {
    /**
     * @var $userTbl User_Model_DbTable_Users
     * @var $settings Core_Model_DbTable_Settings
     **/

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $order = $settings->getSetting('credit.default.sort.mode', 1) ? 'c.current_credit DESC' : 'c.earned_credit DESC';
    $rank = ($page - 1)*10;
    $userTbl = Engine_Api::_()->getDbTable('users', 'user');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("SET @count = ".$rank.";");
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $this->info('name')), array('c.*', 'place' => new Zend_Db_Expr('@count := @count + 1')))
      ->joinLeft(array('u' => $userTbl->info('name')), 'c.balance_id = u.user_id', array())
      ->order($order);

    return $select;
  }
}
