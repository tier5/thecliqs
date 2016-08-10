<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */

return array(
    array(
        'title' => 'YN - Video Channel - Browse Menu',
        'description' => 'Displays a menu in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.browse-menu',
    ),
    array(
        'title' => 'YN - Video Channel - Browse Menu Creation',
        'description' => 'Displays a creation menu in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.browse-menu-creation',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'YN - Video Channel - Browse Search',
        'description' => 'Displays a search box in the video channel page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.browse-search',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => 'Search Item Type',
                        'value' => 'videos',
                        'multiOptions' => array(
                            'videos' => 'Videos',
                            'channels' => 'Channels',
                            'playlists' => 'Playlists'
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Video Channel - Categories',
        'description' => 'Displays categories in the video channel page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.categories',
        'defaultParams' => array(
            'title' => 'Categories'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => 'Search Item Type',
                        'value' => 'videos',
                        'multiOptions' => array(
                            'videos' => 'Videos',
                            'channels' => 'Channels',
                            'playlists' => 'Playlists'
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'YN - Video Channel - Featured Videos',
        'description' => 'Displays featured videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.featured-videos',
        'defaultParams' => array(
            'title' => 'Featured Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Lasted Videos',
        'description' => 'Displays latest videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.latest-videos',
        'defaultParams' => array(
            'title' => 'Latest Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Commented Videos',
        'description' => 'Displays most commented videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-commented-videos',
        'defaultParams' => array(
            'title' => 'Most Commented Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Favorited Videos',
        'description' => 'Displays favorited videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-favorited-videos',
        'defaultParams' => array(
            'title' => 'Most Favorited Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Liked Videos',
        'description' => 'Displays most liked videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-liked-videos',
        'defaultParams' => array(
            'title' => 'Most Liked Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Popular Videos',
        'description' => 'Displays popular videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.popular-videos',
        'defaultParams' => array(
            'title' => 'Popular Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Recommended Videos',
        'description' => 'Displays recommended videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.recommended-videos',
        'defaultParams' => array(
            'title' => 'Recommended Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Top Rated Videos',
        'description' => 'Displays top rated videos in the video channel browse page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.top-rated-videos',
        'defaultParams' => array(
            'title' => 'Top Rated Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Browse Menu Management',
        'description' => 'Displays a management menu in my items page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.browse-menu-management',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'YN - Video Channel - Featured Channels',
        'description' => 'Displays featured channels in all channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.featured-channels',
        'defaultParams' => array(
            'title' => 'Featured Channels',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Latest Channels',
        'description' => 'Displays latest channels in all channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.latest-channels',
        'defaultParams' => array(
            'title' => 'Latest Channels',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Commented Channels',
        'description' => 'Displays most commented channels in all channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-commented-channels',
        'defaultParams' => array(
            'title' => 'Most Commented Channels',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Subscribed Channels',
        'description' => 'Displays subscribed channels in all channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-subscribed-channels',
        'defaultParams' => array(
            'title' => 'Most Subscribed Channels',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Liked Channels',
        'description' => 'Displays most liked channels in all channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-liked-channels',
        'defaultParams' => array(
            'title' => 'Most Liked Channels',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Channel of The Day',
        'description' => 'Displays channel of the day in all channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.channel-of-day',
        'defaultParams' => array(
            'title' => 'Channel of The Day',
            'itemCountPerPage' => 5
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Lasted Playlists',
        'description' => 'Displays latest playlists in all playlists page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.latest-playlists',
        'defaultParams' => array(
            'title' => 'Latest Playlists',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Commented Playlists',
        'description' => 'Displays most commented channels in all playlists page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-commented-playlists',
        'defaultParams' => array(
            'title' => 'Most Commented Playlist',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Most Liked Playlists',
        'description' => 'Displays subscribed channels in all playlists page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.most-liked-playlists',
        'defaultParams' => array(
            'title' => 'Most Liked Playlists',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Create Playlist Button',
        'description' => 'Displays create playlist button in all playlists page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.create-playlist-button',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'YN - Video Channel - Related Videos',
        'description' => 'Displays related videos in video detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.related-videos',
        'requirements' => array(
            'subject' => 'ynvideochannel_video',
        ),
        'defaultParams' => array(
            'title' => 'Related Videos',
            'itemCountPerPage' => 5
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Videos in same Channel',
        'description' => 'Displays videos of channel in video detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.same-channel-videos',
        'requirements' => array(
            'subject' => 'ynvideochannel_video',
        ),
        'defaultParams' => array(
            'title' => 'Videos in same Channel',
            'itemCountPerPage' => 5
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Related Channels',
        'description' => 'Displays related channels in channel detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.related-channels',
        'requirements' => array(
            'subject' => 'ynvideochannel_channel',
        ),
        'defaultParams' => array(
            'title' => 'Related Channels',
            'itemCountPerPage' => 5
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - User Other Videos',
        'description' => 'Displays other videos in video detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.user-other-videos',
        'requirements' => array(
            'subject' => 'ynvideochannel_video',
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - User Other Channels',
        'description' => 'Displays other channels in channel detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.user-other-channels',
        'requirements' => array(
            'subject' => 'ynvideochannel_channel',
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - User Other Playlist',
        'description' => 'Displays other playlists in playlist detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.user-other-playlists',
        'requirements' => array(
            'subject' => 'ynvideochannel_playlist',
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Channel Videos',
        'description' => 'Displays videos of a channel.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-channel-videos',
        'requirements' => array(
            'subject' => 'ynvideochannel_channel',
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Detail Playlist Videos Grid View',
        'description' => 'Displays videos slide show in playlist detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.detail-playlist-grid',
        'requirements' => array(
            'subject' => 'ynvideochannel_playlist',
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Detail Playlist Videos Slide Show',
        'description' => 'Displays videos slide show in playlist detail page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.detail-playlist-slideshow',
        'requirements' => array(
            'subject' => 'ynvideochannel_playlist',
        )
    ),
    array(
        'title' => 'YN - Video Channel - User Profile Videos',
        'description' => 'Displays a member\'s videos on their profile.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.profile-videos',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Video Channel',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - User Profile Channels',
        'description' => 'Displays a member\'s channels on their profile.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.profile-channels',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Channels',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - User Profile Video Playlists',
        'description' => 'Displays a member\'s video playlists on their profile.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.profile-video-playlists',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Video Playlists',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - User Profile Favorite Videos',
        'description' => 'Displays a member\'s favorite videos on their profile.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.profile-favorite-videos',
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Favorite Videos',
            'itemCountPerPage' => 6
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Video Tags',
        'description' => 'Displays a list of video tags.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.video-tags',
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
        'title' => 'YN - Video Channel - Videos Listing',
        'description' => 'Displays a list of videos.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-videos',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Channels Listing',
        'description' => 'Displays a list of channels.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-channels',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Playlists Listing',
        'description' => 'Displays a list of playlists.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-playlists',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - My Videos Listing',
        'description' => 'Displays a list of videos in My Videos page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-my-videos',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - My Favorite Videos Listing',
        'description' => 'Displays a list of videos in My Favorite Videos page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-my-favorite-videos',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - Subscriptions Listing',
        'description' => 'Displays a list of channels on Subscriptions page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-subscription-channels',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - My Channels Listing',
        'description' => 'Displays a list of channels in My Channels page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-my-channels',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
    array(
        'title' => 'YN - Video Channel - My Playlists Listing',
        'description' => 'Displays a list of playlists in My Playlists page.',
        'category' => 'YN - Video Channel',
        'type' => 'widget',
        'name' => 'ynvideochannel.list-my-playlists',
        'defaultParams' => array(
            'itemCountPerPage' => 10
        ),
        'isPaginated' => true
    ),
)
?>