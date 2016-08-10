<?php return array(
    'package' => array(
        'type' => 'module',
        'name' => 'ynvideochannel',
        'version' => '4.01',
        'path' => 'application/modules/Ynvideochannel',
        'title' => 'YN - Video Channel',
        'description' => 'YN - Video Channel',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
        'callback' =>
            array(
                'path' => 'application/modules/Ynvideochannel/settings/install.php',
                'class' => 'Ynvideochannel_Installer',
            ),
        'actions' =>
            array(
                0 => 'install',
                1 => 'upgrade',
                2 => 'refresh',
                3 => 'enable',
                4 => 'disable',
            ),
        'dependencies' =>
            array(
                0 =>
                    array(
                        'type' => 'module',
                        'name' => 'core',
                        'minVersion' => '4.1.2',
                    ),
            ),
        'directories' =>
            array(
                0 => 'application/modules/Ynvideochannel',
            ),
        'files' =>
            array(
                0 => 'application/languages/en/ynvideochannel.csv',
            ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'ynvideochannel_video',
        'ynvideochannel_channel',
        'ynvideochannel_playlist',
        'ynvideochannel_favorite',
        'ynvideochannel_subscribe',
        'ynvideochannel_playlistvideo',
        'ynvideochannel_category',
        'ynvideochannel_usershared'
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Ynvideochannel_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Ynvideochannel_Plugin_Core',
        ),
    ),
    'routes' => array(
        'ynvideochannel_extended' => array(
            'route' => 'video-channel/:controller/:action/*',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'index'
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'ynvideochannel_general' => array(
            'route' => 'video-channel/:action/*',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|share-video|validation-video|add-channel|find-channel|get-channel|browse-videos|channels|browse-channels|playlists|browse-playlists|create-playlist|ajax-create-playlist|manage-videos|manage-channels|manage-playlists|favorites|subscriptions|get-playlist-form|add-to-playlist|send-to-friends|ajax-get-friends)', // all actions in IndexController
            )
        ),
        'ynvideochannel_video' => array(
            'route' => 'video-channel/video/:action/:video_id/*',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'video',
                'action' => 'index',
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'action' => '(edit|delete|favorite|unfavorite|render-playlist-list|external|rate|render-favorite-link)', // all actions in VideoController
            )
        ),
        'ynvideochannel_video_detail' => array(
            'route' => 'video-channel/video/:video_id/:slug',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'video',
                'action' => 'detail',
            ),
            'reqs' => array(
                'video_id' => '\d+',
            )
        ),
        'ynvideochannel_channel' => array(
            'route' => 'video-channel/channel/:action/:channel_id/*',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'channel',
                'action' => 'index',
            ),
            'reqs' => array(
                'channel_id' => '\d+',
                'action' => '(edit|delete|subscribe|unsubscribe|auto-update|add-more-videos|ajax-get-videos)', // all actions in ChannelController
            )
        ),
        'ynvideochannel_channel_detail' => array(
            'route' => 'video-channel/channel/:channel_id/:slug',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'channel',
                'action' => 'detail',
            ),
            'reqs' => array(
                'channel_id' => '\d+',
            )
        ),
        'ynvideochannel_playlist' => array(
            'route' => 'video-channel/playlist/:action/:playlist_id/*',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'playlist',
                'action' => 'index',
            ),
            'reqs' => array(
                'playlist_id' => '\d+',
                'action' => '(edit|delete|detail)', // all actions in PlaylistController
            )
        ),
        'ynvideochannel_playlist_detail' => array(
            'route' => 'video-channel/playlist/:playlist_id/:slug',
            'defaults' => array(
                'module' => 'ynvideochannel',
                'controller' => 'playlist',
                'action' => 'detail',
            ),
            'reqs' => array(
                'playlist_id' => '\d+',
            )
        )
    )
); ?>