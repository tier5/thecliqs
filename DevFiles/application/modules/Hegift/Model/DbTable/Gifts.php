<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Gifts.php 03.02.12 16:20 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Model_DbTable_Gifts extends Engine_Db_Table
{
  protected $_rowClass = 'Hegift_Model_Gift';

  public function getSelect($params = array())
  {
    $select = $this->select();

    if (!empty($params['title'])) {
      $select->where('title LIKE ?', '%' . $params['title'] . '%');
    }

    if (!empty($params['category_id'])) {
      $select->where('category_id = ?', $params['category_id']);
    }

    if (!empty($params['amount'])) {
      $select->where('amount <> 0 OR ISNULL(amount)');
    }

    if (!empty($params['photo'])) {
      $select->where('photo_id <> 0');
    }

    if (!empty($params['enabled'])) {
      $select->where('enabled = 1');
    }

    if (!empty($params['type'])) {
      $select->where('type = ?', $params['type']);
    }

    if (!empty($params['sent_count'])) {
      $select->where('sent_count = 0');
    }

    if (!empty($params['owner_id'])) {
      if ($params['owner_id'] === true) {
        $select->where('owner_id <> 0');
      } else {
        $select->where('owner_id = ?', $params['owner_id']);
      }
    } else {
      $select->where('owner_id = 0');
    }

    if (!empty($params['status'])) {
      $select->where(new Zend_Db_Expr('IF(type = 3, status=1, true)'));
    }

    if (!empty($params['date'])) {
      $select
        ->where('NOT ISNULL(starttime) AND NOT ISNULL(endtime) AND endtime < NOW()')
      ;
    }

    if (!empty($params['sort'])) {
      switch ($params['sort']) {
        case 'recent' :
          $select
            ->order('creation_date DESC');
          break;
        case 'popular' :
          $select
            ->order('sent_count DESC');
          break;
        case 'actual' :
          $select
            ->where('starttime < NOW()')
            ->where('endtime > NOW()');
          break;
        case 'photo' :
          $select
            ->where('type = 1');
          break;
        case 'audio' :
          $select
            ->where('type = 2');
          break;
        case 'video' :
          $select
            ->where('type = 3');
          break;
   	  }
    } else {
      $select->order(( !empty($params['order']) ? $params['order'] : 'gift_id' ) . ' ' . ( !empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));
    }

    return $select;
  }

  public function getGifts($params = array())
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

  public function setDefaultCategory($category_id)
  {
    $select = $this->select()
      ->where('category_id = ?', $category_id);
    $gifts = $this->fetchAll($select);

    foreach ($gifts as $gift) {
      $gift->category_id = 1;
      $gift->save();
    }
  }
}
