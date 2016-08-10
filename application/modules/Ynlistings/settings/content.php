<?php
return array(
    array(
        'title' => 'YN Listings Main Navigation Menu',
        'description' => 'Displays the main menu of ynlistings module.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.main-menu',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    
	array(
	    'title' => 'Profile Listing Info',
	    'description' => 'Displays a listing\'s info on its profile.',
	    'category' => 'YN Listings',
	    'type' => 'widget',
	    'name' => 'ynlistings.listing-info',
	    'defaultParams' => array(
	      'title' => 'Info',
	    ),
	    'requirements' => array(
	      'subject' => 'ynlistings_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing About Us',
	    'description' => 'Displays a listing\'s about us on its profile.',
	    'category' => 'YN Listings',
	    'type' => 'widget',
	    'name' => 'ynlistings.listing-about',
	    'defaultParams' => array(
	      'title' => 'About Us',
	    ),
	    'requirements' => array(
	      'subject' => 'ynlistings_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing Location',
	    'description' => 'Displays a listing\'s location on its profile.',
	    'category' => 'YN Listings',
	    'type' => 'widget',
	    'name' => 'ynlistings.listing-location',
	    'defaultParams' => array(
	      'title' => 'Location',
	    ),
	    'requirements' => array(
	      'subject' => 'ynlistings_listing',
	    ),
    ),
	
	 array(
	    'title' => 'Profile Listing Albums',
	    'description' => 'Displays a listing\'s albums on its profile.',
	    'category' => 'YN Listings',
	    'type' => 'widget',
	    'name' => 'ynlistings.listing-albums',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Albums',
	      'titleCount' => true,
	    ),
	    'requirements' => array(
	      'subject' => 'ynlistings_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing Discussions',
	    'description' => 'Displays a listing\'s discussions on its profile.',
	    'category' => 'YN Listings',
	    'type' => 'widget',
	    'name' => 'ynlistings.listing-discussions',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Discussions',
	      'titleCount'=>true,
	    ),
	    'requirements' => array(
	      'subject' => 'ynlistings_listing',
	    ),
    ),
	
	array(
	    'title' => 'Profile Listing Videos',
	    'description' => 'Displays a list of videos on the listing.',
	    'category' => 'YN Listings',
	    'type' => 'widget',
	    'name' => 'ynlistings.listing-videos',
	    'isPaginated' => true,
	    'defaultParams' => array(
	      'title' => 'Videos',
	      'titleCount' => true,
	    ),
	    'requirements' => array(
	      'subject' => 'ynlistings_listing',
	    ),
   ),
	
    array(
        'title' => 'Profile Related Listings',
        'description' => 'Displays a list of other listings that has the same category to the current listing.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.related-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Related Listings'
        ),
        'requirements' => array(
            'subject' => 'ynlistings_listing',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 6,
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'title' => 'Profile Listing Reviews',
        'description' => 'Displays a list of reviews on the listing.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.listing-reviews',
        'isPaginated' => true,
        'defaultParams' => array(
          'title' => 'Reviews',
        ),
        'requirements' => array(
          'subject' => 'ynlistings_listing',
        ),
    ),
   
    array(
        'title' => 'Listing Browse Search',
        'description' => 'Displays a search form in the listing browse page.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.browse-search',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Search Listings',
        ),
    ),
    
    array(
        'title' => 'List Categories',
        'description' => 'Displays a list of categories.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.list-categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),

    array(
        'title' => 'Most Liked Listings',
        'description' => 'Displays a list of most liked listings.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.most-liked-listings',
        'defaultParams' => array(
            'title' => 'Most Liked Listings',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),

    array(
        'title' => 'Most Discussion Listings',
        'description' => 'Displays a list of most discussion listings.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.most-discussion-listings',
        'defaultParams' => array(
            'title' => 'Most Discussion Listings',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),

    array(
        'title' => 'Most Reviewed Listings',
        'description' => 'Displays a list of most reviewed listings.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.most-reviewed-listings',
        'defaultParams' => array(
            'title' => 'Most Reviewed Listings',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'title' => 'Recently Viewed',
        'description' => 'Displays a most recently viewed listings.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.recently-viewed',
        'defaultParams' => array(
            'title' => 'Recently Viewed',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'title' => 'Highlight Listing',
        'description' => 'Displays highlight listing.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.highlight-listing',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Highlight Listings'
        ),
    ),
    
    array(
        'title' => 'Featured Listings',
        'description' => 'Displays featured listings on listings home page.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.featured-listings',
        'defaultParams' => array(
          'title' => 'Featured Listings',
        ),
        'requirements' => array(
            'no-subject',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 6,
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'title' => 'Browse Categories',
        'description' => 'Displays categories level 1 and child.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.browse-category',
        'defaultParams' => array(
          'title' => 'Browse Categories',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    
    array(
        'title' => 'Most Listings',
        'description' => 'Displays a most listings in listing home page.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.list-most-items',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Listings',
        ),
        'requirements' => array(
            'no-subject',
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
                    'tab_enabled',
                    array(
                        'label' => 'Which tabs are enabled?'
                    )
                ),
                array(
                    'Radio',
                    'tab_recent',
                    array(
                        'label' => 'Recent Listings tab.',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'tab_popular',
                    array(
                        'label' => 'Most Viewed Listings tab.',
                        'multiOptions' => array(
                            1 => 'Yes.',
                            0 => 'No.',
                        ),
                        'value' => 1,
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
                    'mode_pin',
                    array(
                        'label' => 'Pin view.',
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
                            'pin' => 'Pin view.',
                            'map' => 'Map view.',
                        ),
                        'value' => 'list',
                    )
                ),
                array(
                    'Integer',
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 6,
                    ),
                ),
                
            )
        ),
    ),
    
    array(
        'title' => 'Browse Listings',
        'description' => 'Displays listings in browse page.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.browse-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Browse Listings',
        ),
        'requirements' => array(
            'no-subject',
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
                    'mode_pin',
                    array(
                        'label' => 'Pin view.',
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
                            'pin' => 'Pin view.',
                            'map' => 'Map view.',
                        ),
                        'value' => 'list',
                    )
                ),
                
            )
        ),
    ),
    
    array(
        'title' => 'Listing Tags',
        'description' => 'Displays listings tags on listing home page.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.listings-tags',
          'defaultParams' => array(
            'title' => 'Tags',
        ),
    ),
    
    array(
        'title' => 'Listings You May Like',
        'description' => 'Displays a list of listings user may like.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.listings-you-may-like',
        'defaultParams' => array(
            'title' => 'Listings You May Like',
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
                    'num_of_listings',
                    array(
                        'label' => 'Number of listings will show?',
                        'value' => 3,
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'title' => 'User Profile Listings',
        'description' => 'Displays a member\'s listings on their profile.',
        'category' => 'YN Listings',
        'type' => 'widget',
        'name' => 'ynlistings.profile-listings',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Classifieds',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
  ),
);