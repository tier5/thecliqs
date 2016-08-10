<?php defined("_ENGINE") or die("access denied"); return array (
  'menus' => 
  array (
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 281,
      'name' => 'core_admin_plugins_ynmoderation',
      'module' => 'ynmoderation',
      'label' => 'Moderation',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmoderation","controller":"moderations","action":"index"}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    1 => 
    array (
      'id' => 282,
      'name' => 'ynmoderation_admin_main_settings',
      'module' => 'ynmoderation',
      'label' => 'Global Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmoderation","controller":"settings"}',
      'menu' => 'ynmoderation_admin_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    2 => 
    array (
      'id' => 283,
      'name' => 'ynmoderation_admin_main_moderations',
      'module' => 'ynmoderation',
      'label' => 'Content Management',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmoderation","controller":"moderations"}',
      'menu' => 'ynmoderation_admin_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    3 => 
    array (
      'id' => 284,
      'name' => 'ynmoderation_admin_main_reports',
      'module' => 'ynmoderation',
      'label' => 'Reports',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmoderation","controller":"reports"}',
      'menu' => 'ynmoderation_admin_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
  ),
  'mails' => 
  array (
  ),
  'jobtypes' => 
  array (
  ),
  'notificationtypes' => 
  array (
  ),
  'actiontypes' => 
  array (
  ),
  'permissions' => 
  array (
  ),
  'pages' => 
  array (
  ),
);?>