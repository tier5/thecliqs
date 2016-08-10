<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */


// get available category for widgets contain video
$categories = Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo') -> getCategories();
unset($categories[0]);
$categoryMultiOptions = array();
foreach ($categories as $item)
{
    $categoryMultiOptions[$item['category_id']] = str_repeat("-- ", $item['level'] - 1) . $item->getTitle();
}

return array(
    array(
        'title' => 'YN - Ultimate Video - Playlist Create Link',
        'description' => 'Displays a button to create new playlist.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.playlist-create-link',
    ),
    array(
        'title' => 'YN - Ultimate Video - List Categories',
        'description' => 'Displays a list of categories.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Tags',
        'description' => 'Displays a list of video tags.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-tags',
        'defaultParams' => array(
            'title' => 'Tags',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfTags',
                    array(
                        'label' => 'Number of tags',
                        'value' => '20',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Top Members',
        'description' => 'Displays a list top members having most videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-top-members',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfMembers',
                    array(
                        'label' => 'Number of members',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'defaultParams' => array(
            'title' => 'Top Members',
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Most Liked Videos',
        'description' => 'Displays a list of most liked videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-liked-videos',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'defaultParams' => array(
            'title' => 'Most Liked',
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Featured Videos',
        'description' => 'Displays a list by slideshow of featured videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-featured-videos',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Radio',
                    'verticalThumbnails',
                    array(
                        'label' => 'Show vertical thumbnails',
                        'value' => '0',
                        'multiOptions' => array(
                            '0' => 'No',
                            '1' => 'Yes',
                        ),
                    )
                ),
            )
        ),
        'defaultParams' => array(
            'title' => 'Featured Videos',
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Profile Videos',
        'description' => 'Displays a member\'s videos on their profile.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.profile-videos',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Ultimate Videos',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Profile Favorite Videos',
        'description' => 'Displays a member\'s favorite videos on their profile.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.profile-favorite-videos',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Favorite Videos',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Profile Video Playlists',
        'description' => 'Displays a member\'s video playlists on their profile.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.profile-video-playlists',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Video Playlists',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of playlists',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_grid',
                    array(
                        'label' => 'Grid view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Recent Videos',
        'description' => 'Displays a list of recently uploaded videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-recent-videos',
        'defaultParams' => array(
            'title' => 'Latest Videos',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Radio',
                    'recentType',
                    array(
                        'label' => 'Recent Type',
                        'multiOptions' => array(
                            'creation' => 'Creation Date',
                            'modified' => 'Modified Date',
                        ),
                        'value' => 'creation',
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - List Videos By Category',
        'description' => 'Displays a list of videos belong to a category.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-category-videos',
        'defaultParams' => array(
            'title' => 'Videos',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'isPaginated' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categoryMultiOptions,
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Recommended Videos',
        'description' => 'Displays recommended videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-recommended-videos',
        'requirements' => array(
            'viewer',
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
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Popular Videos',
        'description' => 'Displays a list of most viewed videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-popular-videos',
        'defaultParams' => array(
            'title' => 'Popular Videos',
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
                            'rating' => 'Rating',
                            'view' => 'Views',
                            'comment' => 'Comments',
                        ),
                        'value' => 'view',
                    )
                ),
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - People Also Liked',
        'description' => 'Displays a list of other videos that the people who liked this video also liked.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.show-also-liked',
        'defaultParams' => array(
            'title' => 'People Also Liked',
        ),
        'requirements' => array(
            'subject' => 'ynultimatevideo_video',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Other Videos From Member',
        'description' => 'Displays a list of other videos that the member that uploaded this video uploaded.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.show-same-poster',
        'defaultParams' => array(
            'title' => 'From the same Member',
        ),
        'requirements' => array(
            'subject' => array('ynultimatevideo_video','user')
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Similar Videos',
        'description' => 'Displays a list of other videos that are similar to the current video, based on tags.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.show-same-tags',
        'defaultParams' => array(
            'title' => 'Similar Videos',
        ),
        'requirements' => array(
            'subject' => 'ynultimatevideo_video',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Related Videos',
        'description' => 'Displays a list of other videos that has the same category to the current video',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.show-same-categories',
        'defaultParams' => array(
            'title' => 'Related Videos',
        ),
        'requirements' => array(
            'subject' => 'ynultimatevideo_video',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Video Browse Search',
        'description' => 'Displays a search form in the video browse page.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.browse-search',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Videos Manage Menu',
        'description' => 'Displays shortcuts to my video pages.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.manage-menu',
        'defaultParams' => array(
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Top Manage Menu',
        'description' => 'Displays shortcuts to my video pages.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.manage-menu-top',
        'defaultParams' => array(
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - My History',
        'description' => 'Displays my view history.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-history',
        'defaultParams' => array(
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of item per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Video Browse Menu',
        'description' => 'Displays a menu in the video browse page.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - List My Favorite Videos',
        'description' => 'Displays a list of current user\'s favorite videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-my-favorite-videos',
        'requirements' => array(
            'viewer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - List My Videos',
        'description' => 'Displays a list current user\'s videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-manage-videos',
        'requirements' => array(
            'viewer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - List My Playlists',
        'description' => 'Displays a list current user\'s playlists.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-my-playlists',
        'requirements' => array(
            'viewer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of playlists per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - List My Watch Later Videos',
        'description' => 'Displays a list current user\'s watch later videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-my-watch-later-videos',
        'requirements' => array(
            'viewer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - Watch Again',
        'description' => 'Displays a list of recently watched videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-watch-again-videos',
        'requirements' => array(
            'viewer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - List All Videos',
        'description' => 'Displays a list of all videos.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-videos',
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
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'YN - Ultimate Video - List All Playlists',
        'description' => 'Displays playlist listings in playlist browse page.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-playlists',
        'defaultParams' => array(
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
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of playlists per page',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_grid',
                    array(
                        'label' => 'Grid view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - List Popular Playlists',
        'description' => 'Displays popular playlists.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.list-popular-playlists',
        'defaultParams' => array(
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
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of playlists',
                        'value' => '10',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'recent' => 'Most Recent',
                            'view' => 'Most Viewed',
                            'like' => 'Mose Liked',
                            'comment' => 'Most Commented',
                        ),
                        'value' => 'recent',
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
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_grid',
                    array(
                        'label' => 'Grid view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'list' => 'List view',
                            'grid' => 'Grid view',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Playlist View Video Slideshow',
        'description' => 'Displays Playlist Slideshow on detail page',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.playlist-profile-slideshow',
        'requirements' => array(
            'subject' => 'ynultimatevideo_playlist',
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Playlist Profile Info',
        'description' => 'Displays Playlist Info on detail page',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.playlist-profile-info',
        'requirements' => array(
            'subject' => 'ynultimatevideo_playlist',
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Playlist View Video Listings',
        'description' => 'Displays videos listings in playlist detail page.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.playlist-profile-listings',
        'requirements' => array(
            'subject' => 'ynultimatevideo_playlist',
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
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of videos per page',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
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
                    'mode_simple',
                    array(
                        'label' => 'Simple view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_list',
                    array(
                        'label' => 'List view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mode_casual',
                    array(
                        'label' => 'Casual view',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
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
                            'simple' => 'Simple view',
                            'list' => 'List view',
                            'casual' => 'Casual view',
                        ),
                        'value' => 'simple',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Ultimate Video - Other Playlists From Member',
        'description' => 'Displays a list of playlist from this member.',
        'category' => 'YN - Ultimate Video',
        'type' => 'widget',
        'name' => 'ynultimatevideo.playlist-profile-same-poster',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfItems',
                    array(
                        'label' => 'Number of playlist',
                        'value' => '6',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'defaultParams' => array(
            'title' => 'Also from this member',
        )
    ),
)
?>