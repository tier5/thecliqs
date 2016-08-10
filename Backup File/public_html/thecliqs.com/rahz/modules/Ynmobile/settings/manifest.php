<?php
return array(
		'package' => array(
			'type' => 'module',
			'name' => 'ynmobile',
			'version' => '4.08p2',
			'path' => 'application/modules/Ynmobile',
			'title' => 'YouNet Mobile SocialEngine',
			'description' => '',
			'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company/MisterWizard</a>',
			'callback' => array('class' => 'Engine_Package_Installer_Module', ),
			'actions' => array(
					0 => 'install',
					1 => 'upgrade',
					2 => 'refresh',
					3 => 'enable',
					4 => 'disable',
			),
			'callback' => array(
					'path' => 'application/modules/Ynmobile/settings/install.php',
					'class' => 'Ynmobile_Installer',
					'priority' => 3000,
			),
			'directories' => array(
				 0 => 'application/modules/Ynmobile',
				 1 => 'wideimage',
				 // 2 => 'commetchat',
			 ),
			'files' => array(
			     0 => 'application/languages/en/ynmobile.csv',
			     1 => 'cometchat/cometchat_api_mysqli.php',
			     2 => 'cometchat/cometchat_api.php', 
            ),
		),
		// Hooks ---------------------------------------------------------------------
		'hooks' => array(
						array(
							'event' => 'onActivityNotificationCreateAfter',
							'resource' => 'Ynmobile_Plugin_Core',
						),
					),
		'items' => array(
				'ynmobile_map'
		),
);

?>
