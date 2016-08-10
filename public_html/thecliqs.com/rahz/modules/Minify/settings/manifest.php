<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'minify',
    'version' => '4.04',
    'path' => 'application/modules/Minify',
    'title' => 'YN - Minify',
    'description' => 'It combines multiple CSS or Javascript files, removes unnecessary whitespace and comments, and serves them with gzip encoding and optimal client-side cache headers. Make your application run faster.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
	
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
    ),
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
      0 => 'application/modules/Minify',
      1 => 'externals/minify',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/minify.csv',
	  1 => 'application/modules/Core/View/Helper/HeadScript.php',
	  2 => 'application/modules/Core/View/Helper/HeadLink.php',
	  3 => 'application/settings/minify.php'
    ),
  ),
    'items' => array(
        'minify',
        'minify_minifies',
       
    ),
); ?>
