<?php
return array (
	'package' => array (
		'type' => 'module',
		'name' => 'socialgames',
		'version' => '4.9',
		'path' => 'application/modules/Socialgames',
		'title' => 'SocialGames',
		'description' => 'add many games for your site',
		'author' => 'SocialEnginePro LLC/MisterWizard',
		'callback' => array(
			 'path' => 'application/modules/Socialgames/settings/install.php',
			 'class' => 'Socialgameswidget_Installer',
		),
		'actions' => array (
			0 => 'install',
			1 => 'upgrade',
			2 => 'refresh',
			3 => 'enable',
			4 => 'disable',
		),
		'dependencies' => array(
			array(
				'type' => 'module',
				'name' => 'core',
				'minVersion' => '4.2.0',
			),
		),
		'directories' => array (
			0 => 'application/modules/Socialgames',
		),
		'files' => array (
			0 => 'application/languages/en/socialgames.csv',
		),
	),
	
	'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Socialgames_Plugin_Core'
        ),
    ),
	
	'items' => array(
        'socialgames_game'
    ),
	
	'routes' => array(
        'games_general' => array(
            'route' => 'games/:action/*',
            'defaults' => array(
                'module' => 'socialgames',
                'controller' => 'index',
                'action' => 'browse'
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
		'games_view' => array(
            'route' => 'game/:game_id/:slug/*',
            'defaults' => array(
                'module' => 'socialgames',
                'controller' => 'game',
                'action' => 'index',
                'slug' => ''
            ),
			 'reqs' => array(
                'game_id' => '\d+',
            )
        ),
	),
	'menu' => array(
		'socialgame_admin_main' => array(
			'title' => 'Socialgames Admin Menu',
			'menuitems' => array(
				array(
					'name' => 'socialgames_admin_manage',
					'label' => 'Games Manage',
					'plugin' => '',
					'params' => '{"route":"admin_default","module":"socialgames","controller":"manage"}'
				),
				array(
					'name' => 'socialgames_admin_managelevels',
					'label' => 'Member Levels Settings',
					'plugin' => '',
					'params' => '{"route":"admin_default","module":"socialgames","controller":"manage","action":"levels"}'
				),
			)
		),
		'socialgames_main' => array(
            'title' => 'Socialgames user main menu',
            'menuitems' => array(
                array(
                    'name' => 'socialgames_browse',
                    'label' => 'Browse games',
                    'params' => '{"route":"games_general"}'
                ),
				array(
                    'name' => 'socialgames_favourite',
                    'label' => 'Favourite games',
                    'params' => '{"route":"games_general","action":"favourite"}'
                ),
				array(
                    'name' => 'socialgames_featured',
                    'label' => 'Featured games',
                    'params' => '{"route":"games_general","action":"featured"}'
                ),
				array(
                    'name' => 'socialgames_random',
                    'label' => 'Play random game',
                    'params' => '{"route":"games_general","action":"random"}'
                )
            ),
        ),
		'core_main' => array(
            'title' => '',
            'menuitems' => array(
                array(
                    'name' => 'core_main_socialgames',
                    'label' => 'Games',
                    'plugin' => '',
                    'params' => '{"route":"games_general"}',
                ),
            ),
        ),
        'core_sitemap' => array(
            'title' => '',
            'menuitems' => array(
                 array(
                    'name' => 'core_main_socialgamessitemap',
                    'label' => 'Games',
                    'plugin' => '',
                    'params' => '{"route":"games_general"}',
                ),
            ),
        ),
	    'core_admin_main_plugins' => array(
			'title' => '',
			'menuitems' => array(
				array(
					'name' => 'core_admin_main_plugins_socialgames',
					'label' => 'Socialgames Settings',
					'plugin' => '',
					'params' => '{"route":"admin_default","module":"socialgames","controller":"manage"}'
				)
			),
		)
	)
);
?>