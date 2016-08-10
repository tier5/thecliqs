<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Plugin_Core
{

	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getDbTable('videos', 'ynvideochannel');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'ynvideochannel_videos');

		$table = Engine_Api::_() -> getDbTable('channels', 'ynvideochannel');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'ynvideochannel_channels');
	}

	public function onUserDeleteBefore($event)
	{
		$payload = $event -> getPayload();
		if ($payload instanceof User_Model_User)
		{
			// Delete videos
			$videoTable = Engine_Api::_() -> getDbtable('videos', 'ynvideochannel');
			$videoSelect = $videoTable -> select() -> where('owner_id = ?', $payload -> getIdentity());
			foreach ($videoTable->fetchAll($videoSelect) as $video)
			{
				Engine_Api::_() -> getApi('core', 'ynvideochannel') -> deleteVideo($video);
			}

			// Delete playlists
			$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynvideochannel');
			$playlistSelect = $playlistTable -> select() -> where('owner_id = ?', $payload -> getIdentity());
			foreach ($playlistTable->fetchAll($playlistSelect) as $playlist)
			{
				$playlist -> delete();
			}

			// Delete channels
			$channelTable = Engine_Api::_() -> getDbtable('channels', 'ynvideochannel');
			$channelTable -> delete("owner_id = {$payload->getIdentity()}");

			// Delete user shared
			$userSharedTable = Engine_Api::_() -> getDbtable('usershareds', 'ynvideochannel');
			$userSharedTable -> delete("user_id = {$payload->getIdentity()}");

			// Delete favourites
			$favouriteTable = Engine_Api::_() -> getDbtable('favourites', 'ynvideochannel');
			$favouriteTable -> delete("user_id = {$payload->getIdentity()}");

			// Delete subscribes
			$subscriptionTable = Engine_Api::_() -> getDbtable('subscribes', 'ynvideochannel');
			$subscriptionTable -> delete("user_id = {$payload->getIdentity()}");
		}
	}

}
