<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

return array (
		array (
				'title' => 'File Sharing Browse Menu',
				'description' => 'Displays a menu for the module file sharing.',
				'category' => 'File Sharing',
				'type' => 'widget',
				'name' => 'ynfilesharing.browse-menu'
		),
		array (
				'title' => 'File Sharing Search',
				'description' => 'Displays file sharing search box on browse page.',
				'category' => 'File Sharing',
				'type' => 'widget',
				'name' => 'ynfilesharing.filesharing-search',
				'defaultParams' => array(
					'title' => 'File Sharing Search',
				),
		),
		array (
				'title' => 'Profile Folders',
				'description' => 'Displays folders of an object.',
				'category' => 'File Sharing',
				'type' => 'widget',
				'name' => 'ynfilesharing.profile-folders',
				'requirements' => array('subject'),
				'defaultParams' => array(
					'title' => 'Folders',
				),
		),
);