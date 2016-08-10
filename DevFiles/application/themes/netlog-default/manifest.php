<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'netlog-default',
    'version' => '4.2.0',
    'path' => 'application/themes/netlog-default',
    'title' => 'Netlog Theme Default',
    'description' => 'Netlog-like Template with default gray colors',
    'thumb' => 'netlog_theme.jpg',
    'author' => 'MisterWizard',
    'callback' => array (
      'path' => 'application/themes/netlog-default/install.php',
      'class' => 'NetlogDefault_Installer',
    ),
    'actions' => array (
        0 => 'install',
        1 => 'upgrade',
        2 => 'refresh',
        3 => 'remove',
    ),
    'directories' => array (
      0 => 'application/themes/netlog-default',
    ),
  ),
  'files' => array (
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
); ?>