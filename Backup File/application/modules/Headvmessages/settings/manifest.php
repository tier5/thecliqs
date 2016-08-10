<?php return array(
  'package' =>
    array(
      'type' => 'module',
      'name' => 'headvmessages',
      'version' => '4.8.9',
      'path' => 'application/modules/Headvmessages',
      'title' => 'Advanced Messages',
      'description' => 'Advanced Messages Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC/MisterWizard</a>',
      'meta' => array(
        'title' => 'Advanced Messages',
        'description' => 'Advanced Messages Plugin from Hire-Express LLC',
        'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC/MisterWizard</a>',
      ),
      'dependencies' => array(
        array(
          'type' => 'module',
          'name' => 'core',
          'minVersion' => '4.1.8',
        ),
        array(
          'type' => 'module',
          'name' => 'messages',
          'minVersion' => '4.8.1',
        ),
        array(
          'type' => 'module',
          'name' => 'hecore',
          'minVersion' => '4.2.0p1',
        )
      ),
      'callback' => array(
        'path' => 'application/modules/Headvmessages/settings/install.php',
        'class' => 'Headvmessages_Installer',
      ),
      'actions' =>
        array(
          0 => 'install',
          1 => 'upgrade',
          2 => 'refresh',
          3 => 'enable',
          4 => 'disable',
        ),
      'directories' =>
        array(
          0 => 'application/modules/Headvmessages',
        ),
      'files' =>
        array(
          0 => 'application/languages/en/headvmessages.csv',
        ),
    ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Headvmessages_Plugin_Core'
    )
  )
); ?>
