<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'transformer',
    'version' => '4.0.9',
    'path' => 'application/modules/Transformer',
    'title' => 'Transformer',
    'description' => 'Dependencies for Transformer theme.',
    'author' => '<a href="http://setweaks.com" target="_blank">seTweaks.com Team</a>',
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
      0 => 'application/modules/Transformer',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/transformer.csv',
    ),
  ),
); ?>