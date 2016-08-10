<?php
class Ynfeed_Model_DbTable_ActionTypes extends Activity_Model_DbTable_ActionTypes {

  protected $_name = 'activity_actiontypes';
  protected $_actionTypes;

  public function getEnabledGroupedActionTypes($actionFilter = NULL) {
    // Get enabled modules
    $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction') == 1) {
      $exclude = 'friends_follow';
    } else {
      $exclude = 'friends';
    }

    // Get types
    $actionTypes = $this->select()
            ->from($this->info('name'), array('type', 'module'))
            ->where('enabled = ?', 1)
            ->where('displayable > ?', 0)
            ->where('module IN(?)', $enabledModuleNames)
            ->where('type != ?', $exclude)
            ->query()
            ->fetchAll()
    ;

    // Group them
    $groupedActionTypes = array('all' => null);
    foreach ($actionTypes as $actionType) 
    {
      // All
      // Photo
      if (false !== strpos($actionType['type'], 'photo')) {
        $groupedActionTypes['photo'][] = $actionType['type'];
      }
      // Music
      if (false !== strpos($actionType['type'], 'music_playlist') ||
              false !== strpos($actionType['type'], 'song')) {
        $groupedActionTypes['music'][] = $actionType['type'];
      }
      // Video
      if (false !== strpos($actionType['type'], 'video')) {
        $groupedActionTypes['video'][] = $actionType['type'];
      }
      // Posts?
      if (//false !== strpos($actionType['type'], 'comment') ||
          //    false !== strpos($actionType['type'], 'topic') ||
              false !== strpos($actionType['type'], 'post') ||
              false !== strpos($actionType['type'], 'status') || 
              $actionType['type'] === 'share') {
        $groupedActionTypes['posts'][] = $actionType['type'];
      }

      // Like?
      if (false !== strpos($actionType['type'], 'like')) {
        $groupedActionTypes['like'][] = $actionType['type'];
      }
	  
	  $isSocialStream = false;
	  if($actionFilter && false !== strpos($actionType['type'], 'socialstream'))
	  {
	  	  switch ($actionFilter) {
				case 'facebook_feeds':
					$groupedActionTypes['facebook_feeds'][] = 'socialstream_facebook';
					$isSocialStream = true;
					break;
				
				case 'linkedin_feeds':
					$groupedActionTypes['linkedin_feeds'][] = 'socialstream_linkedin';
					$isSocialStream = true;
					break;
				case 'twitter_feeds':
					$groupedActionTypes['twitter_feeds'][] = 'socialstream_twitter';
					$isSocialStream = true;
					break;
			}
	  }
	  if(!$isSocialStream)
	  {
      	// By module?
      	$groupedActionTypes[$actionType['module']][] = $actionType['type']; 
	  }
    }
    return $groupedActionTypes;
  }

}