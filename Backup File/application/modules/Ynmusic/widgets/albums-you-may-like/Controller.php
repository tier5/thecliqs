<?php
class Ynmusic_Widget_AlbumsYouMayLikeController extends Engine_Content_Widget_Abstract {
	public function indexAction(){
		$viewer = Engine_Api::_()->user()->getViewer();
		$likeTbl = Engine_Api::_()->getDbTable('likes', 'core');
		$likeSelect = $likeTbl->select()
			->where('poster_type = ?', 'user')
			->where('poster_id = ?', $viewer->getIdentity())
			->where('resource_type = ?', 'ynmusic_album');
		$rows = $likeTbl->fetchAll($likeSelect);
		$albumIds = array();
		foreach ($rows as $row) {
			$albumIds[] = $row->resource_id;
		}		
		$genreIds = array();
		$artistIds = array();
		foreach ($albumIds as $id) {
			$album = Engine_Api::_()->getItem('ynmusic_album', $id);
			if ($album) {
				$genreIds = array_merge($genreIds, Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getGenreIdsByItem($album));
				$artistIds = array_merge($artistIds, Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic') -> getArtistIdsByItem($album));
			}
		}
		$ids = array();
		if (!empty($genreIds)) {
			$ids = array_merge($ids, Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getItemIdsAssoc($genreIds, 'ynmusic_album'));
		}
		if (!empty($artistIds)) {
			$ids = array_merge($ids, Engine_Api::_()->getDbTable('artistmappings', 'ynmusic')->getItemIdsAssoc($artistIds, 'ynmusic_album'));
		}
		
		if (empty($ids)) {
			return $this->setNoRender();
		}
		
		$limit = $this->_getParam('numOfItemsShow', 5);
		$table = Engine_Api::_()->getItemTable('ynmusic_album');
		$select = $table->select()
			->where('album_id IN (?)', $ids)
			->where('album_id NOT IN (?)', $albumIds);
		if ($limit) $select->limit($limit);
		$albums = $table->fetchAll($select);
		if (!count($albums)) {
			return $this->setNoRender();
		}
		$this->view->albums = $albums;
	}
}
?>
