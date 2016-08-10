<?php defined("_ENGINE") or die("access denied"); return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'yncontest',
    'version' => '4.02p6',
    'path' => 'application/modules/Yncontest',
    'title' => 'YN - Contest',
    'description' => 'The purpose of Contest plug-in is to support YouNet products in Social Engine platform. At the first version this plug-in should be focused on Advanced Albums, Videos and Advanced Blogs.',
    'author' => 'YouNet Company/MisterWizard',
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
    ),
    'callback' => 
    array (
      'path' => 'application/modules/Yncontest/settings/install.php',
      'class' => 'Yncontest_Package_Installer',
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
      0 => 'application/modules/Yncontest',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/yncontest.csv',
      1 => 'application/modules/Music/externals/images/nophoto_playlist_thumb_profile.png'
    ),
  ),
  'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onItemCreateAfter',
      'resource' => 'Yncontest_Plugin_Core',
    ),
    1 => 
    array (
	    'event' => 'onItemUpdateAfter',
	    'resource' => 'Yncontest_Plugin_Core',
    ),	
  ),
  'items' => 
  array (
    0 => 'yncontest_contest',
    1 => 'yncontest_mailtemplate',
    2 => 'yncontest_list',
    3 => 'yncontest_list_item',
    4 => 'yncontest_list',
    5 => 'contest',
    6 => 'yncontest_follows',
    7 => 'yncontest_unfollow',
    8 => 'yncontest_favourite',
    9 => 'yncontest_entriesfavourites',
    10 => 'yncontest_unfavourite',
    11 => 'yncontest_category',
    12 => 'yncontest_location',
    13 => 'yncontest_entries',
    14 => 'yncontest_entry',
    15 => 'yncontest_announcements',
    16 => 'yncontest_members',
    17 => 'yncontest_awards',
    18 => 'yncontest_rules',
    19 => 'yncontest_transactions',
    20 => 'yncontest_settings',
    21 => 'yncontest_managerules',
    22 => 'yncontest_votes',
    23 => 'yncontest_entriesfollows',
    24 => 'yncontest_album',
    25 => 'yncontest_photo',
    26 => 'entry',
    27 => 'yncontest_order',
  ),
  'routes' => 
  array (
    'yncontest_general' => 
    array (
      'route' => 'contest/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(friend|index|entries|faventries|service|delete|listing|listing-compare|display-promote|display-promote-entry|promote|promote-entry|get-my-location|direction)',
      ),
    ),
    'yncontest_mycontest' => 
    array (
      'route' => 'contest/my-contest/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'my-contest',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|upload|list-photo|favcontest|followcontest|term|share|display-promote|promote|close|delete|create-contest|edit-contest|change-multi-level|publish|publish-admin|edit-contest|view|print-view|download|follow|un-follow|un-follow-ajax|favourite|un-favourite|un-favourite-ajax|create-announce|edit-announce|delete-announce|invite-members|ajax|join|leave|cancel|favourite-contest|service)',
      ),
    ),
    'yncontest_photo' => 
    array (
      'route' => 'contest/contest-photo/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'contest-photo',
        'action' => 'view',
      ),
      'reqs' => 
      array (
        'action' => '\\D+',
      ),
    ),
    'yncontest_myentries' => 
    array (
      'route' => 'contest/my-entries/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'my-entries',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|set-session-entry-win|give-award|ajax-search-entries|ajax-search-win-entries|ajax-win-entry-by-owner|submit|edit|approve-give-award|approve-entry|deny-entry|ajax-tab-entries|ajax-win-entries|ajax-participants|delete|ajax-entries-video|ajax-entries-blog|ajax-entries-photo|ajax-entries|get-value|view|suggest|suggest-entry|favourite-entries|get-entries-compare|entries-compare|vote|follow|unfollow|un-follow-ajax|favourite|un-favourite|un-favourite-ajax)',
      ),
    ),
    'yncontest_myrule' => 
    array (
      'route' => 'contest/my-rule/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'my-rule',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|manage-rule|manage-edit-rule|create-rule|edit-rule|delete-rule)',
      ),
    ),
    'yncontest_myaward' => 
    array (
      'route' => 'contest/my-award/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'my-award',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|manage-award|manage-edit-award|create-award|edit-award|delete-award)',
      ),
    ),
    'yncontest_mysetting' => 
    array (
      'route' => 'contest/my-setting/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'my-setting',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|create-contest-setting|edit-contest-setting)',
      ),
    ),
    'yncontest_payment' => 
    array (
      'route' => 'contest/payment/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'payment',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|method|update-order)',
      ),
    ),
    'yncontest_location' => 
    array (
      'route' => 'contest/location/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'location',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|suggest)',
      ),
    ),
    'yncontest_members' => 
    array (
      'route' => 'contest/my-members/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'my-members',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|ban-member|deny-member|approve-member|delete-member|statictis|rule|edit|member|participate|organizer|suggest)',
      ),
    ),
    'yncontest_rules' => 
    array (
      'route' => 'contest/manage-rules/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'manage-rules',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(create|delete|edit)',
      ),
    ),
    'yncontest_transaction' => 
    array (
      'route' => 'contest/transaction/:action/*',
      'defaults' => 
      array (
        'module' => 'yncontest',
        'controller' => 'transaction',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index)',
      ),
    ),
    'yncontest_transaction_process' => array(
	      'route' => 'contest/transaction-process/:action/*',
	      'defaults' => array(
	        'module' => 'yncontest',
	        'controller' => 'transaction-process',
	        'action' => 'index'
	      )
    ),
  ),
);?>