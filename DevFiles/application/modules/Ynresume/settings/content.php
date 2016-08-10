<?php
return array(
	array(
        'title' => 'Resume Main Menu',
        'description' => 'Displays main menu.',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.main-menu',
        'defaultParams' => array(
        	'title' => 'Resume Main Menu',
        ),
	),
	
    array(
        'title' => 'Resume Recommendations Menu',
        'description' => 'Displays recommendations menu.',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.recommendation-menu',
        'defaultParams' => array(
        	'title' => 'Resume Recommendations Menu',
        ),
    ),
	
	array(
        'title' => 'Job You May Interested In',
        'description' => 'Displays jobs related to resume industry of viewer',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.interested-jobs',
        'defaultParams' => array(
        	'title' => 'Job You May Interested In',
        	'itemCountPerPage' => 5,
        ),
        'isPaginated' => true,
    ),
	
	array(
        'title' => 'My Resume Cover',
        'description' => 'Displays Owner Resume Cover on My Resume page',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.my-resume-cover',
        'defaultParams' => array(
        	'title' => 'My Resume Cover',
        ),
        'requirements' => array(
	      'subject' => 'ynresume_resume',
	    ),
    ),
    
	array(
        'title' => 'Most Viewed Resumes',
        'description' => 'Displays Most Viewed Resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.most-viewed-resume',
        'defaultParams' => array(
        	'title' => 'Most Viewed Resumes',
        	'itemCountPerPage' => 16,
        ),
        'isPaginated' => true,
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
        'title' => 'Manage Sections of Resume',
        'description' => 'Displays Manage Sections on My Resume page',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.manage-sections',
        'defaultParams' => array(
        	'title' => 'Manage Sections of Resume',
        ),
        'requirements' => array(
          'subject' => 'ynresume_resume',
        ),
    ),
    
    array(
        'title' => 'View Resume Cover',
        'description' => 'Displays Resume Cover on Resume Detail page',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.view-resume-cover',
        'defaultParams' => array(
        	'title' => 'View Resume Cover',
        ),
        'requirements' => array(
          'subject' => 'ynresume_resume',
        ),
    ),
    
    array(
        'title' => 'View Sections of Resume',
        'description' => 'Displays Sections on Resume Detail page',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.view-sections',
        'defaultParams' => array(
        	'title' => 'View Sections of Resume',
        ),
        'requirements' => array(
          'subject' => 'ynresume_resume',
        ),
    ),
    
    array(
        'title' => 'Endorse Suggestion of Resume',
        'description' => 'Displays Endorse Suggestion on Resume Detail page',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.endorse-suggestion',
        'defaultParams' => array(
        	'title' => 'Endorse Suggestion of Resume',
        ),
        'requirements' => array(
          'subject' => 'ynresume_resume',
        ),
    ),
    
    array(
        'title' => 'Resume Search',
        'description' => 'Displays form for searching resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.resume-search',
        'defaultParams' => array(
            'title' => 'Resume Search',
        ),
    ),
    
    array(
        'title' => 'Featured Resumes',
        'description' => 'Displays Featured Resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.featured-resume',
        'defaultParams' => array(
        	'title' => 'Featured Resumes',
        	'itemCountPerPage' => 8,
        ),
        'isPaginated' => true,
    ),
    
    array(
        'title' => 'Newest Resumes',
        'description' => 'Displays Newest Resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.newest-resume',
        'defaultParams' => array(
        	'title' => 'Newest Resumes',
        	'itemCountPerPage' => 16,
        ),
        'isPaginated' => true,
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
        'title' => 'Most Endorsed Resumes',
        'description' => 'Displays Most Endorsed Resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.most-endorsed-resume',
        'defaultParams' => array(
        	'title' => 'Most Endorsed Resumes',
        	'itemCountPerPage' => 16,
        ),
        'isPaginated' => true,
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
        'title' => 'Most Favorite Resumes',
        'description' => 'Displays Most Favorite Resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.most-favorite-resume',
        'defaultParams' => array(
        	'title' => 'Most Favorite Resumes',
        	'itemCountPerPage' => 16,
        ),
        'isPaginated' => true,
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
        'title' => 'Who Viewed Your Resume',
        'description' => 'Displays Who Viewed Your Resume',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.who-viewed-your-resume',
        'defaultParams' => array(
        	'title' => 'Who Viewed Your Resume',
        ),
    ),
    
    array(
        'title' => 'Browse Resumes',
        'description' => 'Displays Browse Resumes',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.browse-resume',
        'defaultParams' => array(
        	'title' => 'Browse Resumes',
        ),
    ),
    
    array(
        'title' => 'Profile Skills',
        'description' => 'Displays Profile Skills',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.skill',
        'defaultParams' => array(
        	'title' => 'Profile Skills',
        ),
    ),
    
    array(
        'title' => 'Resumes Listing',
        'description' => 'Resumes Listing',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.resume-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Resumes Listing',
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
        'title' => 'Resume Profile Suggestions',
        'description' => 'Displays Resume Suggestions on Resume Detail page',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.profile-suggestions',
        'defaultParams' => array(
            'title' => 'Suggestions',
            'num_of_resumes' => 6
        ),
        'requirements' => array(
            'subject' => 'ynresume_resume',
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
                    'num_of_resumes',
                    array(
                        'label' => 'Number of resumes will show?',
                        'value' => 6,
                    ),
                ),
            ),
        ),
    ),
    
	array(
        'title' => 'Resume Received Recommendations',
        'description' => 'Displays Received Recommendations',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.recommendation-received',
        'defaultParams' => array(
            'title' => 'Resume Received Recommendations',
        ),
    ),
    
    array(
        'title' => 'Resume Given Recommendations',
        'description' => 'Displays Given Recommendations',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.recommendation-given',
        'defaultParams' => array(
            'title' => 'Resume Given Recommendations',
        ),
    ),
    
    array(
        'title' => 'Resume Ask for Recommendations',
        'description' => 'Displays form to ask for Recommendations',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.recommendation-ask',
        'defaultParams' => array(
            'title' => 'Resume Ask for Recommendations',
        ),
    ),
    
    array(
        'title' => 'Resume Give Recommendations',
        'description' => 'Displays form to give Recommendations',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.recommendation-give',
        'defaultParams' => array(
            'title' => 'Resume Give Recommendations',
        ),
    ),
    
    array(
        'title' => 'Resume Request Recommendations',
        'description' => 'Displays Request Recommendations',
        'category' => 'Resume',
        'type' => 'widget',
        'name' => 'ynresume.recommendation-request',
        'defaultParams' => array(
            'title' => 'Resume Request Recommendations',
        ),
    ),
);
