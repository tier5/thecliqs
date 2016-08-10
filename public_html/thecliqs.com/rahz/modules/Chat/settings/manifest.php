<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9932 2013-02-18 21:28:18Z john $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'chat',
    'version' => '4.3.0',
    'revision' => '$Revision: 9932 $',
    'path' => 'application/modules/Chat',
    'repository' => 'socialengine.com',
    'title' => 'Chat',
    'description' => 'Chat',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Chat/settings/install.php',
      'class' => 'Chat_Installer',
    ),
    'directories' => array(
      'application/modules/Chat',
    ),
    'files' => array(
      'application/languages/en/chat.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Chat_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutAdminDefault',
      'resource' => 'Chat_Plugin_Core',
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Chat_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(

  ),
  // Routes --------------------------------------------------------------------
); ?>