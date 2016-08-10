<?php return array(
  array(
    'title' => 'Profile Music',
    'description' => 'Displays a member\'s music on their profile.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.profile-music',
    'defaultParams' => array(
      'title' => 'Mp3 Music',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Player',
    'description' => 'Displays a flash player that plays the music the member has selected to play on their profile.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.profile-player',
  ),
    array(
    'title' => 'Browse Music',
    'description' => 'Displays browse music on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.browse-music',
    ),
    array(
    'title' => 'Categories Music',
    'description' => 'Displays categories on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.categories-music',
    'defaultParams' => array(
      'title' => 'Categories',
    ),
    ),
    array(
    'title' => 'Artists Music',
    'description' => 'Displays artists on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.artists-music',
    'defaultParams' => array(
      'title' => 'Artists',
    ),
    ),
    array(
    'title' => 'Singers Music',
    'description' => 'Displays singers on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.singers-music',
    ),
    array(
    'title' => 'New Playlists',
    'description' => 'Displays new playlists on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.new-playlists',
    'defaultParams' => array(
      'title' => 'New Playlists',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of New Playlists show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    array(
    'title' => 'Top Songs',
    'description' => 'Displays top songs on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.top-songs',
    'defaultParams' => array(
      'title' => 'Top Songs',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of Top Songs show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    array(
    'title' => 'Top Downloads',
    'description' => 'Displays top downloads on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'mp3music',
    'name' => 'mp3music.top-downloads',
    'defaultParams' => array(
      'title' => 'Top Downloads',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of Top Downloads show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    array(
    'title' => 'Menu Music',
    'description' => 'Displays menu music on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.menu-music',
    ),
    array(
    'title' => 'Player Album',
    'description' => 'Displays player album on music detail page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.player-album',
    ),
     array(
    'title' => 'Player Playlist',
    'description' => 'Displays player playlist on music detail page page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.player-playlist',
    ),
    
     array(
    'title' => 'Related Music',
    'description' => 'Displays related music on music detail page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.related-music',
    'defaultParams' => array(
      'title' => 'Related Musics',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of Related Music show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    
    array(
    'title' => 'Search',
    'description' => 'Displays search music on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.search-music',
    'defaultParams' => array(
      'title' => 'Search',
    ),
    ),
     array(
    'title' => 'Top Albums Middle',
    'description' => 'Displays top albums on middle of music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.top-albums',
    'defaultParams' => array(
      'title' => 'Top Albums',
    ),
    ),
    array(
    'title' => 'Featured Albums Middle',
    'description' => 'Displays featured albums on middle of music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.featured-albums',
    'defaultParams' => array(
      'title' => 'Featured Albums',
    ),
     'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of featured albums show on slideshow.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    array(
    'title' => 'New Albums Middle',
    'description' => 'Displays new albums on middle of music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.new-albums',
    'defaultParams' => array(
      'title' => 'New Albums',
    ),
    ),
    array(
    'title' => 'Top Albums Right',
    'description' => 'Displays top albums on right of search playlists page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.top-albums-right',
    'defaultParams' => array(
      'title' => 'Top Albums',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of other albums show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    array(
    'title' => 'New Albums Right',
    'description' => 'Displays new albums on right of search playlists page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.new-albums-right',
    'defaultParams' => array(
      'title' => 'New Albums',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of other albums show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
     array(
    'title' => 'Other Albums',
    'description' => 'Displays other albums on player album page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.other-albums',
    'defaultParams' => array(
      'title' => 'Other Albums',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of other albums show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
     array(
    'title' => 'Related Albums',
    'description' => 'Displays related albums on player album page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.related-albums',
    'defaultParams' => array(
      'title' => 'Related Albums',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of related albums show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
     array(
    'title' => 'Other Playlists',
    'description' => 'Displays other playlists on player playlist page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.other-playlists',
    'defaultParams' => array(
      'title' => 'Other Playlists',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of other playlists show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
     array(
    'title' => 'Related Playlists',
    'description' => 'Displays related playlists on player playlist page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.related-playlists',
    'defaultParams' => array(
      'title' => 'Related Playlist',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of related playlists show on page.',
            'value' => 5,
            
          )
        ),
      )
    ),
    ),
    array(
    'title' => 'Search Albums',
    'description' => 'Displays search albums on search albums page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.browse-albums',
    
    ),
    array(
    'title' => 'Search Playlists',
    'description' => 'Displays search playlists on search playlists page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.browse-playlists',
    
    ),
    array(
    'title' => 'Statistics Music',
    'description' => 'Displays statistics on music home page.',
    'category' => 'Mp3 Music',
    'type' => 'widget',
    'name' => 'mp3music.statistics-music',
     'defaultParams' => array(
      'title' => 'Statistics',
    ),
    ),
) ?>