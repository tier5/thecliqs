<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynmusic',
    'version' => '4.02',
    'path' => 'application/modules/Ynmusic',
    'title' => 'YN - Social Music',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
     'callback' => 
    array (
        'path' => 'application/modules/Ynmusic/settings/install.php',    
        'class' => 'Ynmusic_Installer',
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
      0 => 'application/modules/Ynmusic',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynmusic.csv',
    ),
  ),
  	// Compose -------------------------------------------------------------------
  	'compose' => array(
    	array('_composeYnmusic.tpl', 'music'),
  	),
  	'composer' => array(
    	'ynmusic' => array(
      		'script' => array('_composeYnmusic.tpl', 'ynmusic'),
      		'plugin' => 'Ynmusic_Plugin_Composer',
      		'auth' => array('ynmusic_song', 'create'),
    	),
  	),
	// Items ---------------------------------------------------------------------
    'items' => array(
        'ynmusic_genre',
        'ynmusic_artist',
        'ynmusic_album',
        'ynmusic_song',
        'ynmusic_playlist',
        'ynmusic_playlist_song',
        'ynmusic_faq',
        'ynmusic_alonesong',
       	'ynmusic_history'
    ),
    
	// Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Ynmusic_Plugin_Core',
        ),
        array(
	      	'event' => 'onRenderLayoutMobileDefault',
	      	'resource' => 'Ynmusic_Plugin_Core',
	    ),
    ),
    
	// Routes ---------------------------------------------------------------------
	'routes' => array(
		'ynmusic_extended' => array(
			'route' => 'social-music/:controller/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
		),
		'ynmusic_general' => array(
			'route' => 'social-music/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(validate|suggest-artist|suggest-genre|listing|upload-photo|update-play-count|reposition)',
	        )
		),
		'ynmusic_song_profile' => array(
			'route' => 'social-music/song/:id/:slug/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'songs',
				'action' => 'view',
				'slug' => '',
			),
			'reqs' => array(
					'id' => '\d+',
			)
	    ),
		'ynmusic_song' => array(
			'route' => 'social-music/songs/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'songs',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|manage|upload|upload-song|delete|edit|get-form-edit)',
	        )
		),
		'ynmusic_album_profile' => array(
			'route' => 'social-music/album/:id/:slug/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'albums',
				'action' => 'view',
				'slug' => '',
			),
			'reqs' => array(
					'id' => '\d+',
			)
	    ),
		'ynmusic_album' => array(
			'route' => 'social-music/albums/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'albums',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|manage|delete|edit|sort)',
	        )
		),
		'ynmusic_artist_profile' => array(
			'route' => 'social-music/artist/:id/:slug/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'artists',
				'action' => 'view',
				'slug' => '',
			),
			'reqs' => array(
					'id' => '\d+',
			)
	    ),
		'ynmusic_artist' => array(
			'route' => 'social-music/artists/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'artists',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|view)',
	        )
		),
		'ynmusic_playlist' => array(
			'route' => 'social-music/playlists/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'playlists',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|manage|get-playlist-form|create-playlist|add-to-playlist|render-playlist-list)',
	        )
		),
		
		'ynmusic_faqs' => array(
			'route' => 'social-music/faqs/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'faqs',
				'action' => 'index',
			),
		),
		
		'ynmusic_history' => array(
			'route' => 'social-music/history/:action/*',
			'defaults' => array(
				'module' => 'ynmusic',
				'controller' => 'history',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index)',
	        )
		),
	),
); ?>