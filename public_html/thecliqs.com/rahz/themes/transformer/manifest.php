<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'transformer',
    'version' => '4.0.9',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/transformer',
    'repository' => 'setweaks.com',
    'title' => 'Transformer',
    'thumb' => 'theme.jpg',
    'author' => '<a href="http://setweaks.com" target="_blank">seTweaks.com Team</a>',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/transformer',
    ),
    'description' => 'Responsive theme from seTweaks Team',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>