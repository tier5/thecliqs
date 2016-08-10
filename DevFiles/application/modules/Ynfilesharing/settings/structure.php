<?php defined("_ENGINE") or die("access denied"); return array (
  'menus' => 
  array (
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 131,
      'name' => 'core_main_ynfilesharing',
      'module' => 'ynfilesharing',
      'label' => 'File Sharing',
      'plugin' => '',
      'params' => '{"route":"ynfilesharing_general"}',
      'menu' => 'core_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    1 => 
    array (
      'id' => 132,
      'name' => 'ynfilesharing_main_browse',
      'module' => 'ynfilesharing',
      'label' => 'Browse Folders',
      'plugin' => '',
      'params' => '{"route":"ynfilesharing_general"}',
      'menu' => 'ynfilesharing_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    2 => 
    array (
      'id' => 133,
      'name' => 'ynfilesharing_main_manage',
      'module' => 'ynfilesharing',
      'label' => 'Manage My Folders',
      'plugin' => 'Ynfilesharing_Plugin_Menus',
      'params' => '{"route":"ynfilesharing_general","action":"manage"}',
      'menu' => 'ynfilesharing_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    3 => 
    array (
      'id' => 134,
      'name' => 'ynfilesharing_admin_main_level',
      'module' => 'ynfilesharing',
      'label' => 'Member Level Settings',
      'plugin' => NULL,
      'params' => '{"route":"admin_default","module":"ynfilesharing","controller":"level"}',
      'menu' => 'ynfilesharing_admin_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    4 => 
    array (
      'id' => 135,
      'name' => 'ynfilesharing_admin_main_settings',
      'module' => 'ynfilesharing',
      'label' => 'Global Settings',
      'plugin' => NULL,
      'params' => '{"route":"admin_default","module":"ynfilesharing","controller":"settings"}',
      'menu' => 'ynfilesharing_admin_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    5 => 
    array (
      'id' => 136,
      'name' => 'core_admin_plugins_ynfilesharing',
      'module' => 'ynfilesharing',
      'label' => 'YN - File Sharing',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynfilesharing","controller":"settings","action":"index"}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    6 => 
    array (
      'id' => 137,
      'name' => 'ynfilesharing_admin_main_scribd',
      'module' => 'ynfilesharing',
      'label' => 'Scribd Settings',
      'plugin' => NULL,
      'params' => '{"route":"admin_default","module":"ynfilesharing","controller":"scribd"}',
      'menu' => 'ynfilesharing_admin_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    7 => 
    array (
      'id' => 179,
      'name' => 'ynfilesharing_main_link',
      'module' => 'ynfilesharing',
      'label' => 'Manage Shared Links',
      'plugin' => 'Ynfilesharing_Plugin_Menus',
      'params' => '{"route":"ynfilesharing_general","controller":"link","action":"browse"}',
      'menu' => 'ynfilesharing_main',
      'submenu' => NULL,
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
  ),
  'mails' => 
  array (
  ),
  'jobtypes' => 
  array (
  ),
  'notificationtypes' => 
  array (
  ),
  'actiontypes' => 
  array (
  ),
  'permissions' => 
  array (
    0 => 
    array (
      0 => 'admin',
      1 => 'file',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    1 => 
    array (
      0 => 'admin',
      1 => 'folder',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    2 => 
    array (
      0 => 'admin',
      1 => 'folder',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    3 => 
    array (
      0 => 'admin',
      1 => 'folder',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    4 => 
    array (
      0 => 'admin',
      1 => 'folder',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    5 => 
    array (
      0 => 'admin',
      1 => 'folder',
      2 => 'edit_perm',
      3 => 1,
      4 => NULL,
    ),
    6 => 
    array (
      0 => 'admin',
      1 => 'folder',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    7 => 
    array (
      0 => 'moderator',
      1 => 'file',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    8 => 
    array (
      0 => 'moderator',
      1 => 'folder',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    9 => 
    array (
      0 => 'moderator',
      1 => 'folder',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    10 => 
    array (
      0 => 'moderator',
      1 => 'folder',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    11 => 
    array (
      0 => 'moderator',
      1 => 'folder',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    12 => 
    array (
      0 => 'moderator',
      1 => 'folder',
      2 => 'edit_perm',
      3 => 1,
      4 => NULL,
    ),
    13 => 
    array (
      0 => 'moderator',
      1 => 'folder',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    14 => 
    array (
      0 => 'user',
      1 => 'file',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    15 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'auth_ext',
      3 => 3,
      4 => '',
    ),
    16 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    17 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    18 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    19 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    20 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'edit_perm',
      3 => 1,
      4 => NULL,
    ),
    21 => 
    array (
      0 => 'user',
      1 => 'folder',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    22 => 
    array (
      0 => 'public',
      1 => 'folder',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
  ),
  'pages' => 
  array (
    'ynfilesharing_file_view' => 
    array (
      'page_id' => 25,
      'name' => 'ynfilesharing_file_view',
      'displayname' => 'FileSharing Preview Page',
      'url' => NULL,
      'title' => 'FileSharing Preview Page',
      'description' => 'This is the file sharing preview page.',
      'keywords' => '',
      'custom' => 0,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => '',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 673,
          'page_id' => 25,
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
              'content_id' => 674,
              'page_id' => 25,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 673,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 675,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynfilesharing.browse-menu',
                  'parent_content_id' => 674,
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
          'content_id' => 676,
          'page_id' => 25,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 677,
              'page_id' => 25,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 676,
              'order' => 6,
              'params' => '[]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 678,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 677,
                  'order' => 6,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 679,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'core.comments',
                  'parent_content_id' => 677,
                  'order' => 7,
                  'params' => '{"title":"Comments"}',
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
    'ynfilesharing_folder_view' => 
    array (
      'page_id' => 26,
      'name' => 'ynfilesharing_folder_view',
      'displayname' => 'FileSharing Folder View Page',
      'url' => NULL,
      'title' => 'FileSharing Folder View Page',
      'description' => 'This is the file sharing folder view page.',
      'keywords' => '',
      'custom' => 0,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => '',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 680,
          'page_id' => 26,
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
              'content_id' => 681,
              'page_id' => 26,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 680,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 682,
                  'page_id' => 26,
                  'type' => 'widget',
                  'name' => 'ynfilesharing.browse-menu',
                  'parent_content_id' => 681,
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
          'content_id' => 683,
          'page_id' => 26,
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
              'content_id' => 684,
              'page_id' => 26,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 683,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 685,
                  'page_id' => 26,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 684,
                  'order' => 6,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 686,
                  'page_id' => 26,
                  'type' => 'widget',
                  'name' => 'core.comments',
                  'parent_content_id' => 684,
                  'order' => 7,
                  'params' => '{"title":"Comments"}',
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
    'ynfilesharing_index_index' => 
    array (
      'page_id' => 27,
      'name' => 'ynfilesharing_index_index',
      'displayname' => 'FileSharing Home Page',
      'url' => NULL,
      'title' => 'FileSharing Home Page',
      'description' => 'This is the file sharing home page.',
      'keywords' => '',
      'custom' => 0,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => 'no-viewer;no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 687,
          'page_id' => 27,
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
              'content_id' => 688,
              'page_id' => 27,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 687,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 689,
                  'page_id' => 27,
                  'type' => 'widget',
                  'name' => 'ynfilesharing.browse-menu',
                  'parent_content_id' => 688,
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
          'content_id' => 690,
          'page_id' => 27,
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
              'content_id' => 691,
              'page_id' => 27,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 690,
              'order' => 5,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 692,
                  'page_id' => 27,
                  'type' => 'widget',
                  'name' => 'ynfilesharing.filesharing-search',
                  'parent_content_id' => 691,
                  'order' => 8,
                  'params' => '{"title":"","name":"ynfilesharing.filesharing-search"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 693,
              'page_id' => 27,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 690,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 694,
                  'page_id' => 27,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 693,
                  'order' => 6,
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
    'ynfilesharing_index_manage' => 
    array (
      'page_id' => 28,
      'name' => 'ynfilesharing_index_manage',
      'displayname' => 'FileSharing Manage Page',
      'url' => NULL,
      'title' => 'FileSharing Manage Page',
      'description' => 'This is the file sharing manage page.',
      'keywords' => '',
      'custom' => 0,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => '',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 695,
          'page_id' => 28,
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
              'content_id' => 696,
              'page_id' => 28,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 695,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 697,
                  'page_id' => 28,
                  'type' => 'widget',
                  'name' => 'ynfilesharing.browse-menu',
                  'parent_content_id' => 696,
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
          'content_id' => 698,
          'page_id' => 28,
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
              'content_id' => 699,
              'page_id' => 28,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 698,
              'order' => 5,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 700,
                  'page_id' => 28,
                  'type' => 'widget',
                  'name' => 'ynfilesharing.filesharing-search',
                  'parent_content_id' => 699,
                  'order' => 8,
                  'params' => '{"title":"","name":"ynfilesharing.filesharing-search"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 701,
              'page_id' => 28,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 698,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 702,
                  'page_id' => 28,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 701,
                  'order' => 6,
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
  ),
);?>