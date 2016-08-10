<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
return array(
	'4.01p3' => array(
			'externals/scripts/core.js' => 'Fix bugs',
			'externals/scripts/iframe.js' => 'Fix bugs',
			'settings/changelog.php' => 'Incremented version',
			'settings/manifest.php' => 'Incremented version',
	),
	'4.01p2' => array(
		'externals/scripts/core.js' => 'Add the feature to switch back to user\'s theme and site default layout',
		'externals/scripts/iframe.js' => 'Add the feature to switch back to user\'s theme and site default layout',
		'externals/styles/iframe.css' => 'Add the feature to switch back to user\'s theme and site default layout',
		'Model/Layout.php' => 'Fix the issue about thumbnail image of a layout',
		'views/scripts/index/index.tpl' => 'Add the feature to switch back to user\'s theme and site default layout',
		'settings/changelog.php' => 'Incremented version',
		'settings/manifest.php' => 'Incremented version'	
	),
    '4.01p1' => array(
		'controllers/AdminManageController.php' => 'Fix the about rewrite URL when uploading the image',              
    	'externals/scripts/core.js' => 'Fix the bug when turning off rewrite mode',
		'externals/scripts/iframe.js' => 'Fix the bug when turning off rewrite mode',
		'Model/Layout.php' => 'Fix the bug when deleting a layout',
        'Plugin/Menus.php' => 'Add feature to open the link when access the profile page with the param edit-style',
        'views/index/custom-menu-bar.tpl' => 'Add choosing image feature from URL and choose none background',
		'views/index/custom-tab-bar.tpl' => 'Add choosing image feature from URL and choose none background',
		'views/index/custom-widget-bar.tpl' => 'Add choosing image feature from URL and choose none background',
        'settings/changelog.php' => 'Incremented version',
        'settings/manifest.php' => 'Incremented version',
        'settings/my-upgrade-4.01-4.01p1.sql' => 'Added',
        'settings/my.sql' => 'Incremented version',
    ),    
)
?>