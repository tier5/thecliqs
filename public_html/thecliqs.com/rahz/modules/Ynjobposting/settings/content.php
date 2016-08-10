<?php
return array(
	array(
        'title' => 'Job Posting Browse Menu',
        'description' => 'Displays a menu in the browse page.',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
	),
	array(
        'title' => 'Job Posting Job Listing',
        'description' => 'Displays Job Listing',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.jobs-listing',
		'defaultParams' => array(
      		'title' => '',
    	),
    	'isPaginated' => true,
        'requirements' => array(
        ),
        'adminForm' => array(
				'elements' => array(
					array(
						'Text',
						'title',
						array(
							'label' => 'Title'
						)
					),	
					array(
						'Heading',
						'mode_enabled',
						array(
							'label' => 'Which view modes are enabled?'
						)
					),
					array(
							'Radio',
							'mode_list',
							array(
								'label' => 'List view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_grid',
							array(
								'label' => 'Grid view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_map',
							array(
								'label' => 'Map view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'view_mode',
							array(
									'label' => 'Which view mode is default?',
									'multiOptions' => array(
										'list' => 'List view.',
										'grid' => 'Grid view.',
										'map' => 'Map view.',
									),
									'value' => 'list',
							)
					),
					
				)
			)
	),
	array(
        'title' => 'Job Posting Job Search',
        'description' => 'Displays form for searching jobs',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-search',
		'defaultParams' => array(
      		'title' => '',
    	),
        'requirements' => array(
        ),
	),
	array(
        'title' => 'Job Posting Job Alert',
        'description' => 'Displays a job alert form.',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-alert',
        'defaultParams' => array(
      		'title' => 'Job Alert',
    	),
        'requirements' => array(
            'no-subject',
        ),
	),
	array(
        'title' => 'Job Posting Profile Job Photo',
        'description' => 'Displays a job company\'s photo',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-profile-photo',
        'requirements' => array(
			'subject' => 'ynjobposting_job',
        ),
	),
	array(
        'title' => 'Job Posting Profile Job Option',
        'description' => 'Displays a job options',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-profile-option',
        'requirements' => array(
			'subject' => 'ynjobposting_job',
        ),
	),
	array(
        'title' => 'Job Posting Profile Job Info',
        'description' => 'Displays a job info',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-profile-info',
        'defaultParams' => array(
      		'title' => 'Detail',
    	),
        'requirements' => array(
			'subject' => 'ynjobposting_job',
        ),
	),
	array(
        'title' => 'Job Posting Related Jobs',
        'description' => 'Displays list of related jobs',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-related',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Related Jobs',
    	),
        'requirements' => array(
			'subject' => 'ynjobposting_job',
        ),
	),
	array(
        'title' => 'Job Posting Jobs From This Employer',
        'description' => 'Displays list of jobs from specific employer',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-company',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'More Jobs From This Company',
    	),
        'requirements' => array(
			'subject' => 'ynjobposting_job',
        ),
	),
	array(
		'title' => 'Job Posting Company Profile Cover',
		'description' => 'Displays a company\'s cover and information on it\'s profile.',
		'category' => 'Job Posting',
		'type' => 'widget',
		'name' => 'ynjobposting.company-profile-cover',
		'defaultParams' => array(
      		'title' => '',
    	),
		'requirements' => array(
				'subject' => 'ynjobposting_company',
		),
	),
	array(
		'title' => 'Job Posting Company Profile Description',
		'description' => 'Displays a company\'s description on it\'s profile.',
		'category' => 'Job Posting',
		'type' => 'widget',
		'name' => 'ynjobposting.company-profile-description',
		'defaultParams' => array(
      		'title' => '',
    	),
		'requirements' => array(
				'subject' => 'ynjobposting_company',
		),
	),
	array(
		'title' => 'Job Posting Company Profile Info',
		'description' => 'Displays a company\'s infomation on it\'s profile.',
		'category' => 'Job Posting',
		'type' => 'widget',
		'name' => 'ynjobposting.company-profile-info',
		'defaultParams' => array(
      		'title' => 'General Information',
    	),
		'requirements' => array(
				'subject' => 'ynjobposting_company',
		),
	),
	array(
		'title' => 'Job Posting Company Profile Jobs',
		'description' => 'Displays a company\'s jobs on it\'s profile.',
		'category' => 'Job Posting',
		'type' => 'widget',
		'name' => 'ynjobposting.company-profile-jobs',
		'defaultParams' => array(
      		'title' => 'Jobs',
    	),
		'requirements' => array(
				'subject' => 'ynjobposting_company',
		),
	),
	array(
        'title' => 'Job Posting Sponsored Companies',
        'description' => 'Displays sponsored companies',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.sponsored-companies',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Sponsored Companies',
    	),
        'requirements' => array(
        ),
	),
	array(
        'title' => 'Job Posting Hot Companies',
        'description' => 'Displays Hot companies',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.hot-companies',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Hot Companies',
    	),
        'requirements' => array(
        ),
	),
	array(
        'title' => 'Job Posting Following Companies',
        'description' => 'Displays following companies',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.following-companies',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Following Companies',
    	),
        'requirements' => array(
        ),
	),
	array(
        'title' => 'Job Posting Applied Jobs',
        'description' => 'Displays applied jobs',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.applied-jobs',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Applied Jobs',
    	),
        'requirements' => array(
    		'subject' => 'user',
        ),
	),
	array(
        'title' => 'Job Posting Company Listing',
        'description' => 'Displays list of companies',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.company-listing',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => '',
    	),
        'requirements' => array(
        ),
        'adminForm' => array(
				'elements' => array(
					array(
						'Text',
						'title',
						array(
							'label' => 'Title'
						)
					),	
					array(
						'Heading',
						'mode_enabled',
						array(
							'label' => 'Which view modes are enabled?'
						)
					),
					array(
							'Radio',
							'mode_list',
							array(
								'label' => 'List view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_grid',
							array(
								'label' => 'Grid view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_map',
							array(
								'label' => 'Map view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'view_mode',
							array(
									'label' => 'Which view mode is default?',
									'multiOptions' => array(
										'list' => 'List view.',
										'grid' => 'Grid view.',
										'map' => 'Map view.',
									),
									'value' => 'list',
							)
					),
					
				)
			)
	),
	array(
        'title' => 'Job Posting Newest Jobs',
        'description' => 'Displays list of newest job',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.newest-job',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Newest Jobs',
      		'itemCountPerPage' => 5,
    	),
        'requirements' => array(
        ),
        'adminForm' => array(
				'elements' => array(
					array(
						'Text',
						'title',
						array(
							'label' => 'Title'
						)
					),	
					array(
						'Heading',
						'mode_enabled',
						array(
							'label' => 'Which view modes are enabled?'
						)
					),
					array(
							'Radio',
							'mode_list',
							array(
								'label' => 'List view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_grid',
							array(
								'label' => 'Grid view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_map',
							array(
								'label' => 'Map view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'view_mode',
							array(
									'label' => 'Which view mode is default?',
									'multiOptions' => array(
										'list' => 'List view.',
										'grid' => 'Grid view.',
										'map' => 'Map view.',
									),
									'value' => 'list',
							)
					),
					
				)
			)
	),
	array(
        'title' => 'Job Posting Most Viewed Jobs',
        'description' => 'Displays list of most viewed jobs',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.most-viewed-job',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Most Viewed Jobs',
    	),
        'requirements' => array(
        ),
        'adminForm' => array(
				'elements' => array(
					array(
						'Text',
						'title',
						array(
							'label' => 'Title'
						)
					),	
					array(
						'Heading',
						'mode_enabled',
						array(
							'label' => 'Which view modes are enabled?'
						)
					),
					array(
							'Radio',
							'mode_list',
							array(
								'label' => 'List view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_grid',
							array(
								'label' => 'Grid view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_map',
							array(
								'label' => 'Map view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'view_mode',
							array(
									'label' => 'Which view mode is default?',
									'multiOptions' => array(
										'list' => 'List view.',
										'grid' => 'Grid view.',
										'map' => 'Map view.',
									),
									'value' => 'list',
							)
					),
					
				)
			)
	),
	array(
        'title' => 'Job Posting Hot Jobs',
        'description' => 'Displays list of most applied jobs',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.hot-job',
		'isPaginated' => true,
		'defaultParams' => array(
      		'title' => 'Hot Jobs',
    	),
        'requirements' => array(
        ),
        'adminForm' => array(
				'elements' => array(
					array(
						'Text',
						'title',
						array(
							'label' => 'Title'
						)
					),	
					array(
						'Heading',
						'mode_enabled',
						array(
							'label' => 'Which view modes are enabled?'
						)
					),
					array(
							'Radio',
							'mode_list',
							array(
								'label' => 'List view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_grid',
							array(
								'label' => 'Grid view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'mode_map',
							array(
								'label' => 'Map view.',
								'multiOptions' => array(
									1 => 'Yes.',
									0 => 'No.',
								),
								'value' => 1,
							)
					),
					array(
							'Radio',
							'view_mode',
							array(
									'label' => 'Which view mode is default?',
									'multiOptions' => array(
										'list' => 'List view.',
										'grid' => 'Grid view.',
										'map' => 'Map view.',
									),
									'value' => 'list',
							)
					),
					
				)
			)
	),
	array(
        'title' => 'Job Posting Company Search',
        'description' => 'Displays form for searching companies',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.company-search',
		'defaultParams' => array(
      		'title' => '',
    	),
        'requirements' => array(
        ),
	),
	
    array(
        'title' => 'Job Posting Featured Jobs',
        'description' => 'Displays featured jobs',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.featured-jobs',
        'defaultParams' => array(
            'title' => 'Featured Jobs',
        ),
        'requirements' => array(
        ),
    ),
    
    array(
        'title' => 'Job Posting List Industries',
        'description' => 'Displays a list of industries.',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.list-industries',
        'defaultParams' => array(
            'title' => 'Industries',
        ),
    ),
    
    array(
        'title' => 'Job Posting Interesting Jobs',
        'description' => 'Displays a list of interesting joba.',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-you-may-be-interested',
        'defaultParams' => array(
            'title' => 'Interesting Jobs',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title'
                    )
                ),
                array(
                    'Integer',
                    'num_of_jobs',
                    array(
                        'label' => 'Number of jobs will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'title' => 'Job Posting Jobs Tags',
        'description' => 'Displays jobs tags.',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.jobs-tags',
          'defaultParams' => array(
            'title' => 'Tags',
        ),
    ),
    
    array(
        'title' => 'Job Posting Profile Add This',
        'description' => 'Displays Add This Job Posting.',
        'category' => 'Job Posting',
        'type' => 'widget',
        'name' => 'ynjobposting.job-profile-addthis',
        'defaultParams' => array('title' => 'Add this', ),
    ),
);