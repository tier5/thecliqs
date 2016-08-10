<?php
return array(
	array(
		'title' => 'User Credit - Browse Menu',
		'description' => 'Displays a menu in the user credit browse page.',
		'category' => 'User Credit',
		'type' => 'widget',
		'name' => 'yncredit.browse-menu',
		'requirements' => array(
				'no-subject',
		),
	),
    array(
        'title' => 'User Credit - Buy Credit',
        'description' => '',
        'category' => 'User Credit',
        'type' => 'widget',
        'name' => 'yncredit.buy-credit',
        'defaultParams' => array(
            'title' => 'Buy Credit',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'User Credit - Send Credit',
        'description' => '',
        'category' => 'User Credit',
        'type' => 'widget',
        'name' => 'yncredit.send-credit',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Send Credit',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'User Credit - My Statistics',
        'description' => '',
        'category' => 'User Credit',
        'type' => 'widget',
        'name' => 'yncredit.my-statistics',
        'defaultParams' => array(
            'title' => 'My Statistics',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'User Credit - Statistics',
        'description' => '',
        'category' => 'User Credit',
        'type' => 'widget',
        'name' => 'yncredit.statistics',
        'defaultParams' => array(
            'title' => 'Statistics',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'User Credit - Top Active Members',
        'description' => '',
        'category' => 'User Credit',
        'type' => 'widget',
        'name' => 'yncredit.top-active-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Top Active Members',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'User Credit - Top Credits Balance',
        'description' => '',
        'category' => 'User Credit',
        'type' => 'widget',
        'name' => 'yncredit.top-credits-balance',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Top Credits Balance',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    )
    
)
?>