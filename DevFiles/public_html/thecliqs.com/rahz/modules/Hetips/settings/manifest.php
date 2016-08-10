<?php

// 02.03.2013 - TrioxX

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'hetips',
    'version' => '4.0.0p3',
    'path' => 'application/modules/Hetips',
    'title' => 'Hire-Experts Tips',
    'description' => '',
    'author' => 'Hire-Experts LLC',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.1.9p1',
      ),
      array(
        'type' => 'module',
        'name' => 'wall',
        'minVersion' => '4.2.0',
      ),
    ),
    'callback' =>
    array (
      'class' => 'Hetips_Installer',
      'path' => 'application/modules/Hetips/settings/install.php',
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
      0 => 'application/modules/Hetips',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/hetips.csv',
    ),
  ),
  /*Hooks -----------------------------------------> */
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Hetips_Plugin_Core'
    )
  ),
); ?>