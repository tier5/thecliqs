<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'advmenusystem',
    'version' => '4.04p4',
    'path' => 'application/modules/Advmenusystem',
    'title' => 'YN - Advanced Menu System',
    'description' => 'This is Advanced Menu System module.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Advmenusystem',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/advmenusystem.csv',
    ),
    'dependencies' => 
    array (
       array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'advmenusystem_submenu',
    'advmenusystem_content',
  ),
  'routes' => 
  array (
  
    'advmenusystem_friend_requests' => 
    array (
      'route' => 'adv-menu-system/friend-requests/',
      'defaults' => 
      array (
        'module' => 'advmenusystem',
        'controller' => 'index',
        'action' => 'friend-requests',
      ),
    ),
    'advmenusystem_notifications' => 
    array (
      'route' => 'adv-menu-system/notifications/',
      'defaults' => 
      array (
        'module' => 'advmenusystem',
        'controller' => 'index',
        'action' => 'notifications',
      ),
    ),
  ),
) ; ?>
