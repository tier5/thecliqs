<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynpayment',
    'version' => '4.05',
    'path' => 'application/modules/Ynpayment',
    'title' => 'YN - Advanced Payment Gateway',
    'description' => 'Advanced Payment Gateway',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' => 
    array (
       'path' => 'application/modules/Ynpayment/settings/install.php',
        'class' => 'Ynpayment_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'payment',
        'minVersion' => '4.7.0',
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
      0 => 'application/modules/Ynpayment',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynpayment.csv',
    ),
  ),
  
  
   // Items ---------------------------------------------------------------------
  'items' => array(
    'ynpayment_subscription',
  ),
  // Routes --------------------------------------------------------------------
  
  'routes' => array(   
    'ynpayment_admin_transaction' => array(
        'route' => 'admin/payment/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'admin-index',
            'action' => 'index'
        )
    ),
    'ynpayment_admin_setting' => array(
        'route' => 'admin/payment/settings/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'admin-settings',
            'action' => 'index'
        )
    ),
    'ynpayment_admin_gateway' => array(
        'route' => 'admin/payment/gateway/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'admin-gateway',
            'action' => 'index'
        )
    ),
    'ynpayment_admin_plan' => array(
        'route' => 'admin/payment/package/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'admin-package',
            'action' => 'index'
        )
    ),
    'ynpayment_admin_subscription' => array(
        'route' => 'admin/payment/subscription/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'admin-subscription',
            'action' => 'index'
        )
    ),
    'ynpayment_subscription' => array(
        'route' => 'payment/subscription/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'subscription',
            'action' => 'index'
        )
    ),
    'ynpayment_silent_post' => array(
        'route' => 'payment/silent-post/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'silent-post',
            'action' => 'index'
        )
    ),
    'ynpayment_post_back' => array(
        'route' => 'payment/post-back/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'post-back',
            'action' => 'index'
        )
    ),
    'ynpayment_paypackage' => array(
        'route' => 'payment/pay/:action/*',
        'defaults' => array(
            'module' => 'ynpayment',
            'controller' => 'pay',
            'action' => 'index'
        )
    ),
   )
); ?>
