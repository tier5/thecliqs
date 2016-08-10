<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'photoviewer',
    'version' => '4.2.9p1',
    'path' => 'application/modules/Photoviewer',
    'title' => 'Photo Viewer',
    'description' => 'Photo Viewer',
    'author' => 'Hire-Experts LLC',

    'callback' => array(
      'path' => 'application/modules/Photoviewer/settings/install.php',
      'class' => 'Photoviewer_Installer',
    ),

    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Photoviewer',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/photoviewer.csv',
    ),
  ),
); ?>