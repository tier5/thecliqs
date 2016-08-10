<?php return array(
'package'=>array(
	'type' => 'module',
	'name' => 'netlogtemplatedefault',
	'version' => '4.2.0',
	'path' => 'application/modules/Netlogtemplatedefault',
	'title' => 'Netlog Template Default',
	'description' => 'This plugin is a part of netlog template',
	'author' => 'MisterWizard',
	'actions' => array(
		'install',
		'upgrade',
		'refresh',
		'enable',
		'disable',
	),
	'dependencies' => array(
		array(
			'type' => 'module',
			'name' => 'core',
			'minVersion' => '4.1.1',
		),
	),
	'callback' => array(
		'path' => 'application/modules/Netlogtemplatedefault/settings/install.php',
		'class' => 'Netlogtemplatedefault_Installer',
	),
	'directories' => array( 0=>'application/modules/Netlogtemplatedefault' ),
	'files' => array( 0=>'application/languages/en/netlogtemplate.csv' ),
),

	// Menus --------------------------------------------------------------------
'menu' => array(
	'netlogtemplate_usermenu'=>array(
		'title'=>'Netlog Template Usermenu',
		'menuitems'=>array(
			array(
				'name'=>'netlogtemplate_usermenu_shortcuts_album',
				'label'=>'Add New Photos',
				'plugin'=>'Album_Plugin_Menus::canCreateAlbums',
				'params'=>'{"route":"album_general", "action":"upload"}'
			),
			array(
				'name'=>'netlogtemplate_usermenu_shortcuts_blog',
				'label'=>'Write New Entry',
				'plugin'=>'Blog_Plugin_Menus::canCreateBlogs',
				'params'=>'{"route":"blog_general", "action":"create"}'
			),
			array(
				'name'=>'netlogtemplate_usermenu_shortcuts_event',
				'label'=>'Create New Event',
				'plugin'=>'Event_Plugin_Menus::canCreateEvents',
				'params'=>'{"route":"event_general", "action":"create"}'
			),
			array(
				'name'=>'netlogtemplate_usermenu_shortcuts_classified',
				'label'=>'Post New Listing',
				'plugin'=>'Classified_Plugin_Menus::canCreateClassifieds',
				'params'=>'{"route":"classified_general", "action":"create"}'
			),
			array(
				'name'=>'netlogtemplate_usermenu_shortcuts_video',
				'label'=>'Post New Video',
				'plugin'=>'Video_Plugin_Menus::onMenuInitialize_VideoMainCreate',
				'params'=>'{"route":"video_general", "action":"create"}'
			),
			array(
				'name'=>'netlogtemplate_usermenu_shortcuts_message',
				'label'=>'Send Message',
				'plugin'=>'',
				'params'=>'{"route":"messages_general", "module":"messages", "action":"compose"}'
			),
		),
	),
),

	// Widgets --------------------------------------------------------------------
'widgets' => array(
		// for home page
	array(
		'page'=>'core_index_index',
		'name'=>'netlogtemplatedefault.netlog-members-random',
		'parent_content_name'=>'left',
		'order'=>1,
	),
	array(
		'page'=>'core_index_index',
		'name'=>'user.login-or-signup',
		'parent_content_name'=>'middle',
		'order'=>1,
	),
	array(
		'page'=>'core_index_index',
		'name'=>'netlogtemplatedefault.netlog-mobile-version',
		'parent_content_name'=>'middle',
		'order'=>2,
	),
	array(
		'page'=>'core_index_index',
		'name'=>'netlogtemplatedefault.netlog-members-active',
		'parent_content_name'=>'right',
		'order'=>1,
	),
		// for header
	array(
		'page'=>'header',
		'name'=>'netlogtemplatedefault.netlog-header-menu',
		'parent_content_name'=>'main',
		'order'=>4,
	),
	array(
		'page'=>'header',
		'name'=>'netlogtemplatedefault.netlog-friends',
		'parent_content_name'=>'main',
		'order'=>5,
	),
	array(
		'page'=>'header',
		'name'=>'netlogtemplatedefault.netlog-main-menu',
		'parent_content_name'=>'main',
		'order'=>6,
	),
		// for footer
	array(
		'page'=>'footer',
		'name'=>'netlogtemplatedefault.netlog-network-statistic',
		'parent_content_name'=>'main',
		'order'=>1,
	),
	array(
		'page'=>'footer',
		'name'=>'netlogtemplatedefault.netlog-languages',
		'parent_content_name'=>'main',
		'order'=>3,
	),
),
);
?>