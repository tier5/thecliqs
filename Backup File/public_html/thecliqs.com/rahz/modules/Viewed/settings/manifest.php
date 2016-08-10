<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'viewed',
    'version' => '4.8.6p4',
    'path' => 'application/modules/Viewed',
    'title' => 'Who Viewed Me',
    'description' => 'This package will show the list of users who viewed your profile.',
    'author' => '<a href="http://ipragmatech.com/">iPragmatech/MisterWizard</a>',
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
      0 => 'application/modules/Viewed',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/viewed.csv',
    ),
  ),
	// Items ---------------------------------------------------------------------
		'items' => array(
				'viewed_package',
				'viewed_gateway',
				'viewed_subscription',
				'viewed_order'
		),
); ?>
