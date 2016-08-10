<?php return array(
  'package' =>
  array(
    'type' => 'module',
    'name' => 'donation',
    'version' => '4.2.2p7',
    'path' => 'application/modules/Donation',
    'title' => 'Donation',
    'description' => 'Hire-Experts Donations Plugin',
    'author' => 'Hire-Experts LLC',
    'meta' => array(
      'title' => 'Donation',
      'description' => 'Donation Plugin',
      'author' => 'Hire-Experts LLC',
    ),
    'callback' => array(
      'path' => 'application/modules/Donation/settings/install.php',
      'class' => 'Donation_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.1.8',
      ),
      array(
        'type' => 'module',
        'name' => 'like',
        'minVersion' => '4.2.1',
      )
    ),
    'actions' => array(
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array(
      0 => 'application/modules/Donation',
    ),
    'files' =>
    array(
      0 => 'application/languages/en/donation.csv',
    ),
  ),

  //Items
  'items' => array(
    'transaction',
    'donation_fin_info',
    'fundraise',
    'donation',
    'donation_photo',
    'donation_album',
  ),
  //Hooks -----------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Donation_Plugin_Core'
    )
   ),
  //Routes
  'routes' => array(
    'donation_extended' => array(
      'route' => 'donation/:controller/:action/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'donation_charity_browse' => array(
      'route' => 'donation/charities',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),

    'donation_project_browse' => array(
      'route' => 'donation/projects',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'project',
        'action' => 'browse',
      ),
    ),

    'donation_fundraise_browse' => array(
      'route' => 'donation/fundraisers',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'fundraise',
        'action' => 'browse',
      ),
    ),

    'donation_manage_donations' => array(
      'route' => 'donation/manage-donations',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'index',
        'action' => 'manage',
      ),
    ),

    'donation_general' => array(
      'route' => 'donations/:action/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'index',
        'action' => 'browse',
      ),
    ),
    'reqs' => array(
      'action' => '(browse|manage|create|edit|delete|view)',
    ),

    'fundraise_profile' => array(
      'route' => 'donation/fundraise/:fundraise_id/:title/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'fundraise',
        'action' => 'view',
      ),
      'reqs' => array(
        'fundraise_id' => '\d+'
      )
    ),

    'donation_profile' => array(
      'route' => 'donation/:donation_id/:title/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'index',
        'action' => 'view',
      ),
      'reqs' => array(
        'donation_id' => '\d+'
      )
    ),
    'donation_admin_manage' => array(
      'route' => 'admin/donation/manage/:action/:donation_id/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'admin-donations',
        'action' => 'index',
        'donation_id' => 0
      ),
      'reqs' => array(
        'donation_id' => '\d+'
      )
    ),
    'donation_fundraise' => array(
      'route' => 'donation/fundraise/:fundraise_id/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'fundraise',
        'action' => 'view',
      ),
      'reqs' => array(
        'fundraise_id' => '\d+'
      )
    ),
    'donation_donate' => array(
      'route' => 'making-donation/:action/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'donation',
        'action' => 'donate'
      ),
      'reqs' => array('action' => '(donate|finish|process|return|checkout)'),
    ),
    'donation_ipn' => array(
      'route' => 'donation-ipn/:action/*',
      'defaults' => array(
        'module' => 'donation',
        'controller' => 'ipn',
        'action' => 'index',
      ),
    ),
  ),
); ?>