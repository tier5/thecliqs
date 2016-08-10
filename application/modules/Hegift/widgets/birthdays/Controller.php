<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 09.03.12 11:21 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Widget_BirthdaysController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = $db->select()
      ->from('engine4_user_fields_meta', array('field_id'))
      ->where('type = ?', 'birthdate')
      ->where('display = 1')
      ->limit(1)
    ;
    $field_id = (int)$db->fetchOne($select);

    /**
     * @var $table User_Model_DbTable_Users
     */

    $table = Engine_Api::_()->getDbTable('users', 'user');

    $select = $viewer->membership()->getMembersOfSelect();
    $friends = $table->fetchAll($select);

    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->resource_id;
    }

    if (!count($ids)) {
      $ids = array(0);
    }

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('u' => $table->info('name')), array('u.*'))
      ->joinLeft(array('v' => 'engine4_user_fields_values'), 'u.user_id = v.item_id', array('birthday' => 'v.value'))
      ->where("u.user_id IN(" . join(',', $ids) . ")") // friends
      ->where('v.field_id = ?', $field_id) // birthdate
      ->where('NOT ISNULL(v.value)')
    ;

    $users = $table->fetchAll($select);

    $birthdays = array();
    $counter = 0;
    $oldTz = date_default_timezone_get();
    $viewerTz = $viewer->timezone;
    date_default_timezone_set($viewerTz);
    foreach ($users as $user) {
      if ($this->checkMonth($this->convertDate($user->birthday, 'month'))) {
        if ($this->checkDay($this->convertDate($user->birthday))) {
          $birthdays[$counter]['when'] = 'today';
          $birthdays[$counter]['sent'] = $this->isSent($user, 2);
          $birthdays[$counter++]['user'] = $user;
        } elseif ($this->checkDay($this->convertDate($user->birthday), 'tomorrow')) {
          $birthdays[$counter]['when'] = 'tomorrow';
          $birthdays[$counter]['sent'] = $this->isSent($user, 1);
          $birthdays[$counter++]['user'] = $user;
        }
      }
    }
    date_default_timezone_set($oldTz);

    if (!count($birthdays)) {
      return $this->setNoRender();
    }

    $this->view->birthdays = $birthdays;
  }

  public function convertDate($date, $date_type = 'day')
  {
    $date_array = preg_split('/-/', $date);

    if ($date_type == 'day') {
      return $date_array['2'];
    } elseif ($date_type == 'month') {
      return $date_array['1'];
    } elseif ($date_type == 'year') {
      return $date_array['0'];
    }
  }

  public function checkDay($day, $when = 'today')
  {
    if ($when == 'today') {
      if ($day == date("j")) {
        return true;
      }
    } elseif ($when == 'tomorrow') {
      if ($day - date("j") == 1) {
        return true;
      }
    }
    return false;
  }

  public function checkMonth($month)
  {
    if ($month == date("n")) {
      return true;
    }
    return false;
  }

  public function isSent($user, $day_count)
  {
    /**
     * @var $viewer User_Model_User
     * @var $user User_Model_User
     * @var $table Hegift_Model_DbTable_Recipients
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
    $select = $table->select()
      ->where('subject_id = ?', $viewer->getIdentity())
      ->where('object_id = ?', $user->getIdentity())
      ->where('send_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$day_count} DAY)"))
      ->limit(1)
    ;

    return ($table->fetchRow($select)) ? true : false;
  }
}
