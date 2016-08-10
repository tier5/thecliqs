<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynmediaimporter',
    'version' => '4.03p2',
    'path' => 'application/modules/Ynmediaimporter',
    'title' => 'YN - Social Media Importer',
    'description' => 'Social Media Importer is a right tool for your members if you want to enrich your site. It imports Photo from other top social networks like Facebook, Flickr, Picasa, etc.',
   'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Ynmediaimporter/settings/install.php',
      'class' => 'Ynmediaimporter_Installer',
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
      0 => 'application/modules/Ynmediaimporter',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynmediaimporter.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
       1 => 
      array (
        'type' => 'module',
        'name' => 'album',
        'minVersion' => '4.2.0',
      ),
      2 => array(
        'type' => 'module',
        'name' => 'socialbridge',
        'minVersion' => '4.04p4',
      ),
    ),
  ),
  'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onUserLogoutBefore',
      'resource' => 'Ynmediaimporter_Plugin_Core',
    ),
  ),
  'routes' => 
  array (
    'ynmediaimporter_extended' => 
    array (
      'route' => 'media-importer/:controller/:action/*',
      'defaults' => 
      array (
        'module' => 'ynmediaimporter',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
      ),
    ),
    'ynmediaimporter_general' => 
    array (
      'route' => 'media-importer/:action/*',
      'defaults' => 
      array (
        'module' => 'ynmediaimporter',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(connect|disconnect|index|session|disconnect-facebook)',
      ),
    ),
    'ynmediaimporter_callback' => 
    array (
      'route' => 'media-importer/callback/:service/*',
      'defaults' => 
      array (
        'module' => 'ynmediaimporter',
        'controller' => 'index',
        'action' => 'callback',
        'service' => 'facebook',
      ),
      'reqs' => 
      array (
      ),
    ),
  ),
);?>
