<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Logs.php 03.01.12 16:06 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_DbTable_Logs extends Engine_Db_Table
{
  public function getSelect($params = array())
  {
    $actionsTbl = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $userTbl = Engine_Api::_()->getDbTable('users', 'user');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $this->info('name')))
      ->joinLeft(array('a' => $actionsTbl->info('name')), 'c.action_id = a.action_id', array('a.action_id', 'a.action_type', 'a.action_module', 'a.action_name', 'a.max_credit', 'a.rollover_period'))
      ->joinLeft(array('u' => $userTbl->info('name')), 'c.user_id = u.user_id', array())
    ;

    if (!empty($params['user_id'])) {
      $select->where('c.user_id = ?', $params['user_id']);
    }

    if (!empty($params['displayname'])) {
      $select->where('u.displayname LIKE ?', '%' . $params['displayname'] . '%');
    }

    if (!empty($params['action_id'])) {
      $select->where('a.action_id = ?', $params['action_id']);
    }

    if (!empty($params['group_type'])) {
      $select->where('a.group_type = ?', $params['group_type']);
    }

    $select->order(( !empty($params['order']) ? $params['order'] : 'log_id' ) . ' ' . ( !empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
    return $select;
  }

  public function getTransaction($params = array())
  {
    $select = $this->getSelect($params);
    $paginator = Zend_Paginator::factory($select);

    if (!empty($params['ipp'])) {
      $params['ipp'] = (int)$params['ipp'];
      $paginator->setItemCountPerPage($params['ipp']);
    }

    if (!empty($params['page'])) {
      $params['page'] = (int)$params['page'];
      $paginator->setCurrentPageNumber($params['page']);
    }

    return $paginator;
  }

  public function checkCredit($action, $user_id)
  {
    if ($action == null || $action->action_module == null) {
      return false;
    }

    $select = $this->select()
      ->where('user_id = ?', $user_id)
      ->where('action_id = ?', $action->action_id)
    ;

    if ($action->rollover_period) {
      $select->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$action->rollover_period} DAY)"));
    }

    $credits = $this->fetchAll($select);

    $all_credits = 0;
    foreach($credits as $credit) {
      $all_credits += $credit->credit;
    }

    return ($all_credits < $action->max_credit || $action->action_type == 'signup') ? true : false;
  }

  public function getAvailableCredits($action, $user_id, $count = 1)
  {
    if ($action == null || $action->action_module == null) {
      return 0;
    }

    if ($action->action_name = 'signup') {
      return $action->credit;
    }

    $select = $this->select()
      ->where('user_id = ?', $user_id)
      ->where('action_id = ?', $action->action_id)
    ;

    if ($action->rollover_period) {
      $select->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$action->rollover_period} DAY)"));
    }

    $credits = $this->fetchAll($select);

    $all_credits = 0;
    foreach ($credits as $credit) {
      $all_credits += $credit->credit;
    }

    $credit_sum = $action->credit * $count;

    $left_for_today = $action->max_credit - $all_credits;

    if ($left_for_today <= 0) {
      return 0;
    } elseif ($left_for_today < $credit_sum) {
      return $left_for_today;
    }

    return $credit_sum;
  }

  public function checkJoin($action_id, $object)
  {
    $select = $this->select()
      ->where('action_id = ?', $action_id)
      ->where('user_id = ?', $object->subject_id)
      ->where('object_id = ?', $object->object_id)
      ->where('object_type = ?', $object->object_type)
      ->limit(1)
    ;
    if ($this->fetchRow($select) !== null) {
      return true;
    }
    return false;
  }

  public function checkLike($action_id, $object)
  {
    if ($object->getType() == 'core_like') {
      $object_type = $object->resource_type;
    } elseif ($object->getType() == 'activity_like') {
      $object_type = 'activity_action';
    } else {
      return false;
    }
    $select = $this->select()
      ->where('action_id = ?', $action_id)
      ->where('user_id = ?', $object->poster_id)
      ->where('object_id = ?', $object->resource_id)
      ->where('object_type = ?', $object_type)
      ->limit(1)
    ;
    if ($this->fetchRow($select) !== null) {
      return true;
    }
    return false;
  }
}
