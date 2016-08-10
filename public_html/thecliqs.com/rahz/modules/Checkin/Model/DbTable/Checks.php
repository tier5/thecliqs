<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Checks.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Model_DbTable_Checks extends Engine_Db_Table
{
  protected $_rowClass = "Checkin_Model_Check";

  public function getCheckins($user_id, $config)
  {
    $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $select = $activityTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('a' => $activityTbl->info('name')))
      ->joinInner(array('c' => $this->info('name')), 'a.action_id = c.action_id', array())
      ->joinInner(array('p' => $placesTbl->info('name')), 'c.place_id = p.place_id', array('place_id', 'checkin' => 'place_id'))
      ->where('c.user_id = ?', $user_id);

    if (isset($config['max_id']) && $config['max_id']) {
      $select
        ->where('a.action_id < ?', $config['max_id']);
    }

    $select
      ->order('a.action_id DESC');

    if (isset($config['limit']) && $config['limit']) {
      $select->limit($config['limit']);
    }
    return $activityTbl->fetchAll($select);
  }

  public function getActionById($action_id)
  {
    $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
    $select = $activityTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('a' => $activityTbl->info('name')))
      ->joinLeft(array('c' => $this->info('name')), 'a.action_id = c.action_id')
      ->where('c.action_id = ?', $action_id)
      ->limit(1);

    return $activityTbl->fetchRow($select);
  }

  public function getCheckin($check_id)
  {
    return $this->fetchRow(
      $this->select()
        ->where('check_id = ?', $check_id)
        ->limit(1)
    );
  }

  public function getMatchedChekinsCount($google_id, $user_id, $page_id = null, $count = true, $limit = true)
  {
    if (!$count) {
      $select = $this->select();

      if ($google_id) {
        $places = Engine_Api::_()->getDbTable( 'places', 'checkin' );
        $place = $places->fetchRow( $places->select()->where('google_id=?', $google_id) );

        $select->where('place_id = ?', $place->place_id);
      } elseif ($page_id) {
        $places = Engine_Api::_()->getDbTable( 'places', 'checkin' );
        $place = $places->fetchRow( $places->select()->where('object_id=?', $page_id)->where('object_type=?', 'page') ) ;

        $select->where('place_id = ?', $place->place_id );
      }

      $select->where('user_id <> ?', $user_id);
      //print_die( $select.'' );
      $checkins = $this->fetchAll($select);

      $ids = array(0);
      foreach ($checkins as $checkin) {
        $ids[] = $checkin->user_id;
      }
      $ids = array_unique($ids);

      /**
       * @var $table User_Model_DbTable_Users
       **/

      $table = Engine_Api::_()->getDbTable('users', 'user');
      $select = $table->select()
        ->where('user_id IN('.join(',', $ids).')');
      if ($limit) {
        $select->limit(9);
      }

      return Zend_Paginator::factory($select);
    }

    if ($page_id) {
      $places = Engine_Api::_()->getDbTable( 'places', 'checkin' );
      $place = $places->fetchRow( $places->select()->where('object_id=?', $page_id)->where('object_type=?', 'page') ) ;

      return count($this->fetchAll($this->select()
          ->where('place_id = ?', $place->place_id)
          ->where('user_id <> ?', $user_id)
      )) + 1;
    }


    $places = Engine_Api::_()->getDbTable( 'places', 'checkin' );
    $place = $places->fetchRow( $places->select()->where('google_id=?', $google_id) ) ;

    $select = $this->select()
      ->from($this->info('name'), 'user_id')
      ->distinct(true)
      ->where('place_id = ?', $place->place_id)
      ->where('user_id <> ?', $user_id);

    return count($this->fetchAll($select)) + 1;
  }

  public function getMarkers($activities)
  {
    $markers = array();
    $noPhoto = 'application/modules/Checkin/externals/images/nophoto.png';
    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    $isEventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event');

    $placeIds = array();
    foreach( $activities as $activity ) {
      $placeIds[] = $activity->place_id;
    }

    if (!$placeIds) {
      return array();
    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
    $places = $placesTbl->findByIds($placeIds);

    foreach ($places as $place) {
      if ($place->longitude == null) {
        continue;
      }

      if ($isPageEnabled && $place->object_type == 'page') {
        $page = Engine_Api::_()->getItem('page', $place->object_id);
        $markers[] = array(
          'lat' => $place->latitude,
          'lng' => $place->longitude,
          'object_id' => $place->object_id,
          'pages_photo' => $page->getPhotoUrl('thumb.normal'),
          'title' => $page->getTitle(),
          'desc' => Engine_String::substr($page->getDescription(),0,200),
          'url' => $page->getHref()
        );
      }
      else if ($isEventEnabled && $place->object_type == 'event') {
        $event = Engine_Api::_()->getItem('event', $place->object_id);
        $markers[] = array(
          'lat' => $place->latitude,
          'lng' => $place->longitude,
          'object_id' => $place->object_id,
          'pages_photo' => $event->getPhotoUrl('thumb.normal'),
          'title' => $event->getTitle(),
          'desc' => Engine_String::substr($event->getDescription(),0,200),
          'url' => $event->getHref()
        );
      }
      else {
        $markers[] = array(
          'lat' => $place->latitude,
          'lng' => $place->longitude,
          'checkin_icon' => ($place->icon) ? $place->icon : $noPhoto,
          'title' => $place->name,
        );
      }
    }

    return $markers;
  }

  public function getActionsByPage($page_id)
  {

    $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $select = $activityTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('a' => $activityTbl->info('name')))
      ->joininner(array('c' => $this->info('name')), 'a.action_id = c.action_id')
      ->joininner(array('p' => $placesTbl->info('name')), 'c.place_id = p.place_id', array('page_id' => 'p.object_id'))
      ->where('p.object_type = ?', 'page')
      ->where('p.object_id = ?', $page_id)
      ->order('a.action_id DESC');


    return Zend_Paginator::factory($select);
  }

  public function getListByActionIds($action_ids)
  {
    if (!$action_ids) {
      return array();
    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $select = $placesTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $placesTbl->info('name')))
      ->joinInner(array('c' => $this->info('name')), 'p.place_id = c.place_id', array('action_id'))
      ->where('action_id IN (?)', $action_ids);

    return $this->fetchAll($select);
  }

  public function getActionsByObject($object_type, $object_id)
  {
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
    $place = $placesTbl->findByObject($object_type, $object_id);

    if (!$place) {
      return Zend_Paginator::factory(array());
    }

    $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
    $select = $activityTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('a' => $activityTbl->info('name')))
      ->joinLeft(array('c' => $this->info('name')), 'a.action_id = c.action_id', array('c.place_id'))
      ->where('c.place_id = ?', $place->place_id)
      ->order('a.action_id DESC');

    return Zend_Paginator::factory($select);
  }

  public function getObjectVisitorCount($object_type, $object_ids)
  {
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $this->info('name')), array('p.object_id', new Zend_Db_Expr('COUNT(DISTINCT `c`.`user_id`)')))
      ->joinInner(array('p' => $placesTbl->info('name')), 'c.place_id = p.place_id')
      ->where('p.object_type = ?', $object_type)
      ->group('p.place_id');

    if (is_array($object_ids)) {
      $object_ids = ($object_ids) ? $object_ids : array(0);
      $select->where('p.object_id IN (?)', $object_ids);
    } else {
      $select->where('p.object_id = ?', $object_ids);
    }

    return $this->getAdapter()->fetchPairs($select);
  }

  public function getGoogleVisitorCount($google_ids)
  {
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $this->info('name')),  array('p.google_id', new Zend_Db_Expr('COUNT(DISTINCT c.user_id)')))
      ->joinInner(array('p' => $placesTbl->info('name')), 'c.place_id = p.place_id')
      ->group('p.place_id');

    if (is_array($google_ids)) {
      $google_ids = ($google_ids) ? $google_ids : array(0);
      $select->where('p.google_id IN (?)', $google_ids);
    } else {
      $select->where('p.google_id = ?', $google_ids);
    }

    return $this->getAdapter()->fetchPairs($select);
  }

  public function getPlaceVisitorCount($place_id)
  {
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array($this->info('name')),  array(new Zend_Db_Expr('COUNT(DISTINCT `user_id`)')))
      ->where('place_id = ?', $place_id)
      ->group('place_id');

    return $this->getAdapter()->fetchOne($select);
  }

  public function getPlaceVisitors($place_id, $limit = 10)
  {
    $usersTbl = Engine_Api::_()->getDbTable('users', 'user');

    $select = $usersTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('u' => $usersTbl->info('name')))
      ->joinInner(array('c' => $this->info('name')), 'u.user_id = c.user_id', array())
      ->where('place_id = ?', $place_id)
      ->group('user_id');

    return $usersTbl->fetchAll($select);
  }
}