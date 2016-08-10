<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynrestapi',
    'version' => '4.02',
    'path' => 'application/modules/Ynrestapi',
    'title' => 'YN - SE API',
    'description' => 'YN - SE API',
    'author' => '<a href="https://socialengine.younetco.com/" target="_blank">YouNet Company/MisterWizard</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
    ),
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
      0 => 'application/modules/Ynrestapi',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynrestapi.csv',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'api_default' => array(
      'route' => 'api/*',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'default',
      ),
    ),
    'api_version_name' => array(
      'route' => 'api/:version/:name',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'version' => 'v\d+',
        'name' => '.+',
      ),
    ),
    'api_version_name_method' => array(
      'route' => 'api/:version/:name/:method',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'method',
      ),
      'reqs' => array(
        'version' => 'v\d+',
        'name' => '.+',
        'method' => '.+',
      ),
    ),
    'api_version_name_id' => array(
      'route' => 'api/:version/:name/:id',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'version' => 'v\d+',
        'name' => '.+',
        'id' => '\d+',
      ),
    ),
    'api_version_name_id_method' => array(
      'route' => 'api/:version/:name/:id/:method',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'method',
      ),
      'reqs' => array(
        'version' => 'v\d+',
        'name' => '.+',
        'id' => '\d+',
        'method' => '.+',
      ),
    ),
    'api_version_name_method_id' => array(
      'route' => 'api/:version/:name/:method/:id',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'method',
      ),
      'reqs' => array(
        'version' => 'v\d+',
        'name' => '.+',
        'method' => '.+',
        'id' => '\d+',
      ),
    ),
    // special routes
    'api_version_me_method' => array(
      'route' => 'api/:version/users/me',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'me',
      ),
      'reqs' => array(
        'version' => 'v\d+',
      ),
    ),
    'api_version_me_method' => array(
      'route' => 'api/:version/users/me/:method',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'index',
        'action' => 'me',
      ),
      'reqs' => array(
        'version' => 'v\d+',
        'method' => '.+',
      ),
    ),
    'api_version_oauth_authorize' => array(
      'route' => 'api/:version/oauth/authorize',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'oauth',
        'action' => 'authorize',
      ),
      'reqs' => array(
        'version' => 'v\d+',
      ),
    ),
    'api_version_oauth_token' => array(
      'route' => 'api/:version/oauth/token',
      'defaults' => array(
        'module' => 'ynrestapi',
        'controller' => 'oauth',
        'action' => 'token',
      ),
      'reqs' => array(
        'version' => 'v\d+',
      ),
    ),
  ),
); ?>