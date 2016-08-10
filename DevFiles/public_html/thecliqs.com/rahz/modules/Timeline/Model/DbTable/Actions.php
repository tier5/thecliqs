<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Actions.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Model_DbTable_Actions extends Wall_Model_DbTable_Actions
{
  protected $_rowClass = 'Timeline_Model_Action';
  protected $_name = 'activity_actions';

  public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user,
                                   array $params = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // params
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int) $params['limit'];
    $max_id = (empty($params['max_id'])) ? null : (int) $params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int) $params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];
    $min_date = (empty($params['min_date'])) ? null : $params['min_date'];
    $max_date = (empty($params['max_date'])) ? null : $params['max_date'];

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
      'about' => $about
    ));
    $responses = (array) $event->getResponses();

    if( empty($responses) ) {
      return null;
    }


    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
        ->from($tableTypes->info('name'), array('type', 'displayable'))
        ->where('enabled = 1')
        ->where('displayable & 1 OR displayable & 2')
        ->where('module IN (?)', Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames());


    $total_types = $tableTypes->fetchAll($select);
    $types = array();
    $subjectActionTypes = array(0);
    $objectActionTypes = array(0);

    foreach ($total_types as $item){
      $types[] = $item->type;
      if( $item->displayable & 1 ) {
        $subjectActionTypes[] = $item->type;
      }
      if( $item->displayable & 2 ) {
        $objectActionTypes[] = $item->type;
      }
    }

    if (!empty($showTypes) && is_array($showTypes)){
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)){
      $types = array_diff($types, $hideTypes);
    }


    if (empty($types)){
      return null;
    }


    $tableStream = Engine_Api::_()->getDbTable('stream', 'activity');

    $where = '0';
    foreach ($responses as $response){

      $where .= ' OR (target_type = "'.$response['type'].'" AND ';

      if( empty($response['data']) ) {
        $where .= 'target_id = 0';
      } else if( is_scalar($response['data']) || count($response['data']) === 1 ) {
        if( is_array($response['data']) ) {
          list($response['data']) = $response['data'];
        }
        $where .= 'target_id = ' . $response['data'];
      } else if( is_array($response['data']) ) {
        $where .= 'target_id IN (' . implode(",", (array) $response['data']) . ')';
      } else {
        continue;
      }

      $where .= ')';

    }

    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $select = $actionTable->select()
        ->setIntegrityCheck(false)
        ->from(array('s' => $tableStream->info('name')), array())
        ->join(array('a' => $actionTable->info('name')), 'a.action_id = s.action_id', array('action_id'))
        ->where(new Zend_Db_Expr($where));


    $select
        ->where('s.type IN (?)', $types);

    if (null !== $min_date) {
      $select->where('(a.date > ?) || (a.date = ? && a.action_id > ' . (int)$min_id . ') ', $min_date);
    }
    if (null !== $max_date) {
      $select->where('(a.date < ?) || (a.date = ? && a.action_id < ' . (int)$max_id . ') ', $max_date);
    }

    if (!empty($hideIds) && is_array($hideIds)){
      $select->where('a.action_id NOT IN (?)', $hideIds);
    }

    $select->where(new Zend_Db_Expr("(a.subject_type = '".$about->getType()."' AND a.subject_id = ".$about->getIdentity()." AND s.type IN ('".implode("','", $subjectActionTypes)."') ) OR (a.object_type = '".$about->getType()."' AND a.object_id = ".$about->getIdentity()." AND s.type IN ('".implode("','", $objectActionTypes)."') )"));




    if ($about->getType() == 'user'){
      if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')){
        $data = Engine_Api::_()->getDbtable('membership', 'page')->getMembershipsOfIds($about);
        if (!empty($data)){
          $select->where('!(a.object_type = "page" AND a.object_id IN (?))', $data);
        }
      }
    }

    $select->group('s.action_id');
    $db = Engine_Db_Table::getDefaultAdapter();

    $union = $db->select();

    $union->union(array('(' . $select->__toString() . ')'));
    if ($about->getType() == 'user'){

      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');


      // friends
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();;
      if (!empty($data)){
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
        (t.object_type = "user" AND t.object_id = ' . $about->getIdentity() . ')
        AND
        (
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
        )
      ';

      $selectTag = $tableTag->select()
          ->setIntegrityCheck(false)
          ->from(array('t' => $tableTag->info('name')), array())
          ->join(array('a' => $actionTable->info('name')), 'a.action_id = t.action_id', array('a.action_id'))
          ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = a.action_id', array())
          ->where(new Zend_Db_Expr($tagWhere))

      ;

/*      $selectTag
          ->where('a.type IN (?)', $types);*/


      if (null !== $min_date) {
        $selectTag->where('(a.date > ?) || (a.date = ? && a.action_id > ' . (int)$min_id . ') ', $min_date);
      }
      if (null !== $max_date) {
        $selectTag->where('(a.date < ?) || (a.date = ? && a.action_id < ' . (int)$max_id . ') ', $max_date);
      }


/*      if( null !== $min_id ) {
        $selectTag->where('a.action_id >= ?', $min_id);
      } else if( null !== $max_id ) {
        $selectTag->where('a.action_id <= ?', $max_id);
      }*/

      if (!empty($hideIds) && is_array($hideIds)){
        $selectTag->where('a.action_id NOT IN (?)', $hideIds);
      }
      $selectTag->group('t.action_id');

      $union->union(array('(' . $selectTag->__toString() . ')'));
    }


    $union->order('action_id DESC')
        ->limit($limit);
//    print_die($union->__toString());
    $actions = $db->fetchAll($union);

    if (empty($actions)){
      return null;
    }

    $ids = array();
    foreach ($actions as $data){
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);
    return $this->fetchAll(
      $this->select()
          ->where('action_id IN(' . join(',', $ids) . ')')
          ->group('action_id')
          ->order('date DESC')
          ->order('action_id DESC')
          ->limit($limit)
    );
  }




  protected function _getInfo(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
      'max_date' => null,
      'min_date' => null,
      'showTypes' => null,
      'hideTypes' => null,
      'hideIds' => null,
    );

    $newParams = array();
    foreach ($args as $arg => $default) {
      if (!empty($params[$arg])) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }



}