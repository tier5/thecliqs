<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynlistings',
    'version' => '4.01p2',
    'path' => 'application/modules/Ynlistings',
    'title' => 'YN - Listings',
    'description' => 'This is YouNet Listings Module',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => array(
        'path' => 'application/modules/Ynlistings/settings/install.php',
        'class' => 'Ynlistings_Installer',
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
      0 => 'application/modules/Ynlistings',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynlistings.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
	'hooks' => array(
			array(
				'event' => 'onItemCreateAfter',
				'resource' => 'Ynlistings_Plugin_Core',
			),
			array (
			    'event' => 'onItemUpdateAfter',
			    'resource' => 'Ynlistings_Plugin_Core',
		    ),	
		    array (
			    'event' => 'onItemDeleteAfter',
			    'resource' => 'Ynlistings_Plugin_Core',
		    ),		
            array(
                'event' => 'onStatistics',
                'resource' => 'Ynlistings_Plugin_Core',
            ),   
	),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'ynlistings_mapping',
    'ynlistings_album',
    'ynlistings_photo',
    'ynlistings_category',
    'ynlistings_faq',
    'ynlistings_transaction',
    'ynlistings_listing',
    'ynlistings_order',
    'ynlistings_import',
    'ynlistings_topic',
    'ynlistings_post',
    'ynlistings_report',
    'ynlistings_review',
    'ynlistings_follow',
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
    
	'ynlistings_extended' => array(
		'route' => 'listings/:controller/:action/*',
		'defaults' => array(
			'module' => 'ynlistings',
			'controller' => 'index',
			'action' => 'index',
		),
		'reqs' => array(
			'controller' => '\D+',
			'action' => '\D+',
		)
	),
	
  	 'ynlistings_general' => array(
			'route' => 'listings/:action/*',
			'defaults' => array(
					'module' => 'ynlistings',
					'controller' => 'index',
					'action' => 'index',
			),
			'reqs' => array(
            'action' => '(index|follow|mobileview|view|export|import|manage|browse|place-order|update-order|pay-credit|edit|create|select-theme|get-my-location|delete|close|re-open|display-map-view|import-one-by-one|rollback-import|history-import|print)'
        )
	),
  	
	
	'ynlistings_specific' => array(
        'route' => 'listings/listings/:action/*',
        'defaults' => array(
            'module' => 'ynlistings',
            'controller' => 'listings',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(index|direction|email-to-friends|transfer-owner)'
        )
    ),
    
    'ynlistings_faqs' => array(
        'route' => 'listings/faqs/:action/*',
        'defaults' => array(
            'module' => 'ynlistings',
            'controller' => 'faqs',
            'action' => 'index'
        ),
        'reqs' => array('action' => '(index)')
    ),
    'ynlistings_transaction' => array(
	      'route' => 'listings/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'ynlistings',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
 	 ),
 	 'ynlistings_post' => array(
		'route' => 'listings/post/control/:action/*',
		'defaults' => array(
				'module' => 'ynlistings',
				'controller' => 'post',
				'action' => 'edit',
		),
		'reqs' => array(
				'action' => '(edit|delete|report)',
		)
	),
	
	'ynlistings_review' => array(
        'route' => 'listings/review/:action/*',
        'defaults' => array(
                'module' => 'ynlistings',
                'controller' => 'review',
                'action' => 'index',
        ),
        'reqs' => array(
                'action' => '(index|edit|delete)',
        )
    ),
  ),
  
); ?>
