<?php return array (
  'menus' => 
  array (
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 252,
      'name' => 'album_main_ynmediaimporter',
      'module' => 'ynmediaimporter',
      'label' => 'Import',
      'plugin' => 'Ynmediaimporter_Plugin_Menus::canImport',
      'params' => '{"route":"ynmediaimporter_extended"}',
      'menu' => 'album_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    1 => 
    array (
      'id' => 253,
      'name' => 'ynmediaimporter_main_facebook',
      'module' => 'ynmediaimporter',
      'label' => 'facebook',
      'plugin' => 'Ynmediaimporter_Plugin_Menus::canImportFromFacebook',
      'params' => '{"route":"ynmediaimporter_extended","controller":"facebook","reset":1}',
      'menu' => 'ynmediaimporter_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    2 => 
    array (
      'id' => 254,
      'name' => 'ynmediaimporter_main_flickr',
      'module' => 'ynmediaimporter',
      'label' => 'flickr',
      'plugin' => 'Ynmediaimporter_Plugin_Menus::canImportFromFlickr',
      'params' => '{"route":"ynmediaimporter_extended","controller":"flickr","reset":1}',
      'menu' => 'ynmediaimporter_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    3 => 
    array (
      'id' => 255,
      'name' => 'ynmediaimporter_main_picasa',
      'module' => 'ynmediaimporter',
      'label' => 'picasa',
      'plugin' => 'Ynmediaimporter_Plugin_Menus::canImportFromPicasa',
      'params' => '{"route":"ynmediaimporter_extended","controller":"picasa","reset":1}',
      'menu' => 'ynmediaimporter_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    4 => 
    array (
      'id' => 256,
      'name' => 'ynmediaimporter_main_instagram',
      'module' => 'ynmediaimporter',
      'label' => 'instagram',
      'plugin' => 'Ynmediaimporter_Plugin_Menus::canImportFromInstagram',
      'params' => '{"route":"ynmediaimporter_extended","controller":"instagram","reset":1}',
      'menu' => 'ynmediaimporter_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    5 => 
    array (
      'id' => 257,
      'name' => 'core_main_ynmediaimporter',
      'module' => 'ynmediaimporter',
      'label' => 'Social Media Importer',
      'plugin' => '',
      'params' => '{"route":"ynmediaimporter_general","reset":1}',
      'menu' => 'core_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 100,
    ),
    6 => 
    array (
      'id' => 258,
      'name' => 'core_admin_main_plugins_ynmeidaimporter',
      'module' => 'ynmediaimporter',
      'label' => 'Social Media Importer',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmediaimporter","controller":"settings","reset":1}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    7 => 
    array (
      'id' => 259,
      'name' => 'ynmediaimporter_admin_main_settings',
      'module' => 'ynmediaimporter',
      'label' => 'Global Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmediaimporter","controller":"settings","reset":1}',
      'menu' => 'ynmediaimporter_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    8 => 
    array (
      'id' => 260,
      'name' => 'ynmediaimporter_admin_main_providers',
      'module' => 'ynmediaimporter',
      'label' => 'Provider Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynmediaimporter","controller":"settings","action":"providers","reset":1}',
      'menu' => 'ynmediaimporter_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    /*
    9 => 
    array (
      'id' => 261,
      'name' => 'ynmediaimporter_main_yfrog',
      'module' => 'ynmediaimporter',
      'label' => 'yfrog',
      'plugin' => 'Ynmediaimporter_Plugin_Menus::canImportFromYFrog',
      'params' => '{"route":"ynmediaimporter_extended","controller":"yfrog","reset":1}',
      'menu' => 'ynmediaimporter_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 6,
    ),*/
  ),
  'mails' => 
  array (
  ),
  'jobtypes' => 
  array (
  ),
  'notificationtypes' => 
  array (
    0 => 
    array (
      'type' => 'ynmediaimporter_imported',
      'module' => 'ynmediaimporter',
      'body' => 'Your media imported successful {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
  ),
  'actiontypes' => 
  array (
    0 => 
    array (
      'type' => 'ynmediaimporter_imported',
      'module' => 'ynmediaimporter',
      'body' => '{item:$subject} imported photos in {item:$object}',
      'enabled' => 1,
      'displayable' => 7,
      'attachable' => 1,
      'commentable' => 3,
      'shareable' => 1,
      'is_generated' => 0,
    ),
  ),
  'permissions' => 
  array (
  ),
  'pages' => 
  array (
    'ynmediaimporter_facebook_index' => 
    array (
      'page_id' => 43,
      'name' => 'ynmediaimporter_facebook_index',
      'displayname' => 'Social Media Importer - Facebook',
      'url' => NULL,
      'title' => 'Social Media Importer - Facebook',
      'description' => 'Social Media Importer - Facebook',
      'keywords' => 'Social Media Importer, Facebook',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => 'no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 778,
          'page_id' => 43,
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
              'content_id' => 779,
              'page_id' => 43,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 778,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 840,
                  'page_id' => 43,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.browse-menu',
                  'parent_content_id' => 779,
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
          'content_id' => 776,
          'page_id' => 43,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 780,
              'page_id' => 43,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 776,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 841,
                  'page_id' => 43,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.facebook-menu',
                  'parent_content_id' => 780,
                  'order' => 6,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 777,
              'page_id' => 43,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 776,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 842,
                  'page_id' => 43,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.media-browse',
                  'parent_content_id' => 777,
                  'order' => 8,
                  'params' => '{"title":"","nomobile":"0","name":"ynmediaimporter.media-browse"}',
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
    'ynmediaimporter_flickr_index' => 
    array (
      'page_id' => 46,
      'name' => 'ynmediaimporter_flickr_index',
      'displayname' => 'Social Media Importer - Flickr',
      'url' => NULL,
      'title' => 'Social Media Importer - Flickr',
      'description' => 'Social Media Importer - Flickr',
      'keywords' => 'Social Media Importer, Flickr',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => 'no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 803,
          'page_id' => 46,
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
              'content_id' => 804,
              'page_id' => 46,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 803,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 843,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.browse-menu',
                  'parent_content_id' => 804,
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
          'content_id' => 801,
          'page_id' => 46,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 806,
              'page_id' => 46,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 801,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 844,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.flickr-menu',
                  'parent_content_id' => 806,
                  'order' => 6,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 802,
              'page_id' => 46,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 801,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 845,
                  'page_id' => 46,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.media-browse',
                  'parent_content_id' => 802,
                  'order' => 8,
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
    'ynmediaimporter_index_index' => 
    array (
      'page_id' => 44,
      'name' => 'ynmediaimporter_index_index',
      'displayname' => 'Social Media Importer',
      'url' => NULL,
      'title' => 'Social Media Importer',
      'description' => 'Social Media Importer',
      'keywords' => 'Social Media Importer',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => 'no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 789,
          'page_id' => 44,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 790,
              'page_id' => 44,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 789,
              'order' => 6,
              'params' => '[]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 791,
                  'page_id' => 44,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.browse-menu',
                  'parent_content_id' => 790,
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
          'content_id' => 787,
          'page_id' => 44,
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
              'content_id' => 788,
              'page_id' => 44,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 787,
              'order' => 6,
              'params' => '[]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 792,
                  'page_id' => 44,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 788,
                  'order' => 6,
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
    'ynmediaimporter_instagram_index' => 
    array (
      'page_id' => 45,
      'name' => 'ynmediaimporter_instagram_index',
      'displayname' => 'Social Media Importer - Instagram',
      'url' => NULL,
      'title' => 'Social Media Importer - Instagram',
      'description' => 'Social Media Import - Instagram',
      'keywords' => 'Social Media Import,Instagram',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '["1","2","3","4","5"]',
      'provides' => 'no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 795,
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
              'content_id' => 796,
              'page_id' => 45,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 795,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 846,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.browse-menu',
                  'parent_content_id' => 796,
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
          'content_id' => 793,
          'page_id' => 45,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 798,
              'page_id' => 45,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 793,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 847,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.instagram-menu',
                  'parent_content_id' => 798,
                  'order' => 6,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 794,
              'page_id' => 45,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 793,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 848,
                  'page_id' => 45,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.media-browse',
                  'parent_content_id' => 794,
                  'order' => 8,
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
    'ynmediaimporter_picasa_index' => 
    array (
      'page_id' => 47,
      'name' => 'ynmediaimporter_picasa_index',
      'displayname' => 'Social Media Importer - Picasa',
      'url' => NULL,
      'title' => 'Social Media Importer - Picasa',
      'description' => 'Social Media Importer - Picasa',
      'keywords' => 'Social Media Importer - Picasa',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '["1","2","3","4","5"]',
      'provides' => 'no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 811,
          'page_id' => 47,
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
              'content_id' => 812,
              'page_id' => 47,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 811,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 849,
                  'page_id' => 47,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.browse-menu',
                  'parent_content_id' => 812,
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
          'content_id' => 809,
          'page_id' => 47,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 814,
              'page_id' => 47,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 809,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 850,
                  'page_id' => 47,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.picasa-menu',
                  'parent_content_id' => 814,
                  'order' => 6,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 810,
              'page_id' => 47,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 809,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 851,
                  'page_id' => 47,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.media-browse',
                  'parent_content_id' => 810,
                  'order' => 8,
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
  	/*
    'ynmediaimporter_yfrog_index' => 
    array (
      'page_id' => 48,
      'name' => 'ynmediaimporter_yfrog_index',
      'displayname' => 'Social Media Importer - YFrog',
      'url' => NULL,
      'title' => 'Social Media Importer - YFrog',
      'description' => 'Social Media Importer - YFrog',
      'keywords' => 'Social Media Importer, YFrog Importer',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '["1","2","3","4","5"]',
      'provides' => 'no-subject',
      'view_count' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 822,
          'page_id' => 48,
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
              'content_id' => 823,
              'page_id' => 48,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 822,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 837,
                  'page_id' => 48,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.browse-menu',
                  'parent_content_id' => 823,
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
          'content_id' => 820,
          'page_id' => 48,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 825,
              'page_id' => 48,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 820,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 852,
                  'page_id' => 48,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.yfrog-menu',
                  'parent_content_id' => 825,
                  'order' => 6,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 821,
              'page_id' => 48,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 820,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 839,
                  'page_id' => 48,
                  'type' => 'widget',
                  'name' => 'ynmediaimporter.media-browse',
                  'parent_content_id' => 821,
                  'order' => 8,
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
    ),*/
  ),
);?>