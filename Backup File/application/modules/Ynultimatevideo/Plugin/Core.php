<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Core
{

	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getDbTable('videos', 'ynultimatevideo');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'ynultimatevideo');
	}

	public function onUserDeleteBefore($event)
	{
		$payload = $event -> getPayload();
		if ($payload instanceof User_Model_User)
		{

			// Delete videos
			$videoTable = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo');
			$videoSelect = $videoTable -> select() -> where('owner_id = ?', $payload -> getIdentity());
			foreach ($videoTable->fetchAll($videoSelect) as $video)
			{
				Engine_Api::_() -> getApi('core', 'ynultimatevideo') -> deleteVideo($video);
			}

			// Delete playlists
			$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynultimatevideo');
			$playlistSelect = $playlistTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($playlistTable->fetchAll($playlistSelect) as $playlist)
			{
				$playlist -> delete();
			}

			// Delete signatures
			$signatureTable = Engine_Api::_() -> getDbtable('signatures', 'ynultimatevideo');
			$signatureTable -> delete("user_id = {$payload->getIdentity()}");
		}
	}

}
