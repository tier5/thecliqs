<?php return array (
		'package' =>
		array (
			'type' => 'module',
			'name' => 'yncredit',
			'version' => '4.01p5',
			'path' => 'application/modules/Yncredit',
			'title' => 'YN - User Credits',
			'description' => '',
			'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
			'callback' =>
			array (
				'path' => 'application/modules/Yncredit/settings/install.php',
      			'class' => 'Yncredit_Installer',
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
					0 => 'application/modules/Yncredit',
			),
			'files' =>
			array (
					0 => 'application/languages/en/yncredit.csv',
			),
			'dependencies' =>
			array (
					0 =>
					array (
							'type' => 'module',
							'name' => 'core',
							'minVersion' => '4.1.2',
					),
			),
	),
	'items' => 
	array(
			'yncredit_balance'
	),
	'hooks' =>
	array (
			0 =>
			array (
					'event' => 'onItemCreateAfter',
					'resource' => 'Yncredit_Plugin_Core',
			),
			1 =>
			array (
					'event' => 'onUserLoginAfter',
					'resource' => 'Yncredit_Plugin_Core',
			),
			2 =>
			array (
					'event' => 'onRenderLayoutDefault',
					'resource' => 'Yncredit_Plugin_Core',
			),
			3 => 
		    array (
		      'event' => 'onPaymentSubscriptionUpdateAfter',
		      'resource' => 'Yncredit_Plugin_Core',
		    ),
		    4 => 
			array (
			  'event' => 'onPaymentAfter',
			  'resource' => 'Yncredit_Plugin_Core',
			),
			5 => 
			array (
			  'event' => 'onUserUpdateBefore',
			  'resource' => 'Yncredit_Plugin_Core',
			),
			6 => 
			array (
			  'event' => 'onPurchaseItemAfter',
			  'resource' => 'Yncredit_Plugin_Core',
			),
			7 => 
			array (
			  'event' => 'onPublishItemAfter',
			  'resource' => 'Yncredit_Plugin_Core',
			),
	),
	
	'items' =>
	array (
			0 => 'yncredit',
			2 => 'yncredit_action',
			3 => 'yncredit_log',
			4 => 'yncredit_balance',
			5 => 'yncredit_package',
	),
	
	'routes' =>
	array (
		'yncredit_extended' =>
		array (
				'route' => 'credit/:controller/:action/*',
				'defaults' =>
				array (
						'module' => 'yncredit',
						'controller' => 'index',
						'action' => 'index',
				),
				'reqs' =>
				array (
						'controller' => '\\D+',
						'action' => '\\D+',
				),
		),
		'yncredit_general' =>
		array (
			'route' => 'credit/:action/*',
			'defaults' =>
			array (
					'module' => 'yncredit',
					'controller' => 'index',
					'action' => 'index',
			),
			'reqs' =>
			array (
					'action' => '(index|check-send-credit|send-credit|profile-send-credit)',
			),
		),
		'yncredit_my' =>
		array (
				'route' => 'credit/my/*',
				'defaults' =>
				array (
					'module' => 'yncredit',
					'controller' => 'profile',
					'action' => 'index',
				),
				'reqs' =>
				array (
						'action' => '(index)',
				),
		),
		'yncredit_faq' =>
		array (
				'route' => 'credit/faqs/*',
				'defaults' =>
				array (
						'module' => 'yncredit',
						'controller' => 'faq',
						'action' => 'index',
				),
				'reqs' =>
				array (
						'action' => '(index)',
				),
		),
		 'yncredit_package' => array(
	      'route' => 'credit/buy-package/:action',
	      'defaults' => array(
	        'module' => 'yncredit',
	        'controller' => 'buy-package',
	        'action' => 'index'
	      )
	    ),
	
	    'yncredit_transaction' => array(
	      'route' => 'credit/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'yncredit',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
	    ),
	    'yncredit_spend' => array(
	      'route' => 'credit/spend-credit/:action/:action_type/:item_id/*',
	      'defaults' => array(
	        'module' => 'yncredit',
	        'controller' => 'spend-credit',
	        'action' => 'confirm'
	      )
	    )
	),
); ?>