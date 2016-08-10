<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynjobposting',
    'version' => '4.01p3',
    'path' => 'application/modules/Ynjobposting',
    'title' => 'YN - Job Posting',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Ynjobposting/settings/install.php',	
      'class' => 'Ynjobposting_Installer',
    ),
    'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.7',
            ),
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
      0 => 'application/modules/Ynjobposting',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynjobposting.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'ynjobposting_industry',
    'ynjobposting_company',
    'ynjobposting_package',
    'ynjobposting_job',
    'ynjobposting_faq',
    'ynjobposting_sponsor',
    'ynjobposting_package',
    'ynjobposting_order',
    'ynjobposting_feature',
    'ynjobposting_follow',
  	'ynjobposting_submission',
  	'ynjobposting_applynote',
  	'ynjobposting_mail_template',
  	'ynjobposting_alert',
  	'ynjobposting_sentjob',
  ),
  // Routes ---------------------------------------------------------------------
   'routes' => array(
	 'ynjobposting_extended' => array(
		'route' => 'job-posting/:controller/:action/*',
		'defaults' => array(
			'module' => 'ynjobposting',
			'controller' => 'index',
			'action' => 'index',
		),
		'reqs' => array(
			'controller' => '\D+',
			'action' => '\D+',
		)
	),
    
   	'ynjobposting_general' => array(
			'route' => 'job-posting/:action/*',
			'defaults' => array(
					'module' => 'ynjobposting',
					'controller' => 'index',
					'action' => 'index',
			),
			'reqs' => array(
					'action' => '(index|create|manage|get-my-location|display-map-view|delete-note)',
			)
	),
	
    'ynjobposting_job' => array(
        'route' => 'job-posting/jobs/:action/*',
        'defaults' => array(
            'module' => 'ynjobposting',
            'controller' => 'jobs',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '\D+',
        )
    ),
    
	'ynjobposting_transaction' => array(
	      'route' => 'job-posting/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'ynjobposting',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
	),
  ),
); ?>
