<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 02.02.12 14:14 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_CreateItemsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $user = Engine_Api::_()->user()->getViewer();

    if (!$user->getIdentity()) {
      return $this->setNoRender();
    }

    $modules = array(
     //module => action type
      'album' => 'album_photo_new',
      'article' => 'article_new',
      'blog' => 'blog_new',
      'donation' => 'donation_charity_new',
      'event' => 'event_create',
      'forum' => 'forum_topic_create',
      'group' => 'group_create',
      'inviter' => 'invite',
      'music' => 'music_playlist_song',
      'page' => 'page_create',
      'poll' => 'poll_new',
      'video' => 'video'
    );

    /**
     * @var $modulesTbl Core_Model_DbTable_Modules
     * @var $creditsTbl Credit_Model_DbTable_Logs
     * @var $actionTypesTbl Credit_Model_DbTable_ActionTypes
     * @var $menusApi Core_Api_Menus
     */

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $creditsTbl = Engine_Api::_()->getDbTable('logs', 'credit');
    $actionTypesTbl = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $menusApi = Engine_Api::_()->getApi('menus', 'core');
    $navigation = new Zend_Navigation();

    foreach ($modules as $module => $action_type) {
      if (
        !$modulesTbl->isModuleEnabled($module) ||
        !$creditsTbl->checkCredit($actionTypesTbl->getActionType($action_type), $user)
      ) {
        continue;
      }

      if ($module == 'page' || $module == 'inviter' || $module == 'forum') {
        $pages = array($this->getNav($module));
      } else {
        $pages = $menusApi->getMenuParams($module.'_quick');
      }
      $navigation->addPages($pages);
    }

    $this->view->navigation = $navigation;

    if (!count($navigation)) {
      return $this->setNoRender();
    }
  }

  public function getNav($module)
  {
    if ($module == 'page') {
      return array(
        'route'  =>  'page_create',
        'action' =>  'create',
        'class'  =>  'buttonlink  icon_page_new  menu_page_quick  page_quick_create',
        'label'  =>  'Create New Page',
        'reset_params'  =>  1
      );
    } elseif ($module == 'forum') {
      return array(
        'route'  =>  'forum_general',
        'class'  =>  'buttonlink  icon_forum_post_new  menu_forum_quick  forum_quick_create',
        'label'  =>  'Post New Topic',
        'reset_params'  =>  1
      );
    } elseif ($module == 'inviter') {
      return array(
        'route'  =>  'inviter_general',
        'class'  =>  'buttonlink  icon_invite  menu_invite_quick  inviter_quick_invite',
        'label'  =>  'Invite Friends',
        'reset_params'  =>  1
      );
    }
  }
}
