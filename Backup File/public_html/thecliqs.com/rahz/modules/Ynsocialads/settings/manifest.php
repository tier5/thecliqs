<?php
return array(
	'package' => array(
		'type' => 'module',
		'name' => 'ynsocialads',
		'version' => '4.01p2',
		'path' => 'application/modules/Ynsocialads',
		'title' => 'YN - Social Ads',
		'description' => 'The plugin functions similarly to popular social ads platform nowadays, such as Facebook Ads where all business logic, workflow, terminology are simulated well.',
		'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
		'dependencies' => array(
	      array(
	         'type' => 'module',
	         'name' => 'core',
	         'minVersion' => '4.1.2',
	      ),
	    ),
		'callback' => array(
			'path' => 'application/modules/Ynsocialads/settings/install.php',
			'class' => 'Ynsocialads_Installer',
		),
		'actions' => array(
			0 => 'install',
			1 => 'upgrade',
			2 => 'refresh',
			3 => 'enable',
			4 => 'disable',
		),
		'directories' => array(0 => 'application/modules/Ynsocialads', ),
		'files' => array(0 => 'application/languages/en/ynsocialads.csv', ),
	),
	
	'hooks' => array (
		array (
			'event' => 'onRenderLayoutDefault',
			'resource' => 'Ynsocialads_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Ynsocialads_Plugin_Core',
        ),
    ),
	
	'items' => array(
		'ynsocialads_faq',
		'ynsocialads_campaign',
		'ynsocialads_transaction',
		'ynsocialads_ad',
		'ynsocialads_statistic',
		'ynsocialads_moneyrequest',
		'ynsocialads_module',
		'ynsocialads_track',
		'ynsocialads_package',
		'ynsocialads_packageblock',
		'ynsocialads_virtual',
		'ynsocialads_adblock',
		'ynsocialads_hidden',
		'ynsocialads_mapping',
		'ynsocialads_adtarget',

	),
	'routes' => array(
		'ynsocialads_extended' => array(
			'route' => 'socialads/:controller/:action/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
		),
		
		'ynsocialads_ads' => array(
			'route' => 'socialads/ads/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'ads',
				'action' => 'index',
			),
			'reqs' => array('action' => '(index)', ),
		),

		'ynsocialads_campaigns' => array(
			'route' => 'socialads/campaigns/:action/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'campaigns',
				'action' => 'index'
			),
		),

		'ynsocialads_account' => array(
			'route' => 'socialads/account/:action/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'account',
				'action' => 'index'
			),
			'reqs' => array('action' => '(request|index)')
		),

		'ynsocialads_faqs' => array(
			'route' => 'socialads/faqs/:action/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'faqs',
				'action' => 'index'
			),
			'reqs' => array('action' => '(index)')
		),

		'ynsocialads_ads' => array(
			'route' => 'socialads/ads/:action/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'ads',
				'action' => 'index'
			),
		),

		'ynsocialads_report' => array(
			'route' => 'socialads/report/:action/*',
			'defaults' => array(
				'module' => 'ynsocialads',
				'controller' => 'report',
				'action' => 'index'
			),
			'reqs' => array('action' => '(index)')
		),
		
		'ynsocialads_transaction' => array(
	      'route' => 'socialads/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'ynsocialads',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
	    ),
	)
);
?>
