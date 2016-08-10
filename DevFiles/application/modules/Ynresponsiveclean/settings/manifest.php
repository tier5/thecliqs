<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynresponsiveclean',
    'version' => '4.02',
    'path' => 'application/modules/Ynresponsiveclean',
    'title' => 'YN - Responsive Clean Template',
    'description' => 'Responsive Clean Template',
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
      0 => 'application/modules/Ynresponsiveclean',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynresponsiveclean.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
    ),
  ),
); ?>