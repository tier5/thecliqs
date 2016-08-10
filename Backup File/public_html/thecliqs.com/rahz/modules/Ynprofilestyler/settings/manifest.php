<?php return array(
	'package' => array(
		'type'        => 'module',
		'name'        => 'ynprofilestyler',
		'version'     => '4.01p4',
		'path'        => 'application/modules/Ynprofilestyler',
		'title'       => 'YN - Profile Styler',
		'description' => 'Profile Styler',
		'author'      => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
		'callback'    => array(
			'class' => 'Engine_Package_Installer_Module',
            'priority' => 4000
		),
		'dependencies' => array(            
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.7',
            ),
        ),
		'actions'     => array(
			'install',
            'enable',
            'disable'
		),
		'directories' => array(
			0 => 'application/modules/Ynprofilestyler',
		),
		'files'       => array(
			0 => 'application/languages/en/ynprofilestyler.csv',
		),
	),
	'routes'=>array(
        'ynprofilestyler_mystyle' => array(
            'route' => 'profile-styler/my-style',
            'defaults' => array(
                'module' => 'ynprofilestyler',
                'controller' => 'edit',
                'action' => 'my-style'
            )
        ),
    )
	
);
?>
