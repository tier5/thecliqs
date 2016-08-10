<?php

// 02.03.2013 - TrioxX

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'weather',
    'version' => '4.1.5p5',
    'path' => 'application/modules/Weather',
    'title' => 'Weather Plugin',
    'description' => 'Weather Plugin',
    'author' => 'Hire-Experts LLC',
    'callback' => array(
      'path' => 'application/modules/Weather/settings/install.php',
      'class' => 'Weather_Installer',
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
      0 => 'application/modules/Weather',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/weather.csv',
    ),
  ),
); ?>