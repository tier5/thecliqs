<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynresume',
    'version' => '4.01p1',
    'path' => 'application/modules/Ynresume',
    'title' => 'YN - Resume',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
        'path' => 'application/modules/Ynresume/settings/install.php',    
        'class' => 'Ynresume_Installer',
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
      0 => 'application/modules/Ynresume',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynresume.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
        'ynresume_resume',
        'ynresume_industry',
        'ynresume_order',
		'ynresume_transaction',
		'ynresume_education',
		'ynresume_recommendation',
		'ynresume_badge',
		'ynresume_award',
		'ynresume_photo',
		'ynresume_faq',
  		'ynresume_skill',
    	'ynresume_skill_map',
  		'ynresume_experience',
  		'ynresume_language',
  		'ynresume_project',
  		'ynresume_publication',
  		'ynresume_certification',
  		'ynresume_course',
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onYnjobpostingIndustryCreateAfter',
      'resource' => 'Ynresume_Plugin_Core',
    ),
    array(
      'event' => 'onYnjobpostingIndustryUpdateAfter',
      'resource' => 'Ynresume_Plugin_Core',
    ),
    array(
      'event' => 'onUserUpdateAfter',
      'resource' => 'Ynresume_Plugin_Core',
    ),
    array(
		'event' => 'onUserDeleteBefore',
		'resource' => 'Ynresume_Plugin_Core',
	),
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
    
		'ynresume_extended' => array(
			'route' => 'resume/:controller/:action/*',
			'defaults' => array(
				'module' => 'ynresume',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
		),
		
	  	 'ynresume_general' => array(
			'route' => 'resume/:action/*',
			'defaults' => array(
				'module' => 'ynresume',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|compose-message|get-custom-group|save-group|remove-group|get-my-location|create|who-viewed-me|import|my-favourite|my-saved|edit-resume|manage|get-photo|place-order|update-order|pay-credit|display-map-view|listing|save|favourite|unsave)',
	        )
		),
		
		'ynresume_specific' => array(
			'route' => 'resume/:action/:resume_id/*',
			'defaults' => array(
					'module' => 'ynresume',
					'controller' => 'resume',
					'action' => 'view',
			),
			'reqs' => array(				
					'action' => '(view|edit|delete|remove-photo|photo|service|edit-privacy|select-theme|sort|feature|render-section|upload-photos|delete-photo)',
					'resume_id' => '\d+',
			)
	    ),
	    
		 'ynresume_transaction' => array(
		      'route' => 'resume/transaction/:action/*',
		      'defaults' => array(
		        'module' => 'ynresume',
		        'controller' => 'transaction',
		        'action' => 'index'
		      )
 	   ),
 	   
	   'ynresume_recommend' => array(
		      'route' => 'resume/recommend/:action/*',
		      'defaults' => array(
		        'module' => 'ynresume',
		        'controller' => 'recommendation',
		        'action' => 'received'
		      )
 	   ),
  ),
); ?>
