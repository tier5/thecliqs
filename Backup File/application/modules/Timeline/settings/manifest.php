<?php

// 02.03.2013 - TrioxX

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'timeline',
        'version' => '4.2.0p7',
        'path' => 'application/modules/Timeline',
        'title' => 'Timeline',
        'description' => 'Hire-Experts LLC module',
        'author' => 'Hire-Experts LLC',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'hecore',
                'minVersion' => '4.1.8',
            ),
            array(
                'type' => 'module',
                'name' => 'wall',
                'minVersion' => '4.2.5',
            )
        ),
        'callback' => array(
            'path' => 'application/modules/Timeline/settings/install.php',
            'class' => 'Timeline_Installer',
        ),

        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Timeline',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/timeline.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
      'thumb',
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserCoverPagePhotoUpload',
            'resource' => 'Timeline_Plugin_Core'
        ),
        array(
            'event' => 'onUserCoverPhotoUpload',
            'resource' => 'Timeline_Plugin_Core'
        ),
        array(
            'event' => 'onUserBornPhotoUpload',
            'resource' => 'Timeline_Plugin_Core'
        ),
    ),

    'wall_composer' => array(
        array(
            'script' => array('compose/date.tpl', 'timeline'),
            'composer' => true,
            'plugin' => 'Timeline_Plugin_Composer_Date',
            'module' => 'timeline',
            'type' => 'date'
        ),
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        // Timeline - General
        'timeline_extended' => array(
            'route' => 'timeline/:controller/:action/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'index',
                'action' => 'index'
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
        'timeline_profile' => array(
            'route' => 'timeline/profile/:id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'profile',
                'action' => 'index'
            )
        ),
        'timeline_page_profile' => array(
            'route' => 'timeline-page/:page_id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'page',
                'action' => 'index'
            )
        ),
        'timeline_widget' => array(
            'route' => 'timeline/widget/:id/:content_id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'profile',
                'action' => 'widget',
            ),
        ),

        'timeline_photo' => array(
            'route' => 'timeline/photo/:action/:type/:id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'photo',
                'action' => 'albums',
                'type' => 'cover',
            ),
            'reqs' => array(
                'action' => '(albums|photos|upload|get|set|position|remove)',
                'type' => '(cover|born)',
            )
        ),
// Timeline Page
        'timeline_page_photo' => array(
            'route' => 'timeline/page-photo/:action/:type/:id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'page-photo',
                'action' => 'albums',
                'type' => 'cover',
            ),
            'reqs' => array(
                'action' => '(albums|photos|upload|get|set|position|remove)',
                'type' => '(cover|born)',
            )
        ),
// Timeline Page
        'timeline_date' => array(
            'route' => 'timeline/date/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'index',
                'action' => 'date'
            )
        ),
        'timeline_dates' => array(
            'route' => 'timeline/dates/:id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'index',
                'action' => 'dates'
            )
        ),
        'timeline_life_event' => array(
            'route' => 'timeline/life-event/:action/:id/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'life-event',
                'action' => 'index',
            ),
            'reqs' => array(
//        'type' => '(born)',
            )
        ),
        'timeline_user_settings' => array(
            'route' => 'timeline/user-settings/:param/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'user-settings',
                'action' => 'index',
                'param' => false
            )
        ),
        'timeline_user_edit' => array(
            'route' => 'timeline/user-edit/:param/*',
            'defaults' => array(
                'module' => 'timeline',
                'controller' => 'user-settings',
                'action' => 'index',
                'param' => true
            )
        )
    ),
); ?>