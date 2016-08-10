<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'page',
    'version' => '4.2.7p5',
    'path' => 'application/modules/Page',
    'title' => 'Page',
    'description' => 'Page Plugin',
    'author' => 'Hire-Experts LLC',
    'changeLog' => 'settings/changelog.php',
    'meta' => array(
      'title' => 'Page',
      'description' => 'Page Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.5',
      ),
      array(
        'type' => 'module',
        'name' => 'like',
        'minVersion' => '4.1.8',
      ),
    ),
    'callback' => array(
      'path' => 'application/modules/Page/settings/install.php',
      'class' => 'Page_Installer',
    ),
		'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'directories' => array(
      'application/modules/Page',
    ),
    'files' => array(
      'application/languages/en/page.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'like',
      'resource' => 'Page_Plugin_Core',
    ),
    array(
      'event' => 'unlike',
      'resource' => 'Page_Plugin_Core',
    ),
    array(
      'event' => 'onStatistics',
      'resource' => 'Page_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Page_Plugin_Core'
    ),
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Page_Plugin_Core',
    ),
    array(
      'event' => 'getActivity',
      'resource' => 'Page_Plugin_Core',
      'priority' => 999
    ),
    array(
      'event' => 'addActivity',
      'resource' => 'Page_Plugin_Core',
      'priority' => 999
    ),
    array(
      'event' => 'typeDelete',
      'resource' => 'Page_Plugin_Core'
    ),
    array(
      'event' => 'typeCreate',
      'resource' => 'Page_Plugin_Core'
    ),
  ),
	// Routes ---------------------------------------------------------------------
	'routes' => array(
     
  	'page_editor' => array(
      'route' => 'page-layout/:action/:page/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'editor',
        'action' => 'index',
    		'page' => ''       
      ),
    ),

    'page_team' => array(
      'route' => 'page-team/:action/:page_id/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'page',
    		'action' => 'index',
    		'page_id' => 0
      )
    ),
    
    'page_widget' => array(
      'route' => 'page-widget/:action/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'widget',
        'action' => 'index',
      ),
    ),
    
    'admin_general' => array(
      'route' => 'page-admin/:action',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'team',
    		'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(index|ajax)'
     	)
    ),


    'admin_editor' => array(
      'route' => 'admin-editor/:action',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'admin-editor',
    		'action' => 'index'
      )
    ),

    'admin_specific' => array(
      'route' => 'page-admin/:action/:admin_id',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'team',
    		'action' => 'index',
    		'admin_id' => 0
      ),
      'reqs' => array(
        'action' => '(index|create|remove|edit|change)'
     	)
    ),
    
    'page_stat' => array(
      'route' => 'page-stats/:page_id/:action/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'statistics',
    		'action' => 'visitors',
    		'page_id' => 0
      )
    ),
    
    'page_comment' => array(
      'route' => 'page-comment/:action/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'comment',
    		'action' => 'index',
      )
    ),
    
    'page_admin_manage_level' => array(
      'route' => 'admin/page/permission/level/:level_id',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'admin-permission',
        'action' => 'level'
      )
    ),

    'page_admin_manage' => array(
      'route' => 'admin/page/manage/:action/:page_id/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'admin-manage',
        'action' => 'index',
        'page_id' => 0
      ),
      'reqs' => array(
        'page_id' => '\d+'
      )
    ),
    'page_admin_packages' => array(
      'route' => 'admin/page/packages/:action/:package_id',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'admin-packages',
        'action' => 'index',
        'package_id' => ''
      )
    ),
    
    'page_view' => array(
      'route' => 'page/:page_id/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'view',
        'page_id' => 0
      )
    ),

    'page_print' => array(
      'route' => 'page-print/:page_id/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'print',
        'page_id' => 0,
        'format' => 'smoothbox'
      )
    ),
    
    'page_ajax' => array(
      'route' => 'page-ajax/:action',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(validate)'
      )
    ),
    
    'page_browse' => array(
      'route' => 'browse-pages/:page',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'index',
        'page' => 1
      ),
    ),

    'page_browse_sort' => array(
      'route' => 'browse-pages/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'index'
      ),
    ),

		'page_search' => array(
      'route' => 'search-pages/:action/:page_id/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'search',
        'action' => 'index',
        'page_id' => 0
      ),
    ),
    
    'page_manage' => array(
      'route' => 'manage-pages/:page',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'manage',
        'page' => 1
      ),
    ),
    
    'page_create' => array(
      'route' => 'create-pages/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'create',
      ),
    ),

    'page_claim' => array(
      'route' => 'claim-pages/:action/:id',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'claim',
        'id'          => 0
      ),
    ),

    'page_map' => array(
      'route' => 'page-map/:page_id/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'large-map',
        'page_id' => 0,
      ),
    ),

		'page_package_choose' => array(
			'route' => 'page-package/choose/*',
			'defaults' => array(
				'module' => 'page',
				'controller' => 'package',
				'action' => 'choose',
			),
		),

		'page_package' => array(
			'route' => 'page-package/:action/*',
			'defaults' => array(
				'module' => 'page',
				'controller' => 'package',
				'action' => 'gateway',
			),
      'reqs' => array(
        'action' => '(index|gateway|process|return|finish)',
      )
		),
    
	),
	// Items ---------------------------------------------------------------------
	'items' => array(
	  'page',
    'page_package',
		'page_subscription',
		'page_marker',
		'page_view',
		'page_list',
		'page_list_item',
		'page_tag',
    'page_tag_map',
    'term',
    'page_import'
	),
);