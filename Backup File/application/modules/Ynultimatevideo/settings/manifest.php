<?php return array (
    'package' => array (
        'type' => 'module',
        'name' => 'ynultimatevideo',
        'version' => '4.02',
        'path' => 'application/modules/Ynultimatevideo',
        'title' => 'YN - Ultimate Video',
        'description' => 'YN - Ultimate Video',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
        'callback' => array(
            'path' => 'application/modules/Ynultimatevideo/settings/install.php',
            'class' => 'Ynultimatevideo_Installer',
        ),
        'actions' => array (
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' => array (
            0 => 'application/modules/Ynultimatevideo',
        ),
        'files' => array (
            0 => 'application/languages/en/ynultimatevideo.csv',
        ),
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.2',
            ),
        ),
    ),
    'items' =>array(
        'ynultimatevideo_video',
        'ynultimatevideo_signature',
        'ynultimatevideo_favorite',
        'ynultimatevideo_playlist',
        'ynultimatevideo_playlistassoc',
        'ynultimatevideo_category',
        'ynultimatevideo_history'
    ),
    // Composer -------
    'composer' => array(
        'ynultimatevideo' => array(
            'script' => array('_composeYnultimatevideo.tpl', 'ynultimatevideo'),
            'plugin' => 'Ynultimatevideo_Plugin_Composer',
            'auth' => array('ynultimatevideo_video', 'create'),
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Ynultimatevideo_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Ynultimatevideo_Plugin_Core',
        ),
    ),
    'routes' => array(
        'ynultimatevideo_extended' => array(
            'route' => 'ultimate-videos/:controller/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'video'
            ),
            'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
        ),
        'ynultimatevideo_general' => array(
            'route' => 'ultimate-videos/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|browse|create|create-playlist|list|manage|validation|add-to|edit|delete|rate|compose-upload|add-to-group|oauth2callback|upload-video|send-to-friend)',
            )
        ),
        'ynultimatevideo_specific' => array(
            'route' => 'ultimate-videos/video/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'video',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|send-to-friends)'
            )
        ),
        'ynultimatevideo_view' => array(
            'route' => 'ultimate-videos/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'ynultimatevideo_favorite' => array(
            'route' => 'ultimate-videos/favorite/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'favorite',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|manage|get-playlist-form|create-playlist|add-to-playlist|render-playlist-list)',
            )
        ),
        'ynultimatevideo_playlist' => array(
            'route' => 'ultimate-videos/playlist/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'playlist',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|edit|manage|remove|get-playlist-form|create-playlist|add-to-playlist|render-playlist-list)',
            )
        ),
        'ynultimatevideo_watch_later' => array(
            'route' => 'ultimate-videos/watch-later/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'watch-later',
                'action' => 'index'
            )
        ),
        'ynultimatevideo_compose' => array(
            'route' => 'ultimate-videos/index/compose-upload/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'index',
                'action' => 'compose-upload'
            )
        ),
        'ynultimatevideo_history' => array(
            'route' => 'ultimate-videos/history/:action/*',
            'defaults' => array(
                'module' => 'ynultimatevideo',
                'controller' => 'history',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index)',
            )
        ),
    )
);
?>