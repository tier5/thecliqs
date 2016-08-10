<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2013-01-17 15:22:44 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'headvancedalbum',
        'version' => '4.2.0p2',
        'path' => 'application/modules/Headvancedalbum',
        'title' => 'Advanced Photo Albums',
        'description' => '',
        'author' => 'Hire-Experts LLC',
      'dependencies' => array(
        array(
          'type' => 'module',
          'name' => 'core',
          'minVersion' => '4.1.7',
        ),
        array(
          'type' => 'module',
          'name' => 'album',
          'minVersion' => '4.1.7',
        ),
      ),
        'callback' =>
        array(
          'class' => 'Headvancedalbum_Installer',
          'path' => 'application/modules/Headvancedalbum/settings/install.php',
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
            0 => 'application/modules/Headvancedalbum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/headvancedalbum.csv',
        ),
    ),

    // Routes ----------------------------------------------------
    'routes' => array(


        'headvancedalbum_albums_browse' => array(
            'route' => 'albums/*',
            'defaults' => array(
                'module' => 'headvancedalbum',
                'controller' => 'index',
                'action' => 'browse',
                'filter' => 'albums_browse',
            )
        ),

        'headvancedalbum_photos_browse' => array(
            'route' => 'albums/photos/*',
            'defaults' => array(
                'module' => 'headvancedalbum',
                'controller' => 'index',
                'action' => 'index',
                'filter' => 'photos_browse',
            )
        ),

        'headvancedalbum_mine_albums' => array(
            'route' => 'albums/manage/*',
            'defaults' => array(
                'module' => 'album',
                'controller' => 'index',
                'action' => 'manage',
                'filter' => 'mine_albums',
            )
        ),

        'headvancedalbum_add_photos' => array(
            'route' => 'albums/upload/*',
            'defaults' => array(
                'module' => 'headvancedalbum',
                'controller' => 'index',
                'action' => 'upload',
            )
        ),

        'headvancedalbum_album_view' => array(
            'route' => 'albums/view/:album_id/*',
            'defaults' => array(
                'module' => 'headvancedalbum',
                'controller' => 'index',
                'action' => 'view',
                'album_id' => 0
            )
        ),

        'headvancedalbum_specific' => array(
            'route' => 'albums/:action/:album_id/*',
            'defaults' => array(
                'module' => 'album',
                'controller' => 'album',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|order)',
            ),
        ),

        'healbum_extended' => array(
            'route' => 'albums/photo/view/*',
            'defaults' => array(
                'module' => 'album',
                'controller' => 'photo',
                'action' => 'view'
            ),
        ),

    ),
); ?>