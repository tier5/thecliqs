<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'welcomepagevk',
    'version' => '4.1.1',
    'revision' => '$Revision: 8291 $',
    'path' => 'application/modules/Welcomepagevk',
    'repository' => 'socialenginemarket.com',
    'title' => 'Welcome VK Page ',
    'description' => 'Welcome VK Page ',
    'author' => 'SocialEngineMarket',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'welcomepagevk',
        'minVersion' => '4.1.1',
      ),
    ),
    'actions' => array(
       'install',
       //'upgrade',
       //'refresh',
       //'enable',
       //'disable',
     ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 3000,
    ),
    'directories' => array(
      'application/modules/Welcomepagevk',
    ),
    'files' => array(
      'application/languages/en/welcomepagevk.csv',
    ),
  ),
 
  // Items ---------------------------------------------------------------------
  'items' => array(
    'welcomepagevk',
  ),

  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Welcome - General
    'welcomepagevk_general' => array(
      'route' => 'welcomepagevk/*',
      'defaults' => array(
        'module' => 'welcomepagevk',
        'controller' => 'index',
        'action' => 'index'
      ),
    ),
    'welcomepagevk_admin' => array(
      'route' => 'admin/welcomepagevk/*',
      'defaults' => array(
        'module' => 'welcomepagevk',
        'controller' => 'admin-settings',
        'action' => 'index'
      ),
    ),

  )
); ?>
