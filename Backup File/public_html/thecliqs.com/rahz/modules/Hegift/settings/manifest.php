<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 03.02.12 12:50 TeaJay $
 * @author     Taalay
 */


return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'hegift',
    'version' => '4.2.1p7',
    'path' => 'application/modules/Hegift',
    'repository' => '',
    'title' => 'HeGift',
    'description' => 'Hire-Experts Virtual Gifts Plugin',
    'author' => 'Hire-Experts LLC',
    'meta' =>
    array (
      'title' => 'HeGift Plugin',
      'description' => 'Hire-Experts Virtual Gifts Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      ),
      array(
        'type' => 'module',
        'name' => 'credit',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'disable',
      4 => 'enable',
    ),
    'callback' => array(
      'path' => 'application/modules/Hegift/settings/install.php',
      'class' => 'Hegift_Installer',
    ),
    'directories' =>
    array (
      'application/modules/Hegift',
      'public/gift'
    ),
    'files' => array(
      'application/languages/en/hegift.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'gift',
    'hegift_recipient'
  ),
  // Content -------------------------------------------------------------------
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(

  ),
  // Routes --------------------------------------------------------------------

  'routes' => array(
    'hegift_general' => array(
      'route' => 'gifts/:action/*',
      'defaults' => array(
        'module' => 'hegift',
        'controller' => 'index',
        'action' => 'index'
      )
    ),

    'hegift_own' => array(
      'route' => 'send-gift/:action/*',
      'defaults' => array(
        'module' => 'hegift',
        'controller' => 'own',
        'action' => 'index'
      )
    ),

    'hegift_temp' => array(
      'route' => 'temp/:action/*',
      'defaults' => array(
        'module' => 'hegift',
        'controller' => 'temp',
        'action' => 'index'
      )
    )
  )
);
