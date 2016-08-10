<?php
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'blackapple',
    'version' => '4.2.4',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/blackapple',
    'repository' => 'socialengine.net',
    'title' => 'blackapple',
    'thumb' => 'theme.jpg',
    'author' => 'StarsDeveloper.com',
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/themes/blackapple',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
	'mobile.css',
  ),
) ?>