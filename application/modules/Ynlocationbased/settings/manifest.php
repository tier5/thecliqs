<?php return array(
    'package' =>
        array(
            'type' => 'module',
            'name' => 'ynlocationbased',
            'version' => '4.01',
            'path' => 'application/modules/Ynlocationbased',
            'title' => 'YN - Location-based System',
            'description' => 'This is location-bases system.',
            'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
            'callback' =>
                array (
                    'path' => 'application/modules/Ynlocationbased/settings/install.php',
                    'class' => 'Ynlocationbased_Installer',
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
                    0 => 'application/modules/Ynlocationbased',
                ),
            'files' =>
                array(
                    0 => 'application/languages/en/ynlocationbased.csv',
                ),
            'dependencies' =>
                array(
                    0 =>
                        array(
                            'type' => 'module',
                            'name' => 'core',
                            'minVersion' => '4.1.2',
                        ),
                ),
        ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'ynlocationbased_general' => array(
            'route' => 'location-based/:action/*',
            'defaults' => array(
                'module' => 'ynlocationbased',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(get-my-location)',
            ),
        ),
    ),
); ?>