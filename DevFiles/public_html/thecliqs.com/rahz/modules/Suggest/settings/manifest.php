<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'suggest',
    'version' => '4.1.9p5',
    'path' => 'application/modules/Suggest',
    'title' => 'Suggest',
    'description' => 'Suggest Plugin',
    'author' => 'Hire-Experts LLC',
    'meta' => array(
      'title' => 'Suggest',
      'description' => 'Suggest Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'callback' => array(
      'path' => 'application/modules/Suggest/settings/install.php',
      'class' => 'Suggest_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      )
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'directories' => array(
      'application/modules/Suggest',
    ),
    'files' => array(
      'application/languages/en/suggest.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'addActivity',
      'resource' => 'Suggest_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Suggest_Plugin_Core',
    ),
    array(
      'event' => 'onItemDeleteAfter',
      'resource' => 'Suggest_Plugin_Core',
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Suggest_Plugin_Core',
    )
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
    'suggest_general' => array(
      'route' => 'suggest/:controller/:action/*',
      'defaults' => array(
        'module' => 'suggest',
        'controller' => 'index',
        'action' => 'index'
      ),
    ),
    'suggest_view' => array(
      'route' => 'suggests/:suggest_id/*',
      'defaults' => array(
        'module' => 'suggest',
        'controller' => 'index',
        'action' => 'view',
        'suggest_id' => 0
      ),
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'suggest',
    'suggest_profile_photo'
  ),
  
);