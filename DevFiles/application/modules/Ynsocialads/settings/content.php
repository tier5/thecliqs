<?php
return array(
	array(
		'title' => 'Social Ad Block',
		'description' => 'Displays ad block',
		'category' => 'YN Social Ads',
		'type' => 'widget',
		'name' => 'ynsocialads.ads-content',
		'isPaginated' => true,
		'defaultParams' => array('titleCount' => true, ),
		'requirements' => array('subject' => 'user', ),
	),
	array(
		'title' => 'YN Social Ads Main Navigation Menu',
		'description' => 'Displays the main menu of ynsocialads module.',
		'category' => 'YN Social Ads',
		'type' => 'widget',
		'name' => 'ynsocialads.main-menu',
		'isPaginated' => true,
		'defaultParams' => array(
			'title' => 'YN Social Ads',
			'titleCount' => true,
		),
		'requirements' => array('subject' => 'user', ),
	),
);
