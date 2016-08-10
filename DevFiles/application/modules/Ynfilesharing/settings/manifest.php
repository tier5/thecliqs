<?php defined("_ENGINE") or die("access denied"); return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynfilesharing',
    'version' => '4.03',
    'path' => 'application/modules/Ynfilesharing',
    'title' => 'YN - File Sharing',
    'description' => 'File Sharing',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Ynfilesharing/settings/install.php',
      'class' => 'Ynfilesharing_Installer',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
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
      0 => 'application/modules/Ynfilesharing',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynfilesharing.csv',
    ),
  ),
  'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onGroupDeleteBefore',
      'resource' => 'Ynfilesharing_Plugin_Core',
    ),
    1 => 
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Ynfilesharing_Plugin_Core',
    ),
  ),
  'routes' => 
  array (
    'ynfilesharing_general' => 
    array (
      'route' => 'filesharing/:controller/:action/*',
      'defaults' => 
      array (
        'module' => 'ynfilesharing',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|listing|manage|create|browse-by-tree|browse|move|share|delete|view)',
      ),
    ),
    'ynfilesharing_folder_specific' => 
    array (
      'route' => 'filesharing/folder/:action/:folder_id/:slug/*',
      'defaults' => 
      array (
        'module' => 'ynfilesharing',
        'controller' => 'folder',
        'action' => 'view',
        'slug' => '-',
      ),
      'reqs' => 
      array (
        'folder_id' => '\\d+',
        'action' => '(view|delete|upload|move|edit|edit-perm)',
      ),
    ),
    'ynfilesharing_file_specific' => 
    array (
      'route' => 'filesharing/file/:action/:file_id/:slug/*',
      'defaults' => 
      array (
        'module' => 'ynfilesharing',
        'controller' => 'file',
        'action' => 'view',
        'slug' => '-',
      ),
      'reqs' => 
      array (
        'file_id' => '\\d+',
        'action' => '(view|delete|edit|download)',
      ),
    ),
    'ynfilesharing_share_view' => 
    array (
      'route' => 'filesharing/sh/:object_type/:object_id/:code/:slug/*',
      'defaults' => 
      array (
        'module' => 'ynfilesharing',
        'controller' => 'index',
        'action' => 'shareview',
        'slug' => '-',
      ),
      'reqs' => 
      array (
        'object_type' => '(folder|file)',
        'object_id' => '\\d+',
      ),
    ),
  ),
  'items' => 
  array (
    0 => 'ynfilesharing_folder',
    1 => 'ynfilesharing_file',
    2 => 'folder',
    3 => 'ynfilesharing_document',
    4 => 'file',
  ),
);?>
