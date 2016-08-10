<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Api_Core extends Core_Api_Abstract
{

  protected $_itemTypes = array(
    'page'  => 'Page',
    'poll'  => 'Poll',
    'blog'  => 'Blog',
    'event' => 'Event',
    'video' => 'Video',
    'group' => 'Group',
    'offer' => 'Offers',
    'music_playlist' => 'Playlist',
    'quiz'  => 'Quiz',
    'classified'  => 'Classified',
    'poll'  => 'Poll',
    'album'  => 'Album',
    'album_photo'  => 'Album Photo',
    'article' => 'Article',
    'question' => 'Question',
    'store_product' => 'Store Product',
    'playlist' => 'Page Music',
    'pageblog' => 'Page Blog',
    'pagedocument' => 'Page Document',
    'pagealbum' => 'Page Album',
    'pageevent' => 'Page Event',
    'pagevideo' => 'Page video',
    'avp_video' => 'Video',
    'ynmusic_album' => 'Music',
    'artarticle' => 'Article',
    'list_listing' => 'Listing',
    'document' => 'Document',
    'job' => 'Job'
  );

  protected $_actionTypes = array(
    'page'  => 'page_create',
    'poll'  => 'poll_new',
    'blog'  => 'blog_new',
    'event' => 'event_create',
    'video' => 'video_new',
    'group' => 'group_create',
    'music_playlist' => 'music_playlist_new',
    'quiz'  => 'quiz_new',
    'classified'  => 'classified_new',
    'album'  => 'album_photo_new',
    'article'  => 'article_new',
    'question' => 'question_new'
  );

  public function getItemTypes()
  {
    $modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();
    $data = array();
    foreach ($modules as $module) {
      if ($module == 'music') {
        $module = 'music_playlist';
      }
      if ($module == 'store') {
        $module = 'store_product';
      }
      if ($module == 'offers') {
        $module = 'offer';
      }
      if ($module == 'album') {
        $data['album_photo'] = $this->_itemTypes['album_photo'];
      }
      if ($module == 'avp') {
        $data['avp_video'] = $this->_itemTypes['avp_video'];
      }
      if ($module == 'pagevideo') {
        $data['pagevideo'] = $this->_itemTypes['pagevideo'];
      }
      if ($module == 'ynmusic') {
        $data['ynmusic_album'] = $this->_itemTypes['ynmusic_album'];
      }
      if ($module == 'pageblog') {
        $data['pageblog'] = $this->_itemTypes['pageblog'];
      }
      if ($module == 'pagedocument') {
        $data['pagedocument'] = $this->_itemTypes['pagedocument'];
      }
      if ($module == 'advancedarticles') {
        $data['artarticle'] = $this->_itemTypes['artarticle'];
      }
      if ($module == 'pagemusic') {
        $data['playlist'] = $this->_itemTypes['playlist'];
      }
      if ($module == 'pagealbum') {
        $data['pagealbum'] = $this->_itemTypes['pagealbum'];
      }
      if ($module == 'list') {
        $data['list_listing'] = $this->_itemTypes['list_listing'];
      }

      if (!isset($this->_itemTypes[$module])) {
        continue ;
      }

      $data[$module] = $this->_itemTypes[$module];
    }

    return $data;
  }

  public function getActionTypes()
  {
    $modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();
    $data = array();
    foreach ($modules as $module) {
      if ($module == 'music') {
        $module = 'music_playlist';
      }
      if (!isset($this->_actionTypes[$module])) {
        continue ;
      }
      $data[$module] = $this->_actionTypes[$module];
    }

    return $data;
  }

  public function clearSession()
  {
    $session = new Zend_Session_Namespace();
    unset($session->show_popup);
    unset($session->suggest_type);
    unset($session->object_type);
    unset($session->object_id);
  }

  public function isAllowed($suggestType)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    switch ($suggestType) {
      case 'fr_sent': return (bool)$settings->getSetting('suggest.friend.add', 1); break;
      case 'fr_confirm': return (bool)$settings->getSetting('suggest.friend.confirm', 1); break;
      case 'blog_new': return (bool)$settings->getSetting('suggest.popup.create.blog', 1); break;
      case 'video_new': return (bool)$settings->getSetting('suggest.popup.create.video', 1); break;
      case 'classified_new': return (bool)$settings->getSetting('suggest.popup.create.classified', 1); break;
      case 'poll_new': return (bool)$settings->getSetting('suggest.popup.create.poll', 1); break;
      case 'music_playlist_new': return (bool)$settings->getSetting('suggest.popup.create.music_playlist', 1); break;
      case 'quiz_new': return (bool)$settings->getSetting('suggest.popup.create.quiz', 1); break;
      case 'quiz_take': return (bool)$settings->getSetting('suggest.popup.take.quiz', 1); break;
      case 'page_create': return (bool)$settings->getSetting('suggest.popup.create.page', 1); break;
      case 'event_create': return (bool)$settings->getSetting('suggest.popup.create.event', 1); break;
      case 'group_create': return (bool)$settings->getSetting('suggest.popup.create.group', 1); break;
      case 'article_new': return (bool)$settings->getSetting('suggest.popup.create.article', 1); break;
      case 'question_new': return (bool)$settings->getSetting('suggest.popup.create.question', 1); break;
      case 'album_photo_new': return (bool)$settings->getSetting('suggest.popup.create.album', 1); break;
      case 'gr_join': return (bool)$settings->getSetting('suggest.group.join', 1); break;
      case 'gr_accept': return (bool)$settings->getSetting('suggest.group.accept', 1); break;
      case 'ev_accept': return (bool)$settings->getSetting('suggest.event.accept', 1); break;
      case 'ev_join': return (bool)$settings->getSetting('suggest.event.join', 1); break;
      case 'link_page': return (bool)$settings->getSetting('suggest.link.page', 1); break;
      case 'link_event': return (bool)$settings->getSetting('suggest.link.event', 1); break;
      case 'link_group': return (bool)$settings->getSetting('suggest.link.group', 1); break;
      case 'link_blog': return (bool)$settings->getSetting('suggest.link.blog', 1); break;
      case 'link_poll': return (bool)$settings->getSetting('suggest.link.poll', 1); break;
      case 'link_user': return (bool)$settings->getSetting('suggest.link.user', 1); break;
      case 'link_classified': return (bool)$settings->getSetting('suggest.link.classified', 1); break;
      case 'link_music_playlist': return (bool)$settings->getSetting('suggest.link.music_playlist', 1); break;
      case 'link_video': return (bool)$settings->getSetting('suggest.link.video', 1); break;
      case 'link_album_photo': return (bool)$settings->getSetting('suggest.link.photo', 1); break;
      case 'link_album': return (bool)$settings->getSetting('suggest.link.album', 1); break;
      case 'link_quiz': return (bool)$settings->getSetting('suggest.link.quiz', 1); break;
      case 'link_article': return (bool)$settings->getSetting('suggest.link.article', 1); break;
      case 'link_question': return (bool)$settings->getSetting('suggest.link.question', 1); break;
      case 'suggest_profile_photo': return (bool)$settings->getSetting('suggest.profile.photo', 1); break;
      case 'link_store_product': return (bool)$settings->getSetting('suggest.store.product', 1); break;
      case 'link_playlist': return (bool)$settings->getSetting('suggest.link.playlist', 1); break;
      case 'link_pagealbum': return (bool)$settings->getSetting('suggest.link.pagealbum', 1); break;
      case 'link_pageblog': return (bool)$settings->getSetting('suggest.link.pageblog', 1); break;
      case 'link_pagedocument': return (bool)$settings->getSetting('suggest.link.pagedocument', 1); break;
      case 'link_pageevent': return (bool)$settings->getSetting('suggest.link.pageevent', 1); break;
      case 'link_pagevideo': return (bool)$settings->getSetting('suggest.link.pagevideo', 1); break;
      case 'link_ynmusic_album' : return (bool)$settings->getSetting('suggest.link.ynmusic.album', 1); break;
      case 'link_avp_video' : return (bool)$settings->getSetting('suggest.link.avp.video', 1); break;
      case 'link_artarticle' : return (bool)$settings->getSetting('suggest.link.artarticle', 1); break;
      case 'link_list_listing' : return (bool)$settings->getSetting('suggest.link.list.listing', 1); break;
      case 'link_document' : return (bool)$settings->getSetting('suggest.link.document', 1); break;
      case 'link_job' : return (bool)$settings->getSetting('suggest.link.job', 1); break;
      case 'link_offer': return (bool) $settings->getSetting('suggest.link.offer', 1); break;
      default: return false; break;
    }

    return false;
  }

  public function isMember($resource_type, $resource_id, $user_id)
  {
    if (!$resource_id || !$user_id) {
      return null;
    }

    $table = Engine_Api::_()->getDbTable('membership', $resource_type);
    $select = $table->select()
      ->where('resource_id = ?', $resource_id)
      ->where('user_id = ?', $user_id)
      ->where('user_approved = ?', 1);

    return (bool)$table->getAdapter()->fetchOne($select);
  }

  public function getAllSuggests(array $params, $grouped = true)
  {
    $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $paginator = $table->getPaginator($params);
    $paginator->setItemCountPerPage(100);
    $to = Engine_Api::_()->user()->getViewer();
    $to_id = $to->getIdentity();

    if ($grouped) {
      $data = array();
      foreach ($paginator as $suggest) {
        $object_type = $suggest->object_type;
        $object_id = $suggest->object_id;
        if (in_array($object_type, array('user', 'group', 'event'))) {
          if ($this->isMember($object_type, $object_id, $to_id)) {
            $suggest->delete();
            continue ;
          }
        }
        if (isset($data[$object_type][$object_id])) {
          continue ;
        }
        $data[$object_type][$object_id] = $suggest;
      }
      return $data;
    }

    return $paginator;
  }

  public function flushCache($user, $type)
  {
    $user = (int)$user;
    $cache = Engine_Cache::factory();
    $id = md5('user_'.$type.'_recommendations_'.$user);
    $cache->remove($id);
  }

  public function checkItemModule($type)
  {
    switch ($type) {
      case 'music_playlist':
        $module = 'music';
      break;
      case 'album_photo':
        $module = 'album';
      break;
      case 'store_product':
        $module = 'store';
      break;
      case 'suggest_profile_photo':
        return true;
      break;
      case 'playlist':
        $module = 'pagemusic';
      break;
      break;
      case 'pagediscussion_pagepost':
        $module = 'pagediscussion';
      break;
      case 'ynmusic_album':
        $module = 'ynmusic';
      break;
      case 'avp_video':
        $module = 'avp';
      break;
      case 'artarticle':
        $module = 'advancedarticles';
      break;
      case 'list_listing':
        $module = 'list';
      break;
      default:
        $module = $type;
      break;
    }

    return (bool)Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($module);
  }

  public function getRecommendations($user, $type, $except = array())
  {
    $moduleItem = $type;
    if ($type == 'friend' || $type == 'profile_photo_suggest') {
      $moduleItem = 'user';
    }

    if (!$this->checkItemModule($moduleItem)) {
      return array();
    }

    $user = (int)$user;
    if (APPLICATION_ENV == 'production') {
      $cache = Engine_Cache::factory();
      $id = md5('user_'.$type.'_recommendations_'.$user);
      $data = $cache->load($id);
      if ($data) {
        return $data;
      }
    }

    if ($type == 'friend' || $type == 'profile_photo_suggest') {
      $table = Engine_Api::_()->getItemTable('user');
    } else {
      $table = Engine_Api::_()->getItemTable($type);
    }
    
    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $recTable = Engine_Api::_()->getDbTable('recommendations', 'suggest');
    $rejTable = Engine_Api::_()->getDbTable('rejected', 'suggest');

    $db = $membershipTable->getAdapter();
    $m = $membershipTable->info('name');
    $b = $table->info('name');
    $l = $likeTable->info('name');
    $r = $recTable->info('name');
    $rej = $rejTable->info('name');

    $select = $membershipTable->select()
      ->setIntegrityCheck(false)
      ->from($m, 'user_id')
			->where($m.'.resource_id = ?', $user);
			//->where($m.'.resource_approved = 1') // and requests
			//->where($m.'.user_approved = 1');

    $friendIds = $db->fetchCol($select);

    $primary = $table->info('primary');
    $primary = $primary[1];

    $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.widget.item.count', 6);
    $select = $rejTable->select()
      ->setIntegrityCheck(false)
      ->from($rej, array('object_id'))
      ->where($rej.'.user_id = ?', $user)
      ->where($rej.'.object_type = ?', $type);
    $rejIds = $db->fetchCol($select);

    // Except Self
    //$rejIds[] = $user;

    switch ($type) {
      case 'profile_photo_suggest':
      case 'friend':
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from($b)
          ->joinLeft($m, $m.'.user_id = '.$b.'.user_id', array())
          ->where($m.'.resource_id = ?', $user)
          ->where($m.'.resource_approved = 1')
          ->where($m.'.user_approved = 1')
          ->limit($limit);
      break;
      default:
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from($b)
          ->joinInner($r, $b.'.'.$primary.' = '.$r.'.object_id AND '.$r.'.object_type = "'.$type.'"')
          ->limit($limit);
      break;
    }

    if ($type == 'profile_photo_suggest') {
      $select
        ->where($b.'.photo_id = 0');

    } else if ($type == 'user' && !empty($friendIds)){
      $select
          ->where("{$b}.".$primary." NOT IN (?)", $friendIds);
    }

    if (!empty($rejIds)) {
      $select
        ->where("{$b}.".$primary." NOT IN (?)", $rejIds);
    }

    if (!empty($except)) {
      $select
        ->where("{$b}.".$primary." NOT IN (?)", $except);
    }

    switch ($type) {
      case 'profile_photo_suggest':
      case 'friend':
        $items = $table->fetchAll($select);

        if (APPLICATION_ENV == 'production') {
          $cache->save($items, $id);
          $cache->setLifetime(3600);
        }

        return $items;
      break;      
    }

    if ($type == 'blog') {
      $select
        ->where("{$b}.draft = ?", 0);
    }

    if ($type == 'page') {
      $select
        ->where("{$b}.approved = ?", 1);

      $pageMemTbl = Engine_Api::_()->getDbTable('membership', 'page');

      $pageSel = $pageMemTbl->select()
        ->from(array('m' => $pageMemTbl->info('name')), 'm.resource_id')
        ->where('m.user_id = ?', $user)
        ->where('m.resource_approved = ?', 1)
        ->where('m.user_approved = ?', 1);
      $page_ids = $pageMemTbl->getAdapter()->fetchCol($pageSel);

      if (!empty($page_ids)) {
        $select->where("{$b}.".$primary." NOT IN (?)", $page_ids);
      }

    }

    $adminRecs = $table->fetchAll($select); // if ($type == 'album_photo') {echo $select; exit();}
    $adminRecIds = array();
    foreach ($adminRecs as $rec) {
      $adminRecIds[] = $rec->getIdentity();
    }



    switch ($type) {
      case 'blog':
      case 'classified':
      case 'video':
      case 'pagevideo':
      case 'album':
      case 'album_photo':
        $rank = "( ({$b}.comment_count * 5) + ({$b}.view_count * 1) + (COUNT({$l}.like_id) * 10)";
      break;
      case 'quiz':
        $rank = "( ({$b}.comment_count * 5) + ({$b}.view_count * 1) + ({$b}.take_count * 15) + (COUNT({$l}.like_id) * 10)";
      break;
      case 'music_playlist':
      case 'playlist':
      case 'ynmusic_album':
        $rank = "( ({$b}.play_count * 5) + (COUNT({$l}.like_id) * 10)";
      break;
      case 'poll':
        $rank = "( ({$b}.comment_count * 5) + ({$b}.vote_count * 3) + (COUNT({$l}.like_id) * 10)";
      break;
      case 'question':
        $rank = "( ({$b}.question_views * 5) + (COUNT({$l}.like_id) * 10)";
      break;
      case 'artarticle':
        $rank = "(({$b}.num_views * 1)";
        break;
      case 'document':
        $rank = "(({$b}.views * 1)";
        break;
      default:
        $rank = "( ({$b}.view_count * 1) + (COUNT({$l}.like_id) * 10)";
      break;
    }

    switch ($type) {
      case 'blog':
      case 'classified':
      case 'video':
      case 'album':
      case 'album_photo':
      case 'playlist':
      case 'music_playlist':
      case 'article':
      case 'artarticle':
      case 'avp_video':
      case 'ynmusic_album':
      case 'list_listing':
      case 'document':
      case 'store_product':
        $of = "{$b}.owner_id";
      break;
      default:
        $of = "{$b}.user_id";
      break;
    }

    if (!empty($friendIds)) {
      $rank .= " + ( (CASE WHEN {$of} IN (".implode(',', $friendIds).") THEN 1 ELSE 0 END) * 40 ))";
    }
    else {
      $rank .= ")";
    }


    if (count($adminRecs) < $limit) {
      $limit = $limit - count($adminRecs);
      $select = $table->select()
        ->setIntegrityCheck(false)
        ->from($m, array(
          "{$b}.*",
          'rank' => (new Zend_Db_Expr($rank))
        ))
        ->joinLeft($b, "{$m}.user_id = {$of}", array())
        ->joinLeft($l, "{$b}.".$primary." = {$l}.resource_id AND {$l}.resource_type = '".$type."'", array())
        ->where("{$of} <> ?", $user)
        ->where("{$b}.".$primary." IS NOT NULL")
        ->group("{$b}.".$primary)
        ->order("rank DESC")
        ->limit($limit);

      if (!empty($adminRecIds)) {
        $select
          ->where("{$b}.".$primary." NOT IN (?)", $adminRecIds);
      }

      if (!empty($rejIds)) {
        $select
          ->where("{$b}.".$primary." NOT IN (?)", $rejIds);
      }

      if (!empty($except)) {
        $select
          ->where("{$b}.".$primary." NOT IN (?)", $except);
      }

      if ($type == 'user' && !empty($friendIds)) {
        $select
          ->where("{$of} NOT IN (?)", $friendIds);
      }

      if ($type == 'event'){

        $tableNameEm = Engine_Api::_()->getDbTable('membership', 'event')->info('name');

        $where = "{$b}.{$primary} = {$tableNameEm}.resource_id AND {$tableNameEm}.user_id = {$user}";

        $select
            ->joinLeft($tableNameEm, $where, array())
            ->where("ISNULL({$tableNameEm}.active) OR {$tableNameEm}.active = 0");

      } else if ($type == 'group'){

        $tableNameEm = Engine_Api::_()->getDbTable('membership', 'group')->info('name');

        $where = "{$b}.{$primary} = {$tableNameEm}.resource_id AND {$tableNameEm}.user_id = {$user}";

        $select
            ->joinLeft($tableNameEm, $where, array())
            ->where("ISNULL({$tableNameEm}.active) OR {$tableNameEm}.active = 0");

      }
       
      $recs = $table->fetchAll($select);

    } else {
      $recs = null;
    }

    $recs = isset($recs) ? $recs : null;

    $items = array(
      'admin' => $adminRecs,
      'user' => $recs
    );

    if (APPLICATION_ENV == 'production') {
      $cache->save($items, $id);
      $cache->setLifetime(3600);
    }

    return $items;
  }

  public function getSuggestItems(array $params)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    switch ($params['object_type']) {
      case 'user':
        return $this->getNotMutualFriends($params);
      break;
      default:
        return $this->getFriends($params);
      break;
    }
  }

  public function getSuggestItemsDisabled(array $params)
  {
    switch ($params['object_type']) {
      case 'user':
        return $this->getNotMutualFriendsDisabled($params);
      break;
      default:
        return $this->getFriendsDisabled($params);
      break;
    }
  }

  public function suggest($from, $to, $object)
  {
    if (is_array($from)) {
      $from = Engine_Api::_()->getItem('user', $from[0]);
    } elseif (is_numeric($from)) {
      $from = Engine_Api::_()->getItem('user', $from);
    } else {
      $from = Engine_Api::_()->user()->getViewer();
    }

    if (!( $from instanceof User_Model_User )) {
      return false;
    }

    $tos = array();
    if ( $to instanceof User_Model_User ) {
      $tos[] = $to;
    } elseif (is_numeric($to)) {
      $tos[] = Engine_Api::_()->getItem('user', (int)$to);
    } elseif (is_array($to)) {
      $tos = Engine_Api::_()->getItemMulti('user', $to);
    } else {
      return false;
    }

    if (empty($tos)) {
      return false;
    }

    $object = $this->getItemByInfo($object);

    if (!$object || !$object->getIdentity()) {
      return false;
    }

    $suggestTable = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $db = $suggestTable->getAdapter();
    $db->beginTransaction();
    try {
      foreach ($tos as $to_id) {
        if (!($to_id instanceof User_Model_User) || !$to_id->getIdentity()) {
          continue ;
        }
        $this->suggestItem($from, $to_id, $object);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return true;
  }

  public function isSuggested(User_Model_User $from, User_Model_User $to, Core_Model_Item_Abstract $object)
  {
    $from_id = $from->getIdentity();
    $to_id = $to->getIdentity();
    $object_id = $object->getIdentity();
    $object_type = $object->getType();

    return $this->isSuggestedByInfo($from_id, $to_id, $object_type, $object_id);
  }

  public function isSuggestedByInfo($from_id, $to_id, $object_type, $object_id)
  {
    if (!$object_id || !$to_id || !$from_id || !$object_type) {
      return null;
    }

    $suggestTable = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $select = $suggestTable->select()
      ->where('object_id = ?', $object_id)
      ->where('object_type = ?', $object_type)
      ->where('to_id = ?', $to_id)
      ->where('from_id = ?', $from_id);

    return (bool)$suggestTable->getAdapter()->fetchOne($select);
  }

  public function suggestItem(User_Model_User $from, User_Model_User $to, Core_Model_Item_Abstract $object)
  {
    $from_id = $from->getIdentity();
    $to_id = $to->getIdentity();
    $object_id = $object->getIdentity();
    $object_type = $object->getType();

    if (!$object_id || !$to_id || !$from_id || !$object_type) {
      return false;
    }

    if ($this->isSuggestedByInfo($from_id, $to_id, $object_type, $object_id)) {
      return false;
    }

    if (in_array($object_type, array('user', 'group', 'event'))) {
      if ($this->isMember($object_type, $object_id, $to_id)) {
        return false;
      }
    }

    $suggestTable = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $notifyTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notificationType = 'suggest_' . $object_type;
    
    $select = $suggestTable->select()
      ->where('object_type = ?', $object_type)
      ->where('object_id = ?', $object_id)
      ->where('to_id = ?', $to_id);
    $row = $suggestTable->fetchRow($select);
    if ($row) {
      $select = $notifyTable->select()
        ->where('object_type = ?', 'suggest')
        ->where('type = ?', $notificationType)
        ->where('object_id = ?', $row->getIdentity())
        ->where('user_id = ?', $to_id);
      $notification = $notifyTable->fetchRow($select);
      if ($notification) {
        $notification->delete();
      }
    }

    $suggest = $suggestTable->createRow();

    $suggest->object_type = $object_type;
    $suggest->object_id = $object_id;
    $suggest->from_id = $from_id;
    $suggest->to_id = $to_id;
    $suggest->suggest_date = date('Y-m-d H:i:s');
    $suggest->save();

    $notifyTable->addNotification($to, $from, $suggest, $notificationType);

    return true;
  }

  public function getFriendsDisabled($params)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $userTable = Engine_Api::_()->getItemTable('user');
    $UTname = $userTable->info('name');
    $suggestTable = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $STname = $suggestTable->info('name');

    $select = $userTable->select()
      ->setIntegrityCheck(false)
      ->from($UTname, 'user_id')
			->joinInner($STname, $STname.'.to_id = '.$UTname.'.user_id', array())
      ->where($STname.'.from_id = ?', $viewer->getIdentity())
			->where($STname.'.object_id = ?', $params['object_id'])
			->where($STname.'.object_type = ?', $params['object_type']);

    return $userTable->getAdapter()->fetchCol($select);
  }

  public function getFriends($params)
  {
    if ($params instanceof User_Model_User) {
      $user = $this->getItemInfo($params);
    } elseif (is_array($params)) {
      $user = array('id' => Engine_Api::_()->user()->getViewer()->getIdentity());
    }

    if (!$user['id']) {
      return false;
    }

    $userTable = Engine_Api::_()->getItemTable('user');
    $UTname = $userTable->info('name');
    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $MTname = $membershipTable->info('name');

    $owner = Engine_Api::_()->getItem($params['object_type'], $params['object_id'])->getOwner();

    $select = $userTable->select()
      ->setIntegrityCheck(false)
      ->from($UTname)
			->joinLeft($MTname, $MTname.'.user_id = '.$UTname.'.user_id', array())
			->where($MTname.'.resource_id = ?', $user['id'])
			->where($MTname.'.resource_approved = 1')
      ->where($MTname.'.user_id NOT IN (?)', array($user, $owner->getIdentity()))
			->where($MTname.'.user_approved = 1');

    if (isset($params['keyword']) && !empty($params['keyword'])) {
      $select->where( '(' . $UTname . '.displayname LIKE ? OR '.$UTname .'.username LIKE ?)', "%{$params['keyword']}%");
    }

    return Zend_Paginator::factory($select);
  }

  public function getNotMutualFriendsDisabled($params)
  {
    $user1 = Engine_Api::_()->user()->getViewer()->getIdentity();
    $user2 = $params['object_id'];

    if (!$user1 || !$user2) {
      return false;
    }

    $userTable = Engine_Api::_()->getItemTable('user');

    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $MTname = $membershipTable->info('name');
    $suggestTable = Engine_Api::_()->getDbTable('suggests', 'suggest');
    $STname = $suggestTable->info('name');

    $db = $membershipTable->getAdapter();
    $select = new Zend_Db_Select($db);
    $select
      ->from($MTname, 'user_id')
      ->joinLeft($MTname, "`{$MTname}`.`user_id`=`{$MTname}_2`.user_id", null)
      ->joinInner($STname, $STname.'.to_id = '.$MTname.'.user_id', array())
      ->where("`{$MTname}`.resource_id = ?", $user1)
      ->where("`{$MTname}_2`.resource_id <> ?", $user2)
      ->where("`{$MTname}`.active = ?", 1)
      ->where("`{$MTname}_2`.active = ?", 1)
      ->where($STname.'.from_id = ?', $user1)
			->where($STname.'.object_id = ?', $user2)
			->where($STname.'.object_type = ?', 'user');

    $uids = $db->fetchCol($select);
    if (count($uids) == 0) {
      return array();
    }

    return $uids;
  }

  public function getNotMutualFriends(array $params)
  {
    $user1 = Engine_Api::_()->user()->getViewer()->getIdentity();
    $user2 = (int)$params['object_id'];

    if (!$user1 || !$user2) {
      return false;
    }

    $userTable = Engine_Api::_()->getItemTable('user');

    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $MTname = $membershipTable->info('name');

    $db = $membershipTable->getAdapter();
    $select = new Zend_Db_Select($db);
    $select
      ->from($MTname, 'user_id')
      ->join($MTname, "`{$MTname}`.`user_id`=`{$MTname}_2`.user_id", null)
      ->where("`{$MTname}`.resource_id = ?", $user1)
      ->where("`{$MTname}_2`.resource_id = ?", $user2)
      ->where("`{$MTname}`.active = ?", 1)
      ->where("`{$MTname}_2`.active = ?", 1);

    $uids = $db->fetchCol($select);

    $select = $membershipTable->select()->setIntegrityCheck(false);
    $select
      ->from($MTname, array('user_id'))
      ->where('resource_id = ?', $user1)
      ->where('resource_approved = ?', 1)
      ->where('user_approved = ?', 1)
      ->where('active = ?', 1)
      ->group('user_id');

    if (!empty($uids)) {
      $select
        ->where('user_id NOT IN (?)', $uids);
    }
    $uids = $db->fetchCol($select);

    if (empty($uids)) {
      return array();
    }

    $select3 = $membershipTable->select()->setIntegrityCheck(false);
    $select3
      ->from($MTname, array('user_id'))
      ->where('resource_id = ?', $user2)
      ->where('user_id <> ?', $user1)
      ->where('resource_approved = ?', 1)
      ->where('user_approved = ?', 1)
      ->where('active = ?', 1)
      ->group('user_id');
    $uids1 = $db->fetchCol($select3);
    
    if (!empty($uids1)) {
      $select4 = $membershipTable->select()->setIntegrityCheck(false);
      $select4
        ->from($MTname, array('user_id'))
        ->where('resource_id IN (?)', $uids1)
        ->where('resource_approved = ?', 1)
        ->where('user_approved = ?', 1)
        ->where('user_id IN (?)', $uids)
        ->where('user_id NOT IN (?)', $uids1)
        ->where('active = ?', 1);

      $uids2 = $db->fetchCol($select4);
      $select2 = $userTable->select()
        ->where('user_id <> ?', $user1)
        ->where('user_id NOT IN (?)', $uids1)
        ->where('user_id <> ?', $user2);

      if (!empty($uids2)) {
        $select2
          ->where('user_id IN (?)', $uids2);
      } else {
        $select2
          ->where('FALSE');
      }

      if (!empty($params['keyword'])) {  
        $select2
          ->where('(displayname LIKE ? OR username LIKE ?)', "%{$params['keyword']}%", "%{$params['keyword']}%");
      }

      $potential = Zend_Paginator::factory($select2);
      $potential->setItemCountPerPage(30);
    } else {
      $potential = Zend_Paginator::factory(array());
    }

    $select = $userTable->select()
      ->where('user_id IN (?)', $uids)
      ->where('user_id <> ?', $user1)
      ->where('user_id <> ?', $user2);

    if (!empty($uids2)) {
      $select
        ->where('user_id NOT IN (?)', $uids2);
    }

    if (!empty($params['keyword'])) {
      $select
        ->where('(displayname LIKE ? OR username LIKE ?)', "%{$params['keyword']}%", "%{$params['keyword']}%");
    }

    $all = Zend_Paginator::factory($select);
    $all->setItemCountPerPage(9);


    $page = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('p', 0);

    if ($page > 1) {
      $all->setCurrentPageNumber($page);
      $potential = array();
    }

    return array(
      'all' => $all,
      'potential' => $potential
    );
  }

  public function getItemByInfo($info, $type = 'user')
  {
    if (is_array($info)) {
      $info = Engine_Api::_()->getItem($info['type'], $info['id']);
    } elseif (is_numeric($info)) {
      $info = Engine_Api::_()->getItem($type, $info);
    }

    if (!($info instanceof Core_Model_Item_Abstract)) {
      return false;
    }

    return $info;
  }

  public function getItemInfo($info)
  {
    if ($info instanceof Core_Model_Item_Abstract) {
      $info = array('type' => $info->getType(), 'id' => $info->getIdentity());
    } elseif (is_numeric($info)) {
      $info = array('type' => 'user', 'id' => (int)$info);
    }

    if (!is_array($info)) {
      return false;
    }

    return $info;
  }

  public function maintainSettings($key, &$value)
  {
    if ( is_array($value) ) {
      foreach ($value as $subkey => $subvalue) {
        return $this->maintainSettings($key . '_' . $subkey, $subvalue);
      }
    } elseif (is_numeric($value) && $value) {
      return $key;
    }

    return '';
  }

  public function getMixItems()
  {
    $itemTypes = array_keys($this->getItemTypes());
    $itemTypes[] = 'user';

    $mixValues = array();
    $values = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('suggest.mix', array());

    foreach ($values as $key => $value) {
      $key = $this->maintainSettings($key, $value);
      if ($value && $key) {
        $mixValues[] = $key;
      }
    }
    return array_intersect($mixValues, $itemTypes);
  }

  public function checkInitFbApp()
  {
    $init_fb_app = true;

    // facebooksepage
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $modulesSel = $modulesTbl->select()
      ->where('name = ?', 'facebooksepage')
      ->where('enabled = ?', 1);

    if ($modulesTbl->fetchRow($modulesSel)) {
      return false;
    }

    // socialdna
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $modulesSel = $modulesTbl->select()
      ->where('name = ?', 'socialdna')
      ->where('enabled = ?', 1);

    if ($modulesTbl->fetchRow($modulesSel)) {
      $contentTbl = Engine_Api::_()->getDbTable('content', 'core');
      $contentSel = $contentTbl->select()
        ->where('name = ?', 'socialdna.boot');

      $init_fb_app = !($contentTbl->fetchRow($contentSel));
    }

    return $init_fb_app;
  }
}