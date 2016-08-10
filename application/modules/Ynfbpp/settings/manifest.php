<?php
return array('package' => array(
        'type' => 'module',
        'name' => 'ynfbpp',
        'version' => '4.01p6',
        'path' => 'application/modules/Ynfbpp',
        'title' => 'YN - Profile Popup',
        'description' => 'Profile Popup',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.2',
            ),
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.2',
            ),
        ),
        'callback' => array('class' => 'Engine_Package_Installer_Module', ),
        'actions' => array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' => array(0 => 'application/modules/Ynfbpp', ),
        'files' => array(0 => 'application/languages/en/ynfbpp.csv', ),
    ), );
?>
