<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 28.11.11 18:04 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Widget_ProfileCheckinsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }
    }

    if (!$subject) {
      return $this->setNoRender();
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    // Get some options
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $this->view->updateSettings   = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes     = $request->getParam('viewAllLikes',    $request->getParam('show_likes',    false));
    $this->view->viewAllComments  = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate        = $request->getParam('getUpdate');
    $this->view->checkUpdate      = $request->getParam('checkUpdate');
    $this->view->action_id        = (int) $request->getParam('action_id');
    $this->view->comment_pagination = $request->getParam('comment_pagination', false);

    if( $feedOnly ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if( $length > 50 ) {
      $this->view->length = $length = 50;
    }

    $config = array(
      'action_id' => (int) $request->getParam('action_id'),
      'max_id'    => (int) $request->getParam('maxid'),
      'min_id'    => (int) $request->getParam('minid'),
      'limit'     => (int) $length
    );

    if (!empty($subject)) {
      $config['items'] = array(array('type' => $subject->getType(), 'id' => $subject->getIdentity()));
    }

    // Lists
    if (empty($subject) && $viewer->getIdentity()) {

      $list_params = array(
        'mode' => 'recent',
        'list_id' => 0,
        'type' => ''
      );


      if ($request->getParam('mode')) {
        $list_params['mode'] = $request->getParam('mode', 'recent');
        $list_params['list_id'] = $request->getParam('list_id');
        $list_params['type'] = $request->getParam('type');
      }

      $this->view->list_params = $list_params;
    }

    $checkinTable = Engine_Api::_()->getDbtable('checks', 'checkin');
    $placesTable = Engine_Api::_()->getDbtable('places', 'checkin');

    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();

    $grouped_actions = array();
    $group_types = array('friends', 'like_item_private');

    $matchedCheckinsCount = array();
    $placeIds = array();

    do {
      $actions = $checkinTable->getCheckins($subject->getIdentity(), $config);
      $selectCount++;

      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      if (count($actions) > 0) {

        foreach ($actions as $action) {
          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          if( null === $firstid || $action->action_id > $firstid ) {
            $firstid = $action->action_id;
          }

          if (!$action->getTypeInfo()->enabled) continue;

          if (!$action->getSubject() || !$action->getSubject()->getIdentity()) continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity()) continue;

          $placeIds[] = $action->place_id;

          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          if (in_array($action->type, $group_types)){

            $subject_guid = $action->getSubject()->getGuid();
            $total_guid = $action->type . '_' . $subject_guid;

            if (!isset($grouped_actions[$total_guid])){
              $grouped_actions[$total_guid] = array();
            }
            $grouped_actions[$total_guid][] = $action->getObject();

            if (count($grouped_actions[$total_guid]) > 1){
              continue ;
            }

          }

          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            continue;
          }

          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }

    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);

    /**
     * @var $activityChecks Engine_Db_Table_Rowset
     */
    $activityChecks = $placesTable->getDetailedPlaces($placeIds);

    foreach ($activity as $key => $action) {
      if ($place = $activityChecks->getRowMatching(array('place_id' => $action->place_id))) {
        $action->checkin = $place;
      }
      if (in_array($action->type, $group_types)) {

        $subject_guid = $action->getSubject()->getGuid();
        $total_guid = $action->type . '_' . $subject_guid;

        if (isset($grouped_actions[$total_guid])) {
          foreach ($grouped_actions[$total_guid] as $item) {
            $activity[$key]->grouped_subjects[] = $item;
          }
        }
      }
    }

    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = (int) $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;

    if( !empty($subject) ) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if( $viewer->getIdentity() && !$this->_getParam('action_id') ) {
      if( !$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer)) ) {
        if( Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status') ) {
          $this->view->enableComposer = true;
        }
      } else if( $subject ) {
        if( Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment') ) {
          $this->view->enableComposer = true;
        }
      }
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->view->last_id = $last_id = $config['max_id'];

    if (!$last_id) {
      $activity = $checkinTable->getCheckins($subject->getIdentity(), array());

      $markers = $checkinTable->getMarkers($activity);
      $bounds = Engine_Api::_()->checkin()->getMapBounds($markers);
      $this->view->markers = (!empty($markers)) ? Zend_Json_Encoder::encode($markers) : '';
      $this->view->bounds = Zend_Json_Encoder::encode($bounds);
    }

    if (!$feedOnly) { // no ajax
      // Instance
      $unique = rand(11111, 99999);
      $this->view->feed_uid = 'wall_' . $unique;

      // Composers
      $composePartials = array();
      foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $type => $config){
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          continue;
        }
        $composePartials[$type] = $config['script'];
      }
      $this->view->composePartials = $composePartials;
    }
  }
}