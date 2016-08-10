<?php 
ob_start();
$route = 'business-page';
$module='';
$controller='';
$action='';
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
    $module = $request->getModuleName(); 
    $action = $request->getActionName();
    $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
	$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynbusinesspages_pathname', 'business-page');
}

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynbusinesspages',
    'version' => '4.01p7',
    'path' => 'application/modules/Ynbusinesspages',
    'title' => 'YN - Business Pages',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'core',
         'minVersion' => '4.1.2',
      ),
    ),
    'callback' => 
    array (
        'path' => 'application/modules/Ynbusinesspages/settings/install.php',    
        'class' => 'Ynbusinesspages_Installer',
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
      0 => 'application/modules/Ynbusinesspages',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynbusinesspages.csv',
      1 => 'application/modules/Ynbusinesspages/views/scripts/dashboard/package.tpl'
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
  	'ynbusinesspages_feature',
    'ynbusinesspages_category',
    'ynbusinesspages_faq',
    'ynbusinesspages_creator',
	'ynbusinesspages_business',
	'ynbusinesspages_comparisonfield',
	'ynbusinesspages_mail_template',
	'ynbusinesspages_package',
	'ynbusinesspages_packagemodule',
	'ynbusinesspages_module',
	'ynbusinesspages_order',
	'ynbusinesspages_transaction',
	'ynbusinesspages_location',
	'ynbusinesspages_cover',
  	'ynbusinesspages_list',
  	'ynbusinesspages_list_item',
  	'ynbusinesspages_claimrequest',
  	'ynbusinesspages_album',
  	'ynbusinesspages_photo',
  	'ynbusinesspages_topic',
  	'ynbusinesspages_post',
  	'ynbusinesspages_announcement',
  	'ynbusinesspages_review',
  	'ynbusinesspages_contact',
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onItemDeleteAfter',
      'resource' => 'Ynbusinesspages_Plugin_Core',
    ),
    array(
      'event' => 'onItemCreateAfter',
      'resource' => 'Ynbusinesspages_Plugin_Core',
    ),
    array(
      'event' => 'onItemUpdateAfter',
      'resource' => 'Ynbusinesspages_Plugin_Core',
    ),
     array(
      'event' => 'addActivity',
      'resource' => 'Ynbusinesspages_Plugin_Core'
    ),
    array(
      'event' => 'getActivity',
      'resource' => 'Ynbusinesspages_Plugin_Core'
    ),
	array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Ynbusinesspages_Plugin_Core',
    ),
    array(
      'event' => 'onStatistics',
      'resource' => 'Ynbusinesspages_Plugin_Core',
    ),
  ),
  // Routes ---------------------------------------------------------------------
  'routes' => array(
    
	'ynbusinesspages_extended' => array(
		'route' => $route.'/:controller/:action/*',
		'defaults' => array(
			'module' => 'ynbusinesspages',
			'controller' => 'index',
			'action' => 'index',
		),
		'reqs' => array(
			'controller' => '\D+',
			'action' => '\D+',
		)
	),
	
  	 'ynbusinesspages_general' => array(
		'route' => $route.'/:action/*',
		'defaults' => array(
			'module' => 'ynbusinesspages',
			'controller' => 'index',
			'action' => 'index',
		),
		'reqs' => array(
            'action' => '(index|listing|manage|create|delete-claim|claim-business|create-step-one|create-step-two|create-for-claiming|add-info|get-my-location|founder-suggest|create-for-claiming|place-order|update-order|pay-credit|direction|print|login-as-business|logout-business|manage-claim|manage-follow|manage-favourite|warning|get-category|display-map-view|business-badge|compose-message)',
        )
	),
  	
	'ynbusinesspages_profile' => array(
			'route' => $route.'/:id/:slug/*',
			'defaults' => array(
					'module' => 'ynbusinesspages',
					'controller' => 'profile',
					'action' => 'index',
					'slug' => '',
			),
			'reqs' => array(
					'id' => '\d+',
			)
	),
	
	'ynbusinesspages_specific' => array(
			'route' => $route.'/:action/:business_id/*',
			'defaults' => array(
					'module' => 'ynbusinesspages',
					'controller' => 'business',
					'action' => 'index',
			),
			'reqs' => array(				
					'action' => '(open-close|delete|transfer|tooltip|edit|add-to-compare|profile-follow|profile-favourite|un-follow|un-favourite|promote|checkin|remove-item|get-people-checkin)',
					'business_id' => '\d+',
			)
	),
	
    'ynbusinesspages_transaction' => array(
	      'route' => $route.'/transaction/:action/*',
	      'defaults' => array(
	        'module' => 'ynbusinesspages',
	        'controller' => 'transaction',
	        'action' => 'index'
	      )
 	 ),
	 
	'ynbusinesspages_dashboard' => array(
	      'route' => $route.'/dashboard/:action/:business_id/*',
	      'defaults' => array(
	        'module' => 'ynbusinesspages',
	        'controller' => 'dashboard',
	        'action' => 'statistics'
	      ),
	      'reqs' => array(
	      	'action' => '(statistics|package|package-change|renewal-notification|module|cover|delete-cover|manage-role|role-setting|feature|theme|add-role|edit-role|delete-role|chart-data|delete-photo)',
	        'business_id' => '\d+',
	      ),
 	 ),
 	 
 	 'ynbusinesspages_dashboard_page' => array(
	      'route' => $route.'/dashboard/manage-business-page/:business_id/*',
	      'defaults' => array(
	        'module' => 'ynbusinesspages',
	        'controller' => 'layout',
	        'action' => 'index'
	      ),
	      'reqs' => array(
	        'business_id' => '\d+',
	      ),
 	 ),
 	 'ynbusinesspages_announcement' => array(
	      'route' => $route.'/annoucement/:action/:business_id/*',
	      'defaults' => array(
	        'module' => 'ynbusinesspages',
	        'controller' => 'announcement',
	        'action' => 'manage'
	      ),
	      'reqs' => array(
	      	'action' => '(manage|create|edit|delete|deleteselected|mark)',
	        'business_id' => '\d+',
	      ),
 	 ),
    'ynbusinesspages_compare' => array(
        'route' => $route.'/compare/:action/*',
            'defaults' => array(
            'module' => 'ynbusinesspages',
            'controller' => 'compare',
            'action' => 'index'
        ),
    ),
    'ynbusinesspages_view_folder' => array(
        'route' => $route.'/view-folder/:business_id/:folder_id/:slug',
            'defaults' => array(
            'module' => 'ynbusinesspages',
            'controller' => 'file',
            'action' => 'view-folder'
        ),
    ),
    'ynbusinesspages_review' => array(
        'route' => $route.'/review/:action/*',
        'defaults' => array(
                'module' => 'ynbusinesspages',
                'controller' => 'review',
                'action' => 'index',
        ),
        'reqs' => array(
                'action' => '(index|edit|delete)',
        )
    ),
    'ynbusinesspages_contact' => array(
        'route' => $route.'/contact/:action/:id/*',
        'defaults' => array(
                'module' => 'ynbusinesspages',
                'controller' => 'contact',
                'action' => 'edit',
        ),
        'reqs' => array(
                'action' => '(edit|add-question|add-receiver|delete-receiver|delete-question|edit-question)',
                'id' => '\d+',
        )
    ),
  ),
  
); ?>