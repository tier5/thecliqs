<?php return array(
	array(
	    'title' => 'YN - Social Music - Main Menu Widget',
	    'description' => 'Music main menu.',
	    'category' => 'YN - Social Music',
	    'type' => 'widget',
	    'name' => 'ynmusic.main-menu',
	    'defaultParams' => array(),
	),
	array(
        'title' => 'YN - Social Music - Manage Menu',
        'description' => 'Displays manage menu in manage page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.manage-menu',
        'defaultParams' => array(
        ),
    ),
    array(
    	'title' => 'YN - Social Music - Search',
		'description' => 'Music Search Widget.',
    	'category' => 'YN - Social Music',
    	'type' => 'widget',
    	'name' => 'ynmusic.search-music',
    	'defaultParams' => array(
    	),
	),
	array(
        'title' => 'YN - Social Music - List Genres',
        'description' => 'Displays a list of genres.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.list-genres',
        'defaultParams' => array(
            'title' => 'Genres',
        ),
    ),
    array(
        'title' => 'YN - Social Music - Artist Profile Cover',
        'description' => 'Displays Artist Cover on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.artist-profile-cover',
        'requirements' => array(
	      'subject' => 'ynmusic_artist',
	    ),
    ),
     array(
        'title' => 'YN - Social Music - Artist Profile Song',
        'description' => 'Displays Artist Songs on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.artist-profile-song',
        'requirements' => array(
	      'subject' => 'ynmusic_artist',
	    ),
	    'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Songs',
            'titleCount' => true,
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    array(
       'title' => 'YN - Social Music - Artist Profile Album',
        'description' => 'Displays Artist Albums on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.artist-profile-album',
        'requirements' => array(
	      'subject' => 'ynmusic_artist',
	    ),
	    'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => true,
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    array(
       'title' => 'YN - Social Music - Artist Profile Info',
        'description' => 'Displays Artist Info on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.artist-profile-info',
        'requirements' => array(
	      'subject' => 'ynmusic_artist',
	    ),
	    'defaultParams' => array(
            'title' => 'Information',
        ),
    ),
    array(
       'title' => 'YN - Social Music - Artist Profile Related',
        'description' => 'Displays Related Artists on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.artist-profile-related',
        'requirements' => array(
	      'subject' => 'ynmusic_artist',
	    ),
	    'defaultParams' => array(
            'title' => 'Related Artists',
        ),
    ),
    array(
        'title' => 'YN - Social Music - Song Profile Cover',
        'description' => 'Displays Song Cover on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.song-profile-cover',
        'requirements' => array(
	      'subject' => 'ynmusic_song',
	    ),
    ),
    array(
        'title' => 'YN - Social Music - Song Profile Info',
        'description' => 'Displays Song Info on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.song-profile-info',
        'requirements' => array(
	      'subject' => 'ynmusic_song',
	    ),
    ),
    array(
        'title' => 'YN - Social Music - Song Profile In Playlists',
        'description' => 'Displays Playlists which song is belong',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.song-profile-in-playlist',
        'isPaginated' => true,
        'requirements' => array(
	      'subject' => 'ynmusic_song',
	    ),
	    'defaultParams' => array(
            'title' => 'In Playlists',
        ),
    ),
    array(
        'title' => 'YN - Social Music - Album Profile Cover',
        'description' => 'Displays Album Cover on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.album-profile-cover',
        'requirements' => array(
	      'subject' => 'ynmusic_album',
	    ),
    ),
	array(
        'title' => 'YN - Social Music - Album Profile Info',
        'description' => 'Displays Album Info on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.album-profile-info',
        'requirements' => array(
	      'subject' => 'ynmusic_album',
	    ),
    ),
	array(
        'title' => 'YN - Social Music - Album Profile More',
        'description' => 'Displays More Albums on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.album-profile-more',
        'isPaginated' => true,
        'requirements' => array(
	      'subject' => 'ynmusic_album',
	    ),
	    'defaultParams' => array(
            'title' => 'More From This User',
            'itemCountPerPage' => 3
            
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Playlist Profile Cover',
        'description' => 'Displays Playlist Cover on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.playlist-profile-cover',
        'requirements' => array(
	      'subject' => 'ynmusic_playlist',
	    ),
    ),
    
	array(
        'title' => 'YN - Social Music - Playlist Profile Info',
        'description' => 'Displays Playlist Info on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.playlist-profile-info',
        'requirements' => array(
	      'subject' => 'ynmusic_playlist',
	    ),
    ),
    
	array(
        'title' => 'YN - Social Music - Playlist Profile More',
        'description' => 'Displays More Playlists from User on detail page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.playlist-profile-more',
        'isPaginated' => true,
        'requirements' => array(
	      'subject' => 'ynmusic_playlist',
	    ),
	    'defaultParams' => array(
            'title' => 'More From This User',
            'itemCountPerPage' => 3
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Played Albums',
        'description' => 'Displays Most Played Albums on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-played-albums',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Played Albums',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Liked Albums',
        'description' => 'Displays Most Liked Albums on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-liked-albums',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Liked Albums',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Discussed Albums',
        'description' => 'Displays Most Discussed Albums on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-discussed-albums',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Discussed Albums',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Played Songs',
        'description' => 'Displays Most Played Songs on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-played-songs',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Played Songs',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Liked Songs',
        'description' => 'Displays Most Liked Songs on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-liked-songs',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Liked Songs',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Discussed Songs',
        'description' => 'Displays Most Discussed Songs on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-discussed-songs',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Discussed Songs',
        ),
    ),
	
	array(
        'title' => 'YN - Social Music - User Music Info',
        'description' => 'Displays Music Info of User',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.user-profile-info',
    ),
    
	array(
        'title' => 'YN - Social Music - Recently Played',
        'description' => 'Displays Recently Played Items on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.recent-played',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Recently Played',
		 ),
	 ),
	 
	 array(
        'title' => 'YN - Social Music - Music Listings',
        'description' => 'Displays music listings in music listing page.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.music-listing',
        'defaultParams' => array(
            'title' => 'Music Listings',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
                
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Playlist Listings',
        'description' => 'Displays playlist listings in playlist browse page.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.playlists-listing',
        'defaultParams' => array(
            'title' => '',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
                
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Featured Albums',
        'description' => 'Displays Featured Albums on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.featured-albums',
	    'defaultParams' => array(
            'title' => 'Featured Albums',
		 ),
	 ),
	 
	 array(
        'title' => 'YN - Social Music - Songs You May Like',
        'description' => 'Displays songs viewer may like.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.songs-you-may-like',
        'defaultParams' => array(
          'title' => 'Songs You May Like',
          'numOfItemsShow' => 5
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
                    'numOfItemsShow',
                    array(
                        'label' => 'Number of items will show?',
                        'value' => 5,
                    ),
                ),
            ),
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Albums You May Like',
        'description' => 'Displays albums viewer may like.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.albums-you-may-like',
        'defaultParams' => array(
          'title' => 'Albums You May Like',
          'numOfItemsShow' => 5
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
                    'numOfItemsShow',
                    array(
                        'label' => 'Number of items will show?',
                        'value' => 5,
                    ),
                ),
            ),
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Played Playlists',
        'description' => 'Displays Most Played Playlists on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-played-playlists',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Played Playlists',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Liked Playlists',
        'description' => 'Displays Most Liked Playlists on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-liked-playlists',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Liked Playlists',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Most Discussed Playlists',
        'description' => 'Displays Most Discussed Playlists on browse page',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.most-discussed-playlists',
        'isPaginated' => true,
	    'defaultParams' => array(
            'title' => 'Most Discussed Playlists',
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Album Listings',
        'description' => 'Displays album listings in album browse page.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.albums-listing',
        'defaultParams' => array(
            'title' => '',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
                
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Song Listings',
        'description' => 'Displays song listings in song browse page.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.songs-listing',
        'defaultParams' => array(
            'title' => '',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
                
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - Artists Listings',
        'description' => 'Displays artist listings in artist browse page.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.artists-listing',
        'defaultParams' => array(
            'title' => '',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - User Profile Albums',
        'description' => 'Displays a member\'s albums on their profile.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.user-profile-albums',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => true,
        ),
        'requirements' => array(
            'user',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - User Profile Songs',
        'description' => 'Displays a member\'s songs on their profile.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.user-profile-songs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Songs',
            'titleCount' => true,
        ),
        'requirements' => array(
            'user',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
    
	array(
        'title' => 'YN - Social Music - User Profile Playlists',
        'description' => 'Displays a member\'s playlists on their profile.',
        'category' => 'YN - Social Music',
        'type' => 'widget',
        'name' => 'ynmusic.user-profile-playlists',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Playlists',
            'titleCount' => true,
        ),
        'requirements' => array(
            'user',
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
                    'view_mode',
                    array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                    )
                ),
            )
        ),
    ),
) ?>
