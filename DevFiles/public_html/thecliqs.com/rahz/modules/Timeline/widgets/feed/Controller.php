<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Widget_FeedController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {
//      $db = Engine_Db_Table::getDefaultAdapter();
//      $db->setProfiler(array(
//        'class' => 'Zend_Db_Profiler_Firebug',
//        'enabled' => true
//      ));
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;

        /**
         * @var $subject Timeline_Model_User
         */

        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();

            if ($subject instanceof User_Model_User) {
                $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
            }

            if (!$subject->authorization()->isAllowed($viewer, 'view') || !($subject instanceof User_Model_User)) //!in_array($subject->getType(), Engine_Api::_()->timeline()->getSupportedItems()))
            {
                return $this->setNoRender();
            }
        }

        $this->view->subject = $subject;

        $request = Zend_Controller_Front::getInstance()->getRequest();
        // Get some options
        $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
        $this->view->composerOnly = $composerOnly = $this->_getParam('composerOnly', false);
        $this->view->length = $length = $request->getParam('limit', 10 /*Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 10)*/);
        $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

        $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
        $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
        $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
        $this->view->getUpdate = $request->getParam('getUpdate');
        $this->view->checkUpdate = $request->getParam('checkUpdate');
        $this->view->action_id = (int)$request->getParam('action_id');
        $this->view->comment_pagination = $request->getParam('comment_pagination', false);


        $userSetting = null;
        if ($viewer->getIdentity()) {
            $userSetting = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);
        }
        $this->view->userSetting = $userSetting;

        $customStreamClass = null;

        if ($feedOnly) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        if ($length > 50) {
            $this->view->length = $length = 50;
        }
        $config = array(
            'action_id' => (int)$request->getParam('action_id'),
            'max_id' => (int)$request->getParam('maxid', 0),
            'min_id' => (int)$request->getParam('minid', 0),
            'max_date' => $request->getParam('maxdate'),
            'min_date' => $request->getParam('mindate'),
            'limit' => (int)$length
        );


        // get mute actions
        if ($viewer->getIdentity()) {
            $config['hideIds'] = Engine_Api::_()->getDbTable('mute', 'wall')->getActionIds($viewer);
        }

        $birthdate = $subject->getBirthdate();
        if (!isset($config['min_date']) || (strtotime($config['min_date']) < strtotime($birthdate))) {
            $time = strtotime($birthdate) - 1;
            $config['min_date'] = date('Y-m-d H:i:s', $time);
        }


        // Lists
        if (empty($subject) && $viewer->getIdentity()) {

            $list_params = array(
                'mode' => 'recent',
                'list_id' => 0,
                'type' => ''
            );

            $userSetting = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);

            $default_type = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.default');
            if ($default_type != '') {
                $list_params['mode'] = 'type';
                $list_params['type'] = $default_type;

                if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)) {
                    $userSetting->getParams();
                    $list_params['mode'] = $userSetting->mode;
                    $list_params['type'] = $userSetting->type;
                    $list_params['list_id'] = $userSetting->list_id;
                }

            }
            if ($request->getParam('mode')) {

                if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)) {
                    $userSetting->setParams($request);
                }

                $list_params['mode'] = $request->getParam('mode', 'recent');
                $list_params['list_id'] = $request->getParam('list_id');
                $list_params['type'] = $request->getParam('type');
            }

            $this->view->list_params = $list_params;

            if ($list_params['mode'] == 'type') {

                try {

                    $types = Engine_Api::_()->wall()->getManifestType('wall_type');

                    if (in_array($list_params['type'], array_keys($types))) {
                        $typeClass = Engine_Api::_()->loadClass(@$types[$list_params['type']]['plugin']);
                        if ($typeClass instanceof Wall_Plugin_Type_Abstract) {
                            $config['items'] = $typeClass->getItems($viewer);
                            $config['showTypes'] = $typeClass->getTypes($viewer);
                            if ($typeClass->customStream) {
                                $customStreamClass = $typeClass;
                            }
                        }
                    }

                } catch (Exception $e) {
                }

            } else if ($list_params['mode'] == 'list' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)) {

                $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_params['list_id']);
                if ($list) {
                    $config['items'] = $list->getItems();
                }

            } else if ($list_params['mode'] == 'friendlist' && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.frendlistenable', true)) {

                $list = Engine_Api::_()->getDbTable('lists', 'user')->fetchRow(array('list_id = ?' => $list_params['list_id']));

                if ($list) {

                    $table = Engine_Api::_()->getDbTable('users', 'user');
                    $select = $table->select()
                        ->from(array('li' => Engine_Api::_()->getDbTable('listItems', 'user')->info('name')), array())
                        ->join(array('u' => $table->info('name')), 'li.child_id = u.user_id', new Zend_Db_Expr('u.*'))
                        ->where('li.list_id = ?', $list->getIdentity());

                    $data = array();
                    foreach ($table->fetchAll($select) as $item) {
                        $data[] = array('type' => $item->getType(), 'id' => $item->getIdentity());
                    }

                    $config['items'] = $data;
                }

            }

            $this->view->types = array_keys(Engine_Api::_()->wall()->getManifestType('wall_type'));
            $this->view->lists = Engine_Api::_()->getDbTable('lists', 'wall')->getPaginator($viewer);
            $this->view->friendlists = Engine_Api::_()->getDbTable('lists', 'user')->fetchAll(array('owner_id = ?' => $viewer->getIdentity()));

        }


        /**
         * @var $actionTable Timeline_Model_DbTable_Actions
         * @var $action Timeline_Model_Action
         */
        $actionTable = Engine_Api::_()->getDbtable('actions', 'timeline');

        $selectCount = 0;
        $lastid = null;
        $firstid = null;

        $lastdate = null;
        $firstdate = null;

        $tmpConfig = $config;
        $activity = array();
        $timelineData = array();
        $endOfFeed = false;

        $friendRequests = array();
        $itemActionCounts = array();

        $grouped_actions = array();
        $group_types = array('friends', 'like_item_private');
        $tmp_action_ids = array();

        $this->view->feed_config = array();

        do {
          if (!empty($subject)){
            $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
          } else {
            $actions = $actionTable->getActivity($viewer, $tmpConfig);
          }

            $selectCount++;
            if (count($actions) < $length || count($actions) <= 0) {
                $endOfFeed = true;
            }

            if (count($actions) > 0) {

                foreach ($actions as $action) {

                    if (in_array($action->action_id, $tmp_action_ids)) {
                        continue;
                    }

                    $tmp_action_ids[] = $action->action_id;

                    if (count($tmp_action_ids) > $length) {
                        $endOfFeed = false;
                    }

                    if (count($activity) >= $length) {
                        break;
                    }

                    if (
                        null === $lastdate ||
                        strtotime($action->date) < strtotime($lastdate) ||
                        (strtotime($action->date) == strtotime($lastdate) && $action->action_id < $lastid)
                    ) {
                        $lastdate = $action->date;
                        $lastid = $action->action_id;
                    }
                    if (
                        null === $firstdate ||
                        strtotime($action->date) > strtotime($firstdate) ||
                        (strtotime($action->date) == strtotime($firstdate) && $action->action_id > $firstid)
                    ) {
                        $firstdate = $action->date;
                        $firstid = $action->action_id;
                    }

                    if ($composerOnly) {
                        continue;
                    }

                    if (
                        !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ||
                        !$action->getSubject() || !$action->getSubject()->getIdentity() ||
                        !$action->getObject() || !$action->getObject()->getIdentity()
                    ) {
                        continue;
                    }

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

                    if (in_array($action->type, $group_types)) {

                        $subject_guid = $action->getSubject()->getGuid();
                        $total_guid = $action->type . '_' . $subject_guid;

                        if (!isset($grouped_actions[$total_guid])) {
                            $grouped_actions[$total_guid] = array();
                        }
                        $grouped_actions[$total_guid][] = $action->getObject();

                        if (count($grouped_actions[$total_guid]) > 1) {
                            continue;
                        }

                    }

                    try {
                        $attachments = $action->getAttachments();
                    } catch (Exception $e) {
                        continue;
                    }

                    if (count($activity) < $length) {
                        $time = strtotime($action->date);
                        $year = date('Y', $time);
                        $month = date('m', $time);
                        $day = date('d', $time);
                        $timelineData['y' . $year]['m' . $month]['d' . $day][] = array(
                            'id' => $action->action_id,
                            'year' => $year,
                            'month' => $month,
                            'date' => $action->date
                        );

                        $activity[] = $action;
                        if (count($activity) == $length) {
                            $actions = array();
                        }
                    }
                }
            }

            if ($lastid) {
                $tmpConfig['max_id'] = $lastid;
            }
            if ($lastdate) {
                $tmpConfig['max_date'] = $lastdate;
            }
            if (!empty($tmpConfig['action_id'])) {
                $actions = array();
            }

        } while (count($activity) < $length && $selectCount <= 2 && !$endOfFeed);


        foreach ($activity as $key => $action) {

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
        $this->view->timelineData = $timelineData;
        $this->view->activityCount = count($activity);
        $this->view->lastid = (int)$lastid;
        $this->view->firstid = $firstid;
        $this->view->lastdate = $lastdate;
        $this->view->firstdate = $firstdate;
        $this->view->endOfFeed = $endOfFeed;

        //@todo temporary solution for Born Activity. Realize it for all Life Events
        $this->view->showBorn = ($request->getParam('mindate', 0)) ? 0 : 1;
        if(!$subject->getBirthdate())
            $this->view->showBorn = false;

        if (!empty($subject)) {
            $this->view->subjectGuid = $subject->getGuid(false);
        }

        $this->view->enableComposer = false;
        if ($viewer->getIdentity() && !$this->_getParam('action_id')) {
            if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
                if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
                    $this->view->enableComposer = true;
                }
            } else if ($subject) {
                if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
                    $this->view->enableComposer = true;
                }
            }
        }

        if (!$this->view->enableComposer && $composerOnly) {
            return $this->setNoRender();
        }
        ;

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);

        if (!$feedOnly) { // no ajax

            // Instance
            $unique = rand(11111, 99999);
            $this->view->feed_uid = 'wall_' . $unique;

            $composers_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.composers.disabled', 'smile'));

            // Composers
            $composePartials = array();
            foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $type => $config) {
                if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                    continue;
                }
                if (in_array($type, $composers_disabled)) {
                    continue;
                }
                $composePartials[$type] = $config['script'];
            }
            $this->view->composePartials = $composePartials;
        }
        // Composer Privacy

        $this->view->allowPrivacy = false;
        $this->view->privacy_type = $privacy_type = ($subject) ? $subject->getType() : 'user';

        $privacy = array();
        $privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled', ''));
        foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item) {
            if (in_array($privacy_type . '_' . $item, $privacy_disabled)) {
                continue;
            }
            $privacy[] = $item;
        }
        $this->view->privacy = $privacy;

        if ($viewer->getIdentity() && $privacy) {

            $this->view->allowPrivacy = true;
            $this->view->privacy_active = (empty($privacy[0])) ? null : $privacy[0];

            $last_privacy = Engine_Api::_()->getDbTable('userSettings', 'wall')->getLastPrivacy($subject, $viewer);
            if ($last_privacy && in_array($last_privacy, $privacy)) {
                $this->view->privacy_active = $last_privacy;
            }

        }

        $this->view->fbpage_id = 0;
        if ($subject && $subject->getType() == 'page') {
            $fbpage_id = Engine_Api::_()->wall()->getPageLink($viewer, $subject->getIdentity());
            if (!empty($fbpage_id)) {
                $this->view->fbpage_id = $fbpage_id;
            }
        }


//      $this->view->content()->renderWidget('activity.feed');
//
//
//
//      $queries = array();
//      foreach ($db->getProfiler()->getQueryProfiles() as $item){
//        $queries[] = $item;
//      }
//
//      function sortQuery($a, $b)
//      {
//        return $a->getElapsedSecs() < $b->getElapsedSecs() ;
//      }
//
//      usort($queries, "sortQuery");
//
//      print_arr( $db->getProfiler()->getTotalNumQueries(). ' @ '.$db->getProfiler()->getTotalElapsedSecs() );
//      $results = array();
//      foreach ($queries as $query){
//        $results[] = array($query->getElapsedSecs(), $query->getQuery());
//      }
//      print_die($results);

    }
}
