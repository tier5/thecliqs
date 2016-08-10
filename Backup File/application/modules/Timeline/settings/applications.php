<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: applications.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
$apps = array(
  'user.profile-friends' => array(
    'module' => 'user',
    'title' => 'Friends',
    'items' => array(),
    'add-link' => array(
      'route' => 'user_general',
    ),
  ),
  'user.profile-friends-followers' => array(
    'module' => 'user',
    'title' => 'Followers',
    'items' => array(),
    'add-link' => array(
      'route' => 'user_general',
    ),
  ),
  'user.profile-friends-following' => array(
    'module' => 'user',
    'title' => 'Following',
    'items' => array(),
    'add-link' => array(
      'route' => 'user_general',
    ),
  ),
  'page.profile-pages' => array(
    'module' => 'page',
    'title' => 'Pages',
    'items' => array(),
    'add-link' => array(
      'route' => 'page_create',
    )
  ),
  'album.profile-albums' => array(
    'module' => 'album',
    'title' => 'Photos',
    'items' => array(),
    'add-link' => array(
      'route' => 'album_general',
      'action' => 'upload'
    )
  ),
  'headvancedalbum.profile-albums' => array(
    'module' => 'headvancedalbum',
    'title' => 'Photos',
    'items' => array(),
    'add-link' => array(
      'route' => 'album_general',
      'action' => 'upload'
    )
  ),
  'video.profile-videos' => array(
    'module' => 'video',
    'title' => 'Videos',
    'items' => array(),
    'add-link' => array(
      'route' => 'video_general',
      'action' => 'create'
    )
  ),
  'event.profile-events' => array(
    'module' => 'event',
    'title' => 'Events',
    'items' => array(),
    'add-link' => array(
      'route' => 'event_general',
      'action' => 'create'
    )
  ),
  'group.profile-groups' => array(
    'module' => 'group',
    'title' => 'Groups',
    'items' => array(),
    'add-link' => array(
      'route' => 'group_general',
      'action' => 'create'
    ),
  ),
  'classified.profile-classifieds' => array(
    'module' => 'classified',
    'title' => 'Classifieds',
    'items' => array(),
    'add-link' => array(
      'route' => 'classified_general',
      'action' => 'create'
    ),
  ),
  'like.profile-likes' => array(
    'module' => 'like',
    'title' => 'Likes',
    'items' => array(),
  ),

  //Just supported
  'blog.profile-blogs' => array(
    'module' => 'blog',
    'title' => 'Blogs',
    'render' => false,
    'add-link' => array(
      'route' => 'blog_general',
      'action' => 'create'
    ),
  ),
  'poll.profile-polls' => array(
    'module' => 'poll',
    'title' => 'Polls',
    'render' => false,
    'add-link' => array(
      'route' => 'poll_general',
      'action' => 'create'
    ),
  ),
  'forum.profile-forum-posts' => array(
    'module' => 'forum',
    'title' => 'Forum Posts',
    'render' => false,
    'add-link' => array(
      'route' => 'forum_general',
    ),
  ),
  'forum.profile-forum-topics' => array(
    'module' => 'forum',
    'title' => 'Forum Topics',
    'render' => false,
    'add-link' => array(
      'route' => 'forum_general',
    ),
  ),
  'checkin.profile-checkins' => array(
    'module' => 'checkin',
    'title' => 'Check-Ins',
    'render' => false,
  ),
);

if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('ynblog')) {
  $apps['ynblog.profile-blogs'] = array(
    'module' => 'ynblog',
    'title' => 'Blogs',
    'render' => false,
    'add-link' => array(
      'route' => 'blog_general',
      'action' => 'create'
    ),
  );
}

return $apps;