<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'checkin',
    'version' => '4.1.2p6',
    'path' => 'application/modules/Checkin',
    'title' => 'Checkin',
    'description' => 'Checkin',
    'author' => 'Hire-Experts LLC',
    'callback' =>
    array (
      'path' => 'application/modules/Checkin/settings/install.php',
      'class' => 'Checkin_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'wall',
        'minVersion' => '4.2.4p2',
      ),
    ),
    'actions' =>
    array (
      'preinstall',
      'install',
      'upgrade',
      'refresh',
      'enable',
      'disable',
    ),
    'directories' =>
    array (
      0 => 'application/modules/Checkin',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/checkin.csv',
    ),
  ),
  'wall_composer' => array(
     array(
       'script' => array('compose/checkin.tpl', 'checkin'),
       'plugin' => 'Checkin_Plugin_Composer_Core',
       'module' => 'checkin',
       'type' => 'checkin',
       'composer' => TRUE
     ),
   ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Checkin_Plugin_Core'
    ),
  ),
);