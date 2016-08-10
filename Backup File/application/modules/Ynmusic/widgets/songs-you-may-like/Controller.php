<?php
class Ynmusic_Widget_SongsYouMayLikeController extends Engine_Content_Widget_Abstract {
	public function indexAction(){
		$viewer = Engine_Api::_()->user()->getViewer();
		$likeTbl = Engine_Api::_()->getDbTable('likes', 'core');
		$likeSelect = $likeTbl->select()
			->where('poster_type = ?', 'user')
			->where('poster_id = ?', $viewer->getIdentity())
			->where('resource_type = ?', 'ynmusic_song');
		$rows = $likeTbl->fetchAll($likeSelect);
		$songIds = array();
		foreach ($rows as $row) {
			$songIds[] = $row->resource_id;
		}		
		$genreIds = array();
		$artistIds = array();
		foreach ($songIds as $id) {
			$song = Engine_Api::_()->getItem('ynmusic_song', $id);
			if ($song) {
				$genreIds = array_merge($genreIds, Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getGenreIdsByItem($song));
				$artistIds = array_merge($artistIds, Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic') -> getArtistIdsByItem($song));
			}
		}
		
		$ids = array();
		if (!empty($genreIds)) {
			$ids = array_merge($ids, Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getItemIdsAssoc($genreIds, 'ynmusic_song'));
		}
		if (!empty($artistIds)) {
			$ids = array_merge($ids, Engine_Api::_()->getDbTable('artistmappings', 'ynmusic')->getItemIdsAssoc($artistIds, 'ynmusic_song'));
		}
		
		if (empty($ids)) {
			return $this->setNoRender();
		}
		
		$limit = $this->_getParam('numOfItemsShow', 5);
		$table = Engine_Api::_()->getItemTable('ynmusic_song');
		$select = $table->select()
			->where('song_id IN (?)', $ids)
			->where('song_id NOT IN (?)', $songIds);
		if ($limit) $select->limit($limit);
		$songs = $table->fetchAll($select);
		if (!count($songs)) {
			return $this->setNoRender();
		}
		$this->view->songs = $songs;
	}
}
?>
