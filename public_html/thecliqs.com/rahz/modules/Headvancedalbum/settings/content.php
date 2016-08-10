<?php

return array(
    array(
        'title' => 'Navigation Tabs',
        'description' => 'Displays the Navigation tabs for Advanced Albums having links of Advanced Albums Photos, Albums, My Albums and Add Photos. This widget should be placed only at the top of Browse Photos',
        'category' => 'HE Advanced Albums',
        'type' => 'widget',
        'name' => 'headvancedalbum.navigation-tabs',
        'requirements' => array(
            'no-subject',
        ),
    ),

    array(
        'title' => 'Profile Albums',
        'description' => 'Displays a member\'s albums on their profile.',
        'category' => 'HE Advanced Albums',
        'type' => 'widget',
        'name' => 'headvancedalbum.profile-albums',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Featured Albums',
        'description' => 'Displays featured albums',
        'category' => 'HE Advanced Albums',
        'type' => 'widget',
        'name' => 'headvancedalbum.featured-albums',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Featured Albums',
        ),
    ),

    array(
        'title' => 'Featured Photos',
        'description' => 'Displays featured photos',
        'category' => 'HE Advanced Albums',
        'type' => 'widget',
        'name' => 'headvancedalbum.featured-photos',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Featured Photos',
        ),
    ),


    array(
        'title' => 'Popular Photos',
        'description' => 'Display a list of the most popular photos.',
        'category' => 'HE Advanced Albums',
        'type' => 'widget',
        'name' => 'headvancedalbum.popular-photos',
        'defaultParams' => array(
            'title' => 'Popular Photos',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'view' => 'Views',
                            'comment' => 'Comments',
                            'like' => 'Likes',
                        ),
                        'value' => 'comment',
                    )
                ),
                array(
                    'Text',
                    'photosCount',
                    array(
                        'label' => 'Photos Count',
                        'value' => 10,
                    )
                ),
            )
        ),
    ),

    array(
        'title' => 'Popular Albums',
        'description' => 'Display a list of the most popular albums.',
        'category' => 'HE Advanced Albums',
        'type' => 'widget',
        'name' => 'headvancedalbum.popular-albums',
        'defaultParams' => array(
            'title' => 'Popular Albums',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'view' => 'Views',
                            'comment' => 'Comments',
                            'like' => 'Likes',
                        ),
                        'value' => 'comment',
                    )
                ),
                array(
                    'Text',
                    'photosCount',
                    array(
                        'label' => 'Albums Count',
                        'value' => 10,
                    )
                ),
            )
        ),
    ),


  array(
    'title' => 'Friends\' Albums',
    'description' => 'Display a random list of friends\' albums.',
    'category' => 'HE Advanced Albums',
    'type' => 'widget',
    'name' => 'headvancedalbum.friends-albums',
    'defaultParams' => array(
      'title' => 'Friends\' Albums',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'itemCountPerPage',
          array(
            'label' => 'Count',
            'value' => 3,
          )
        ),
      )
    ),

  ),

  array(
    'title' => 'Friends\' Photos',
    'description' => 'Display a random list of friends\' photos.',
    'category' => 'HE Advanced Albums',
    'type' => 'widget',
    'name' => 'headvancedalbum.friends-photos',
    'defaultParams' => array(
      'title' => 'Friends\' Photos',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'itemCountPerPage',
          array(
            'label' => 'Count',
            'value' => 3,
          )
        ),
      )
    ),

  )

);