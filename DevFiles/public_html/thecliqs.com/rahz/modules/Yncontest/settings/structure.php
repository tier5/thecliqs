<?php defined("_ENGINE") or die("access denied"); return array (
  'menus' => 
  array (
    0 => 
    array (
      'id' => 18,
      'name' => 'yncontest_main',
      'type' => 'standard',
      'title' => 'Yncontest Main Navigation Menu',
      'order' => 999,
    ),
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 164,
      'name' => 'core_main_yncontest',
      'module' => 'yncontest',
      'label' => 'Contest',
      'plugin' => '',
      'params' => '{"route":"yncontest_general"}',
      'menu' => 'core_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    1 => 
    array (
      'id' => 165,
      'name' => 'core_admin_main_plugins_yncontest',
      'module' => 'yncontest',
      'label' => 'Contest',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"manage"}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    2 => 
    array (
      'id' => 166,
      'name' => 'yncontest_admin_main_manage',
      'module' => 'yncontest',
      'label' => 'Manage Contest',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"manage"}',
      'menu' => 'yncontest_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    3 => 
    array (
      'id' => 167,
      'name' => 'yncontest_admin_main_statistic',
      'module' => 'yncontest',
      'label' => 'Statistics',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"statistic"}',
      'menu' => 'yncontest_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    4 => 
    array (
      'id' => 168,
      'name' => 'yncontest_admin_main_level',
      'module' => 'yncontest',
      'label' => 'Member Level Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"level"}',
      'menu' => 'yncontest_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 5,
    ),
    5 => 
    array (
      'id' => 169,
      'name' => 'yncontest_admin_main_settings',
      'module' => 'yncontest',
      'label' => 'Global Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"settings"}',
      'menu' => 'yncontest_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 6,
    ),
    7 =>
    array (
      'id' => 171,
      'name' => 'yncontest_admin_main_transactions',
      'module' => 'yncontest',
      'label' => 'Transactions',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"transaction"}',
      'menu' => 'yncontest_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 10,
    ),
    8 => 
    array (
      'id' => 172,
      'name' => 'yncontest_main_contest',
      'module' => 'yncontest',
      'label' => 'All Contests',
      'plugin' => '',
      'params' => '{"route":"yncontest_general"}',
      'menu' => 'yncontest_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    9 => 
    array (
      'id' => 173,
      'name' => 'yncontest_main_mycontests',
      'module' => 'yncontest',
      'label' => 'My Contests',
      'plugin' => 'Yncontest_Plugin_Menus::canMyContests',
      'params' => '{"route":"yncontest_mycontest"}',
      'menu' => 'yncontest_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    10 => 
    array (
      'id' => 174,
      'name' => 'yncontest_main_myentries',
      'module' => 'yncontest',
      'label' => 'My Entries',
      'plugin' => 'Yncontest_Plugin_Menus::canMyEntries',
      'params' => '{"route":"yncontest_myentries"}',
      'menu' => 'yncontest_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    11 => 
    array (
      'id' => 175,
      'name' => 'yncontest_main_create_contest',
      'module' => 'yncontest',
      'label' => 'Create New Contest',
      'plugin' => 'Yncontest_Plugin_Menus::canCreateContest',
      'params' => '{"route":"yncontest_mycontest","action":"create-contest"}',
      'menu' => 'yncontest_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 5,
    ),
    12 => 
    array (
      'id' => 176,
      'name' => 'yncontest_admin_main_categories',
      'module' => 'yncontest',
      'label' => 'Categories',
      'plugin' => 'Yncontest_Plugin_Menus::canAdminCategory',
      'params' => '{"route":"admin_default","module":"yncontest","controller":"categories"}',
      'menu' => 'yncontest_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 5,
    ),
    13 => 
    array (
      'id' => 177,
      'name' => 'yncontest_main_friendcontests',
      'module' => 'yncontest',
      'label' => 'Friend\'s Contests',
      'plugin' => 'Yncontest_Plugin_Menus::canFriendsContests',
      'params' => '{"route":"yncontest_general"}',
      'menu' => 'yncontest_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
  ),
  'mails' => 
  array (
    0 => 
    array (
      'mailtemplate_id' => 37,
      'type' => 'notify_contest_denied',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
    1 => 
    array (
      'mailtemplate_id' => 38,
      'type' => 'notify_close_contest',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
    2 => 
    array (
      'mailtemplate_id' => 39,
      'type' => 'notify_contest_approved',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
    3 => 
    array (
      'mailtemplate_id' => 40,
      'type' => 'notify_new_participant',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
    4 => 
    array (
      'mailtemplate_id' => 41,
      'type' => 'notify_submit_entry',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
    5 => 
    array (
      'mailtemplate_id' => 42,
      'type' => 'notify_register_service',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
    6 => 
    array (
      'mailtemplate_id' => 43,
      'type' => 'notify_entry_win_vote',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[vote_desc]',
    ),
    7 => 
    array (
      'mailtemplate_id' => 44,
      'type' => 'notify_entry_denied',
      'module' => 'yncontest',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]',
    ),
  ),
  'jobtypes' => 
  array (
  ),
  'notificationtypes' => 
  array (
    0 => 
    array (
      'type' => 'close_contest',
      'module' => 'yncontest',
      'body' => '{item:$subject} has just closed contest {item:$object}',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    1 => 
    array (
      'type' => 'close_contest_members',
      'module' => 'yncontest',
      'body' => '{item:$subject} has just closed contest {item:$object}',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    2 => 
    array (
      'type' => 'contest_accepted',
      'module' => 'yncontest',
      'body' => 'Your request to join the contest {item:$subject} has been approved.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    3 => 
    array (
      'type' => 'contest_approve',
      'module' => 'yncontest',
      'body' => '{item:$subject} has requested to join the contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    4 => 
    array (
      'type' => 'contest_approved',
      'module' => 'yncontest',
      'body' => '{item:$subject} has just approved your contest {item:$object}',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    5 => 
    array (
      'type' => 'contest_cancel_invite',
      'module' => 'yncontest',
      'body' => 'The contest {item:$subject} invitation has been cancel, please contact contest owner for more information.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    6 => 
    array (
      'type' => 'contest_denied',
      'module' => 'yncontest',
      'body' => '{item:$subject} has just denied your contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    7 => 
    array (
      'type' => 'contest_edited',
      'module' => 'yncontest',
      'body' => '{item:$subject} has just edited contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    8 => 
    array (
      'type' => 'contest_invite',
      'module' => 'yncontest',
      'body' => '{item:$subject} has invited you to the contest {item:$object}.',
      'is_request' => 1,
      'handler' => 'contest.widget.request-contest',
      'default' => 1,
    ),
    9 => 
    array (
      'type' => 'create_annoucement',
      'module' => 'yncontest',
      'body' => '{item:$subject} has just created new announcement contest {item:$object}',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    10 => 
    array (
      'type' => 'entry_denied',
      'module' => 'yncontest',
      'body' => 'Your entry {item:$subject} has just been denied in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    11 => 
    array (
      'type' => 'entry_no_win_vote',
      'module' => 'yncontest',
      'body' => 'Your entry {item:$subject} has just lost award in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    12 => 
    array (
      'type' => 'entry_no_win_vote_f',
      'module' => 'yncontest',
      'body' => 'Entry {item:$subject} has just lost award in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    13 => 
    array (
      'type' => 'entry_win_vote',
      'module' => 'yncontest',
      'body' => 'Your entry {item:$subject} won in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    14 => 
    array (
      'type' => 'entry_win_vote_f',
      'module' => 'yncontest',
      'body' => 'Entry {item:$subject} won in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    15 => 
    array (
      'type' => 'new_participant',
      'module' => 'yncontest',
      'body' => 'You have been new participant in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    16 => 
    array (
      'type' => 'new_participant_f',
      'module' => 'yncontest',
      'body' => 'You have new participant in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    17 => 
    array (
      'type' => 'new_vote',
      'module' => 'yncontest',
      'body' => '{item:$subject}  has just voted your entry {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    18 => 
    array (
      'type' => 'register_service',
      'module' => 'yncontest',
      'body' => 'You have successfully registered contest {item:$object}',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    19 => 
    array (
      'type' => 'submit_entry',
      'module' => 'yncontest',
      'body' => 'You have submited new entry in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    20 => 
    array (
      'type' => 'submit_entry_f',
      'module' => 'yncontest',
      'body' => '{item:$subject} has submited new entry in contest {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
  ),
  'actiontypes' => 
  array (
    0 => 
    array (
      'type' => 'comment_yncontest',
      'module' => 'yncontest',
      'body' => '{item:$subject} commented on {item:$owner}\'s {item:$object:Page}: {body:$body}',
      'enabled' => 1,
      'displayable' => 1,
      'attachable' => 1,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 0,
    ),
    1 => 
    array (
      'type' => 'yncontest_new',
      'module' => 'yncontest',
      'body' => '{item:$subject} created a new contest',
      'enabled' => 1,
      'displayable' => 5,
      'attachable' => 1,
      'commentable' => 3,
      'shareable' => 1,
      'is_generated' => 1,
    ),
    2 => 
    array (
      'type' => 'yncontest_update',
      'module' => 'yncontest',
      'body' => '{item:$subject} updated a contest',
      'enabled' => 1,
      'displayable' => 5,
      'attachable' => 1,
      'commentable' => 3,
      'shareable' => 1,
      'is_generated' => 1,
    ),
  ),
  'permissions' => 
  array (
    0 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    1 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'createcontests',
      3 => 1,
      4 => NULL,
    ),
    2 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'createentries',
      3 => 1,
      4 => NULL,
    ),
    3 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'deletecontests',
      3 => 1,
      4 => NULL,
    ),
    4 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'deleteentries',
      3 => 1,
      4 => NULL,
    ),
    5 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'editcontests',
      3 => 1,
      4 => NULL,
    ),
    6 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'editentries',
      3 => 1,
      4 => NULL,
    ),
    7 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'max_entries',
      3 => 0,
      4 => NULL,
    ),
    8 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'or_ban_user',
      3 => 1,
      4 => NULL,
    ),
    9 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'or_edit_entries',
      3 => 1,
      4 => NULL,
    ),
    10 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'or_give_award',
      3 => 1,
      4 => NULL,
    ),
    11 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    12 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'viewentries',
      3 => 1,
      4 => NULL,
    ),
    13 => 
    array (
      0 => 'admin',
      1 => 'contest',
      2 => 'voteentries',
      3 => 1,
      4 => NULL,
    ),
    14 => 
    array (
      0 => 'admin',
      1 => 'yncontest_entry',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    15 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    16 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'createcontests',
      3 => 1,
      4 => NULL,
    ),
    17 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'createentries',
      3 => 1,
      4 => NULL,
    ),
    18 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'deletecontests',
      3 => 1,
      4 => NULL,
    ),
    19 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'deleteentries',
      3 => 1,
      4 => NULL,
    ),
    20 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'editcontests',
      3 => 1,
      4 => NULL,
    ),
    21 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'editentries',
      3 => 1,
      4 => NULL,
    ),
    22 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'max_entries',
      3 => 0,
      4 => NULL,
    ),
    23 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'or_ban_user',
      3 => 1,
      4 => NULL,
    ),
    24 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'or_edit_entries',
      3 => 1,
      4 => NULL,
    ),
    25 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'or_give_award',
      3 => 1,
      4 => NULL,
    ),
    26 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    27 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'viewentries',
      3 => 1,
      4 => NULL,
    ),
    28 => 
    array (
      0 => 'moderator',
      1 => 'contest',
      2 => 'voteentries',
      3 => 1,
      4 => NULL,
    ),
    29 => 
    array (
      0 => 'moderator',
      1 => 'yncontest_entry',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    30 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    31 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'createcontests',
      3 => 1,
      4 => NULL,
    ),
    32 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'createentries',
      3 => 1,
      4 => NULL,
    ),
    33 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'deletecontests',
      3 => 1,
      4 => NULL,
    ),
    34 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'deleteentries',
      3 => 1,
      4 => NULL,
    ),
    35 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'editcontests',
      3 => 1,
      4 => NULL,
    ),
    36 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'editentries',
      3 => 1,
      4 => NULL,
    ),
    37 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'endingsoonC_fee',
      3 => 5,
      4 => NULL,
    ),
    38 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'featureC_fee',
      3 => 5,
      4 => NULL,
    ),
    39 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'max_entries',
      3 => 3,
      4 => '',
    ),
    40 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'or_ban_user',
      3 => 1,
      4 => NULL,
    ),
    41 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'or_edit_entries',
      3 => 1,
      4 => NULL,
    ),
    42 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'or_give_award',
      3 => 1,
      4 => NULL,
    ),
    43 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'premiumC_fee',
      3 => 5,
      4 => NULL,
    ),
    44 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'publishC_fee',
      3 => 5,
      4 => NULL,
    ),
    45 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    46 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'viewentries',
      3 => 1,
      4 => NULL,
    ),
    47 => 
    array (
      0 => 'user',
      1 => 'contest',
      2 => 'voteentries',
      3 => 1,
      4 => NULL,
    ),
    48 => 
    array (
      0 => 'user',
      1 => 'yncontest_entry',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    49 => 
    array (
      0 => 'public',
      1 => 'contest',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    50 => 
    array (
      0 => 'public',
      1 => 'contest',
      2 => 'viewentries',
      3 => 1,
      4 => NULL,
    ),
  ),
  'pages' => 
  array (
    'yncontest_index_entries' => 
    array (
      'page_id' => 44,
      'name' => 'yncontest_index_entries',
      'displayname' => 'All Entries Page',
      'url' => NULL,
      'title' => 'All Entries Page',
      'description' => 'The All Entries Page of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 766,
          'page_id' => 44,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 767,
              'page_id' => 44,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 766,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 768,
                  'page_id' => 44,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 767,
                  'order' => 6,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 769,
          'page_id' => 44,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 770,
              'page_id' => 44,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 769,
              'order' => 5,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 771,
                  'page_id' => 44,
                  'type' => 'widget',
                  'name' => 'yncontest.winning-entries',
                  'parent_content_id' => 770,
                  'order' => 8,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 772,
              'page_id' => 44,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 769,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 773,
                  'page_id' => 44,
                  'type' => 'widget',
                  'name' => 'yncontest.listing-entries',
                  'parent_content_id' => 772,
                  'order' => 6,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_index_index' => 
    array (
      'page_id' => 45,
      'name' => 'yncontest_index_index',
      'displayname' => 'Contest - Home Page',
      'url' => NULL,
      'title' => 'Contest - Home Page',
      'description' => 'The Homepage of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 774,
          'page_id' => 45,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 775,
              'page_id' => 45,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 774,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 776,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 775,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 777,
          'page_id' => 45,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 778,
              'page_id' => 45,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 777,
              'order' => 5,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 779,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.search-contest',
                  'parent_content_id' => 778,
                  'order' => 15,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 780,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-categories',
                  'parent_content_id' => 778,
                  'order' => 16,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 781,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.premium-contest',
                  'parent_content_id' => 778,
                  'order' => 17,
                  'params' => '{"title":"Premium Contests"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                3 => 
                array (
                  'content_id' => 782,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.top-contest',
                  'parent_content_id' => 778,
                  'order' => 18,
                  'params' => '{"title":"Top Contests"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                4 => 
                array (
                  'content_id' => 783,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.hot-contest',
                  'parent_content_id' => 778,
                  'order' => 19,
                  'params' => '{"title":"Hot Contests"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                5 => 
                array (
                  'content_id' => 784,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.tag',
                  'parent_content_id' => 778,
                  'order' => 20,
                  'params' => '{"title":"Tag"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 785,
              'page_id' => 45,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 777,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 786,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.featured-contest',
                  'parent_content_id' => 785,
                  'order' => 6,
                  'params' => '{"number":"10","slideshowtype":"featured","slider_action":"overlap","height":"400","nomobile":null,"title":"Featured Contests","name":"yncontest.featured-contest"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 787,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.ending-soon-contest',
                  'parent_content_id' => 785,
                  'order' => 7,
                  'params' => '{"title":"Ending Soon Contests","number":"4","height":"200","width":"200","nomobile":null,"name":"yncontest.ending-soon-contest"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 788,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'yncontest.new-contest',
                  'parent_content_id' => 785,
                  'order' => 8,
                  'params' => '{"title":"New Contests","number":"4","height":"200","width":"200","nomobile":null,"name":"yncontest.new-contest"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                3 => 
                array (
                  'content_id' => 789,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'core.container-tabs',
                  'parent_content_id' => 785,
                  'order' => 9,
                  'params' => '{"max":6}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                    0 => 
                    array (
                      'content_id' => 790,
                      'page_id' => 45,
                      'type' => 'widget',
                      'name' => 'yncontest.listing-entries-by-type',
                      'parent_content_id' => 789,
                      'order' => 10,
                      'params' => '{"typeyncontest":"advalbum","maxadvalbum":"10","maxynblog":"12","maxmp3music":"12","maxynvideo":"12","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"120","widthynblog":"250","heightmp3music":"120","widthmp3music":"250","title":"Photo Entries","nomobile":"0","name":"yncontest.listing-entries-by-type"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    1 => 
                    array (
                      'content_id' => 791,
                      'page_id' => 45,
                      'type' => 'widget',
                      'name' => 'yncontest.listing-entries-by-type',
                      'parent_content_id' => 789,
                      'order' => 11,
                      'params' => '{"typeyncontest":"ynvideo","maxadvalbum":"12","maxynblog":"12","maxmp3music":"12","maxynvideo":"10","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"120","widthynblog":"250","heightmp3music":"120","widthmp3music":"250","title":"Video Entries","nomobile":"0","name":"yncontest.listing-entries-by-type"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    2 => 
                    array (
                      'content_id' => 792,
                      'page_id' => 45,
                      'type' => 'widget',
                      'name' => 'yncontest.listing-entries-by-type',
                      'parent_content_id' => 789,
                      'order' => 12,
                      'params' => '{"typeyncontest":"ynblog","maxadvalbum":"12","maxynblog":"8","maxmp3music":"12","maxynvideo":"12","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"120","widthynblog":"265","heightmp3music":"120","widthmp3music":"250","title":"Blog Entries","nomobile":"0","name":"yncontest.listing-entries-by-type"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    3 => 
                    array (
                      'content_id' => 793,
                      'page_id' => 45,
                      'type' => 'widget',
                      'name' => 'yncontest.listing-entries-by-type',
                      'parent_content_id' => 789,
                      'order' => 13,
                      'params' => '{"typeyncontest":"mp3music","maxadvalbum":"12","maxynblog":"12","maxmp3music":"8","maxynvideo":"12","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"120","widthynblog":"250","heightmp3music":"120","widthmp3music":"250","title":"Music Entries","nomobile":"0","name":"yncontest.listing-entries-by-type"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    4 =>
                    array (
                      'content_id' => 794,
                      'page_id' => 45,
                      'type' => 'widget',
                      'name' => 'yncontest.listing-entries-by-type',
                      'parent_content_id' => 789,
                      'order' => 13,
                      'params' => '{"typeyncontest":"ynmusic","maxadvalbum":"12","maxynblog":"12","maxmp3music":"8","maxynvideo":"12","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"120","widthynblog":"250","heightmp3music":"120","widthmp3music":"250","title":"Social Music Entries","nomobile":"0","name":"yncontest.listing-entries-by-type"}',
                      'attribs' => NULL,
                      'ynchildren' =>
                      array (
                      ),
                    ),
                    5 =>
                    array (
                      'content_id' => 795,
                      'page_id' => 45,
                      'type' => 'widget',
                      'name' => 'yncontest.listing-entries-by-type',
                      'parent_content_id' => 789,
                      'order' => 13,
                      'params' => '{"typeyncontest":"ynultimatevideo","maxadvalbum":"12","maxynblog":"12","maxmp3music":"8","maxynvideo":"12","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"120","widthynblog":"250","heightmp3music":"120","widthmp3music":"250","title":"Ultimate Video Entries","nomobile":"0","name":"yncontest.listing-entries-by-type"}',
                      'attribs' => NULL,
                      'ynchildren' =>
                      array (
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_index_listing' => 
    array (
      'page_id' => 46,
      'name' => 'yncontest_index_listing',
      'displayname' => 'Contest - All Contests',
      'url' => NULL,
      'title' => 'Contest - All Contests',
      'description' => 'The All Contests Page of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 794,
          'page_id' => 46,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 795,
              'page_id' => 46,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 794,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 796,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 795,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 797,
          'page_id' => 46,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 798,
              'page_id' => 46,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 797,
              'order' => 5,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 799,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'yncontest.search-contest',
                  'parent_content_id' => 798,
                  'order' => 8,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 800,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'yncontest.hot-contest',
                  'parent_content_id' => 798,
                  'order' => 9,
                  'params' => '{"title":"Hot Contests"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 801,
              'page_id' => 46,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 797,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 802,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'yncontest.listing-search',
                  'parent_content_id' => 801,
                  'order' => 6,
                  'params' => '{"title":"","number":"16","height":"200","width":"200","nomobile":null,"name":"yncontest.listing-search"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-contest_favcontest' => 
    array (
      'page_id' => 47,
      'name' => 'yncontest_my-contest_favcontest',
      'displayname' => 'Contest - Favorite Contests',
      'url' => NULL,
      'title' => 'Contest - Favorite Contests',
      'description' => 'Favorite Contests',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 803,
          'page_id' => 47,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 804,
              'page_id' => 47,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 803,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 805,
                  'page_id' => 47,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 804,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 806,
                  'page_id' => 47,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-menu-mini',
                  'parent_content_id' => 804,
                  'order' => 4,
                  'params' => '{"title":""}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 807,
          'page_id' => 47,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 808,
              'page_id' => 47,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 807,
              'order' => 5,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
              ),
            ),
            1 => 
            array (
              'content_id' => 809,
              'page_id' => 47,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 807,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 1138,
                  'page_id' => 47,
                  'type' => 'widget',
                  'name' => 'yncontest.my-favorite-contests',
                  'parent_content_id' => 809,
                  'order' => 7,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-contest_followcontest' => 
    array (
      'page_id' => 48,
      'name' => 'yncontest_my-contest_followcontest',
      'displayname' => 'Contest - Follow Contests',
      'url' => NULL,
      'title' => 'Contest - Follow Contests',
      'description' => 'Follow Contests',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 811,
          'page_id' => 48,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 812,
              'page_id' => 48,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 811,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 813,
                  'page_id' => 48,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 812,
                  'order' => 3,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 814,
                  'page_id' => 48,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-menu-mini',
                  'parent_content_id' => 812,
                  'order' => 4,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 815,
          'page_id' => 48,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 816,
              'page_id' => 48,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 815,
              'order' => 5,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
              ),
            ),
            1 => 
            array (
              'content_id' => 817,
              'page_id' => 48,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 815,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 818,
                  'page_id' => 48,
                  'type' => 'widget',
                  'name' => 'yncontest.my-follow-contests',
                  'parent_content_id' => 817,
                  'order' => 6,
                  'params' => '{"title":""}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-contest_index' => 
    array (
      'page_id' => 49,
      'name' => 'yncontest_my-contest_index',
      'displayname' => 'Contest - My Contests',
      'url' => NULL,
      'title' => 'Contest - My Contests',
      'description' => 'The My Contests Page of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 819,
          'page_id' => 49,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 820,
              'page_id' => 49,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 819,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 821,
                  'page_id' => 49,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 820,
                  'order' => 3,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 822,
                  'page_id' => 49,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-menu-mini',
                  'parent_content_id' => 820,
                  'order' => 4,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                    0 => 
                    array (
                      'content_id' => 823,
                      'page_id' => 49,
                      'type' => 'widget',
                      'name' => 'yncontest.main-menu',
                      'parent_content_id' => 822,
                      'order' => 6,
                      'params' => NULL,
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 824,
          'page_id' => 49,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 825,
              'page_id' => 49,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 824,
              'order' => 5,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
              ),
            ),
            1 => 
            array (
              'content_id' => 826,
              'page_id' => 49,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 824,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 827,
                  'page_id' => 49,
                  'type' => 'widget',
                  'name' => 'yncontest.my-contests',
                  'parent_content_id' => 826,
                  'order' => 6,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-contest_view' => 
    array (
      'page_id' => 50,
      'name' => 'yncontest_my-contest_view',
      'displayname' => 'Contest - Detail Page',
      'url' => NULL,
      'title' => 'Contest - Detail Page',
      'description' => 'Detail page of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 828,
          'page_id' => 50,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 829,
              'page_id' => 50,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 828,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 830,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 829,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 831,
          'page_id' => 50,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 832,
              'page_id' => 50,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 831,
              'order' => 4,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 833,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-options',
                  'parent_content_id' => 832,
                  'order' => 6,
                  'params' => '{"title":"Contest Profile Options"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 834,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-addthis',
                  'parent_content_id' => 832,
                  'order' => 7,
                  'params' => '{"title":"Add this"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 835,
              'page_id' => 50,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 831,
              'order' => 5,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 836,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-participants',
                  'parent_content_id' => 835,
                  'order' => 21,
                  'params' => '{"title":"Participants","number":"9","nomobile":null,"name":"yncontest.profile-participants"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 837,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-most-item-entries',
                  'parent_content_id' => 835,
                  'order' => 22,
                  'params' => '{"title":"Most Viewed Entries","number":"5","type":"view_count","nomobile":"0","name":"yncontest.profile-most-item-entries"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 838,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-most-item-entries',
                  'parent_content_id' => 835,
                  'order' => 23,
                  'params' => '{"title":"Most Voted Entries","number":"5","type":"vote_count","nomobile":"0","name":"yncontest.profile-most-item-entries"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            2 => 
            array (
              'content_id' => 839,
              'page_id' => 50,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 831,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 840,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-information',
                  'parent_content_id' => 839,
                  'order' => 9,
                  'params' => '{"title":"Contest Profile Information"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 841,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-announce',
                  'parent_content_id' => 839,
                  'order' => 10,
                  'params' => '{"title":"Announcement Con"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 842,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'core.container-tabs',
                  'parent_content_id' => 839,
                  'order' => 11,
                  'params' => '{"max":"8","title":"","nomobile":"0","name":"core.container-tabs"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                    0 => 
                    array (
                      'content_id' => 843,
                      'page_id' => 50,
                      'type' => 'widget',
                      'name' => 'yncontest.profile-description',
                      'parent_content_id' => 842,
                      'order' => 12,
                      'params' => '{"title":"Description"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    1 => 
                    array (
                      'content_id' => 844,
                      'page_id' => 50,
                      'type' => 'widget',
                      'name' => 'yncontest.profile-award',
                      'parent_content_id' => 842,
                      'order' => 13,
                      'params' => '{"title":"Award","name":"yncontest.profile-award"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    2 => 
                    array (
                      'content_id' => 845,
                      'page_id' => 50,
                      'type' => 'widget',
                      'name' => 'yncontest.profile-winning-entries',
                      'parent_content_id' => 842,
                      'order' => 14,
                      'params' => '{"title":"Winning Entries","number":"6","heightadvalbum":"160","widthadvalbum":"155","heightynblog":"100","widthynblog":"250","nomobile":null,"name":"yncontest.profile-winning-entries"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    3 => 
                    array (
                      'content_id' => 846,
                      'page_id' => 50,
                      'type' => 'widget',
                      'name' => 'yncontest.profile-manage-winning-entries',
                      'parent_content_id' => 842,
                      'order' => 15,
                      'params' => '{"title":"Manage Winning Entries","number":"8","heightadvalbum":"138","widthadvalbum":"118","heightynblog":"100","widthynblog":"255","nomobile":null,"name":"yncontest.profile-manage-winning-entries"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    4 => 
                    array (
                      'content_id' => 847,
                      'page_id' => 50,
                      'type' => 'widget',
                      'name' => 'yncontest.submit-entry',
                      'parent_content_id' => 842,
                      'order' => 16,
                      'params' => '{"title":"Submit Entry","typeyncontest":"ynvideo","maxadvalbum":"6","maxynblog":"6","maxmp3music":"6","maxynvideo":"6","height":"102","width":"134","nomobile":"0","name":"yncontest.submit-entry"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                  ),
                ),
                3 => 
                array (
                  'content_id' => 848,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-participants-center',
                  'parent_content_id' => 839,
                  'order' => 17,
                  'params' => '{"title":"Participants","number":"9","height":"50","width":"150","nomobile":null,"name":"yncontest.profile-participants-center"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                4 => 
                array (
                  'content_id' => 849,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-tab',
                  'parent_content_id' => 839,
                  'order' => 18,
                  'params' => '{"title":"Entries","typeyncontest":"ynvideo","maxadvalbum":"9","maxynblog":"9","maxmp3music":"12","maxynvideo":"12","heightadvalbum":"200","widthadvalbum":"180","heightynvideo":"200","widthynvideo":"180","heightynblog":"130","widthynblog":"250","heightmp3music":"130","widthmp3music":"250","nomobile":"0","name":"yncontest.profile-tab"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                5 => 
                array (
                  'content_id' => 891,
                  'page_id' => 50,
                  'type' => 'widget',
                  'name' => 'yncontest.item-comment',
                  'parent_content_id' => 839,
                  'order' => 19,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-entries_index' => 
    array (
      'page_id' => 51,
      'name' => 'yncontest_my-entries_index',
      'displayname' => 'Contest - My Entry',
      'url' => NULL,
      'title' => 'Contest - My Entry',
      'description' => 'The My Entry Page of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 851,
          'page_id' => 51,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 852,
              'page_id' => 51,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 851,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 853,
                  'page_id' => 51,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 852,
                  'order' => 3,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 854,
          'page_id' => 51,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 855,
              'page_id' => 51,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 854,
              'order' => 5,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
              ),
            ),
            1 => 
            array (
              'content_id' => 856,
              'page_id' => 51,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 854,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 857,
                  'page_id' => 51,
                  'type' => 'widget',
                  'name' => 'yncontest.my-entries',
                  'parent_content_id' => 856,
                  'order' => 6,
                  'params' => '{"title":"","typeyncontest":"advalbum","heightadvalbum":"160","widthadvalbum":"155","heightynvideo":"160","widthynvideo":"155","heightynblog":"90","widthynblog":"250","heightmp3music":"90","widthmp3music":"250","nomobile":"0","name":"yncontest.my-entries"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-entries_view' => 
    array (
      'page_id' => 52,
      'name' => 'yncontest_my-entries_view',
      'displayname' => 'Contest - Entry Detail',
      'url' => NULL,
      'title' => 'Contest - Entry Detail',
      'description' => 'The Entry Detail Page of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 858,
          'page_id' => 52,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 859,
              'page_id' => 52,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 858,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 860,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 859,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 861,
          'page_id' => 52,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 862,
              'page_id' => 52,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 861,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 863,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-options',
                  'parent_content_id' => 862,
                  'order' => 6,
                  'params' => '{"title":"Contest Profile Options"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 864,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-addthis',
                  'parent_content_id' => 862,
                  'order' => 7,
                  'params' => '{"title":"Add this"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 865,
              'page_id' => 52,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 861,
              'order' => 5,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 866,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-participants',
                  'parent_content_id' => 865,
                  'order' => 12,
                  'params' => '{"title":"","number":"9","nomobile":null,"name":"yncontest.profile-participants"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 867,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-most-item-entries',
                  'parent_content_id' => 865,
                  'order' => 13,
                  'params' => '{"title":"Most Viewed Entries","number":"5","type":"view_count","nomobile":"0","name":"yncontest.profile-most-item-entries"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 868,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.profile-most-item-entries',
                  'parent_content_id' => 865,
                  'order' => 14,
                  'params' => '{"title":"Most Voted Entries","number":"5","type":"vote_count","nomobile":"0","name":"yncontest.profile-most-item-entries"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            2 => 
            array (
              'content_id' => 869,
              'page_id' => 52,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 861,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 870,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 869,
                  'order' => 9,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 890,
                  'page_id' => 52,
                  'type' => 'widget',
                  'name' => 'yncontest.item-comment',
                  'parent_content_id' => 869,
                  'order' => 10,
                  'params' => '{"title":"","name":"yncontest.item-comment"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_my-members_statictis' => 
    array (
      'page_id' => 53,
      'name' => 'yncontest_my-members_statictis',
      'displayname' => 'Contest - Manage Statictis',
      'url' => NULL,
      'title' => 'Contest - Manage Statistics',
      'description' => 'Statistics of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 872,
          'page_id' => 53,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 873,
              'page_id' => 53,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 872,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 874,
                  'page_id' => 53,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 873,
                  'order' => 3,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 875,
                  'page_id' => 53,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-menu-mini',
                  'parent_content_id' => 873,
                  'order' => 4,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 876,
          'page_id' => 53,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 877,
              'page_id' => 53,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 876,
              'order' => 5,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
              ),
            ),
            1 => 
            array (
              'content_id' => 878,
              'page_id' => 53,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 876,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 879,
                  'page_id' => 53,
                  'type' => 'widget',
                  'name' => 'yncontest.statictis-contest',
                  'parent_content_id' => 878,
                  'order' => 6,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'yncontest_transaction_index' => 
    array (
      'page_id' => 54,
      'name' => 'yncontest_transaction_index',
      'displayname' => 'Contest - Manage Transaction',
      'url' => NULL,
      'title' => 'Contest - Manage Transaction',
      'description' => 'Manage Transaction of Yncontest module.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 880,
          'page_id' => 54,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 881,
              'page_id' => 54,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 880,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 882,
                  'page_id' => 54,
                  'type' => 'widget',
                  'name' => 'yncontest.main-menu',
                  'parent_content_id' => 881,
                  'order' => 3,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 883,
                  'page_id' => 54,
                  'type' => 'widget',
                  'name' => 'yncontest.contest-menu-mini',
                  'parent_content_id' => 881,
                  'order' => 4,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 884,
          'page_id' => 54,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 885,
              'page_id' => 54,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 884,
              'order' => 5,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
              ),
            ),
            1 => 
            array (
              'content_id' => 886,
              'page_id' => 54,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 884,
              'order' => 6,
              'params' => '',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 887,
                  'page_id' => 54,
                  'type' => 'widget',
                  'name' => 'yncontest.transaction',
                  'parent_content_id' => 886,
                  'order' => 6,
                  'params' => NULL,
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);?>