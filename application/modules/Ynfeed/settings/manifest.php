<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynfeed',
    'version' => '4.01p4',
    'path' => 'application/modules/Ynfeed',
    'title' => 'YN - Advanced Feed',
    'description' => 'This is advanced feed plugin.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Ynfeed',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynfeed.csv',
    ),
    'dependencies' => 
	    array (
	      0 => 
	      array (
	        'type' => 'module',
	        'name' => 'core',
	        'minVersion' => '4.7.0',
	      ),
	      1 => 
	      array (
	        'type' => 'module',
	        'name' => 'core',
	        'minVersion' => '4.7.0',
	      ),
	    ),
  ),
  // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'addActivity',
            'resource' => 'Ynfeed_Plugin_Core',
        ),
        array(
            'event' => 'getActivity',
            'resource' => 'Ynfeed_Plugin_Core',
        ),
        array(
            'event' => 'onItemCreateAfter',
            'resource' => 'Ynfeed_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
  'items' => array(
			'ynfeed_map',
			'ynfeed_content',
			'ynfeed_customtype',
			'ynfeed_list_item',
			'ynfeed_list',
			'ynfeed_welcome'
	),
	'routes' => array(
	'ynfeed_extended' => 
	    array (
	      'route' => 'ynfeed/:controller/:action/*',
	      'defaults' => 
	      array (
	        'module' => 'ynfeed',
	        'controller' => 'index',
	        'action' => 'index',
	      ),
	      'reqs' => 
	      array (
	        'controller' => '\\D+',
	        'action' => '\\D+',
	      ),
	    ),
	    'ynfeed_map' => array(
	      'route' => 'ynfeed/view-map/:map_id/*',
	      'defaults' => array(
	        'module' => 'ynfeed',
	        'controller' => 'index',
	        'action' => 'view-map',
	      ),
	   ),
	   'ynfeed_more_tagfriends' => array(
	      'route' => 'ynfeed/more-friend/:action_id/:friend_id/*',
	      'defaults' => array(
	        'module' => 'ynfeed',
	        'controller' => 'index',
	        'action' => 'more-friend',
	      ),
	   ),
	   'ynfeed_edit_post' => array(
	      'route' => 'ynfeed/edit-post/:action_id/*',
	      'defaults' => array(
	        'module' => 'ynfeed',
	        'controller' => 'index',
	        'action' => 'edit-post',
	      ),
	   ),
	),
); ?>