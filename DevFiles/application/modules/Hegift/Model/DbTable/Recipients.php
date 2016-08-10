<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Recipients.php 10.02.12 15:38 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Model_DbTable_Recipients extends Engine_Db_Table
{
  protected $_rowClass = 'Hegift_Model_Recipient';

  public function getSelect($params = array())
  {
    $select = $this->select()
      ->order('send_date DESC')
    ;

    if (!empty($params['gift_id'])) {
      $select->where('gift_id = ?', $params['gift_id']);
    }

    if (!empty($params['subject_id'])) {
      $select->where('subject_id = ?', $params['subject_id']);
    }

    if (!empty($params['approved'])) {
      $select->where('approved = 0');
    }

    if (!empty($params['action_name'])) {
      $user = Engine_Api::_()->getItem('user', $params['user_id']);
      if ($params['action_name'] == 'received') {
        $select
          ->where('object_id = ?', $user->getIdentity())
          ->where('approved=1')
        ;
      } else {
        $select->where('subject_id = ?', $user->getIdentity());
      }
    }
    return $select;
  }

  public function getPaginator($params = array())
  {
    $select = $this->getSelect($params);

    $paginator = Zend_Paginator::factory($select);
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['ipp'])) {
      $paginator->setItemCountPerPage($params['ipp']);
    }

    return $paginator;
  }

  public function checkGiftForUser($object_id, $gift_id)
  {
    $subject_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $select = $this->select()
      ->where('subject_id = ?', $subject_id)
      ->where('object_id = ?', $object_id)
      ->where('gift_id = ?', $gift_id)
    ;

    if ($this->fetchRow($select) === null) {
      return false;
    } else {
      return true;
    }
  }
}
