<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynchat',
    'version' => '4.01p1',
    'path' => 'application/modules/Ynchat',
    'title' => 'YN - Chat',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
    'callback' =>
    array (
      'path' => 'application/modules/Ynchat/settings/install.php',
      'class' => 'Ynchat_Installer',
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
      0 => 'application/modules/Ynchat',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynchat.csv',
    ),
  ),
  
  'items' => array(
    'ynchat_banword',
    'ynchat_file',
    'ynchat_message'
  ),
  
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Ynchat_Plugin_Core',
    ),
    array(
      'event' => 'onRenderLayoutMobileDefault',
      'resource' => 'Ynchat_Plugin_Core',
    ),
  ),
  
  'routes' => array(
    'ynchat_index' => array(
        'route' => 'ynchat/:action/*',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'index',
            'action' => 'index',
        ),
    ),
    'ynchat_initLangAndConfig' => array(
        'route' => 'ynchat/initLangAndConfig',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'init-lang-and-config',
        ),
    ),
    'ynchat_updateAgent' => array(
        'route' => 'ynchat/updateAgent',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'update-agent',
        ),
    ),
    'ynchat_getAdvancedSetting' => array(
        'route' => 'ynchat/getAdvancedSetting',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'get-advanced-setting',
        ),
    ),
    'ynchat_updateUserBoxSetting' => array(
        'route' => 'ynchat/updateUserBoxSetting',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'update-user-box-setting',
        ),
    ),
    'ynchat_updateStatusGoOnline' => array(
        'route' => 'ynchat/updateStatusGoOnline',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'update-status-go-online',
        ),
    ),
    'ynchat_updateStatusPlaySound' => array(
        'route' => 'ynchat/updateStatusPlaySound',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'update-status-play-sound',
        ),
    ),
    'ynchat_getUnreadBox' => array(
        'route' => 'ynchat/getUnreadBox',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'get-unread-box',
        ),
    ),
    'ynchat_searchFriend' => array(
        'route' => 'ynchat/searchFriend',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'search-friend',
        ),
    ),
    'ynchat_threadInfo' => array(
        'route' => 'ynchat/threadInfo',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'thread-info',
        ),
    ),
    'ynchat_getOldConversation' => array(
        'route' => 'ynchat/getOldConversation',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'get-old-conversation',
        ),
    ),
    'ynchat_upload' => array(
        'route' => 'ynchat/upload',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'upload',
        ),
    ),
    'ynchat_download' => array(
        'route' => 'ynchat/download/*',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'download',
            'action' => 'index',
        ),
    ),
    'ynchat_updateStatusMessage' => array(
        'route' => 'ynchat/updateStatusMessage/*',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'update-status-message',
        ),
    ),
    'ynchat_sendMessageByAjax' => array(
        'route' => 'ynchat/sendMessageByAjax/*',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'send-message-by-ajax',
        ),
    ),
	'ynchat_saveAdvancedSetting' => array(
        'route' => 'ynchat/saveAdvancedSetting/*',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'save-advanced-setting',
        ),
    ),
    'ynchat_removeOldMessage' => array(
        'route' => 'ynchat/removeOldMessage/*',
        'defaults' => array(
            'module' => 'ynchat',
            'controller' => 'ajax',
            'action' => 'remove-old-message',
        ),
    ),
  )
); ?>
