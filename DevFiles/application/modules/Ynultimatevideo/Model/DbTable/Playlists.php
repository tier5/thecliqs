<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Model_DbTable_Playlists extends Engine_Db_Table {

    protected $_rowClass = 'Ynultimatevideo_Model_Playlist';
    protected $_name = 'ynultimatevideo_playlists';

	public function importItem($item) 
	{
		if ($item->getType() != 'ynvideo_playlist') return false;
		$update = false;
		if (!$this -> hasImportedItem($item)) 
			$update = true;
		$playlist = $this->getImportedItem($item);
		//import videos of playlists	
		$videos = $item->getVideos();
		
		$aFiles = array();
		$table = Engine_Api::_()->getDbTable('playlistassoc', 'ynultimatevideo');
		foreach ($videos as $video) 
		{
			if (!Engine_Api::_()->ynultimatevideo()->hasImported($video)) 
			{
				continue;
			}
			$importedVideo = Engine_Api::_()->getItemTable('ynultimatevideo_video')->getImportedItem($video);
			if(!$importedVideo)
			{
				continue;
			}
			$row = $table->getMapRow($playlist->getIdentity(), $importedVideo->getIdentity());
			if (!$row) {
				$mapRow = $table -> createRow();
				$mapRow -> playlist_id = $playlist->getIdentity();
				$mapRow -> video_id = $importedVideo->getIdentity();
				$mapRow -> save();
				$update = true;
			}
		}
		if ($update) Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->updateItem($playlist, $item, 'imported');
	}
	public function hasImportedItem($item)
	{
		$select = $this->select()->where('import_id = ?', $item->getIdentity())->where('import_type = ?', $item->getType()) -> limit(1);
		$playlist = $this -> fetchRow($select);
		return ($playlist)?true:false;
	}
	public function getImportedItem($item) {
		$select = $this->select()->where('import_id = ?', $item->getIdentity())->where('import_type = ?', $item->getType()) -> limit(1);
		$playlist = $this -> fetchRow($select);
		if (!$playlist) {
			$values	= array(
				'user_id' => $item->user_id,
				'title' => $item->title,
				'description' => $item->description,
				'video_count' => $item -> video_count,
				'ordering' => $item -> ordering,
				'import_id' => $item->getIdentity(),
				'import_type' => $item -> getType(),
				'search' => $item -> search,
				'creation_date' => $item -> creation_date,
				'modified_date' => $item -> modified_date
			);
			$playlist = $this->createRow();
			$playlist->setFromArray($values);
			$playlist->save();
			
			//clone playlist photo
			if ($item->photo_id) {
				$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($item->photo_id);
				if ($photo) {
					$playlist->setPhoto($photo);
				}
			}
			
			//clone authorization
			Engine_Api::_()->ynultimatevideo()->cloneAuth($item, $playlist);
		}
		
		return $playlist;
	}

	public function getUserPlaylist($user) {
		$select = $this -> select() -> where('user_id =?', $user -> getIdentity());
		return $this -> fetchAll($select);
	}

	public function getPlaylistsPaginator($params = array()) {
		$paginator = Zend_Paginator::factory($this -> getPlaylistsSelect($params));
		if (!empty($params['page'])) {
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit'])) {
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	public function getPlaylistsSelect($params = array()) {
		$p_table = Engine_Api::_() -> getDbTable('playlists', 'ynultimatevideo');
		$p_name = $p_table -> info('name');
		$select = $p_table -> select() -> from($p_table) -> group("$p_name.playlist_id");

		$tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tagsTblName = $tagsTbl -> info('name');
		$categoryTbl = Engine_Api::_() -> getItemTable('ynultimatevideo_category');

		if (!empty($params['keyword']))
			$select -> where("$p_name.title LIKE ?", "%{$params['keyword']}%");
		if (!empty($params['title']))
			$select -> where("$p_name.title LIKE ?", "%{$params['title']}%");
		if (!empty($params['owner'])) {
			$select -> join('engine4_users as u', "u.user_id = $p_name.user_id", '') -> where("u.displayname LIKE ?", "%{$params['owner']}%");
		}
		if (isset($params['user_id']) && !empty($params['user_id'])) {
			$select->where("$p_name.user_id = ?", $params['user_id']);
		}
		if (array_key_exists('category', $params) && is_numeric($params['category'])) {
			$categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
			$category = $categoryTbl->fetchRow($categorySelect);
			if ($category) {
				$tree = array();
				$node = $categoryTbl -> getNode($category->getIdentity());
				$categoryTbl -> appendChildToTree($node, $tree);
				$categories = array();
				foreach ($tree as $node) {
					array_push($categories, $node->category_id);
				}
				$select->where($p_name.'.category_id IN (?)', $categories);
			}
		}
		if (!empty($params['category_id'])) {
			$select->where("$p_name.category_id = ?", $params['category_id']);
		}

		if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = "$p_name.creation_date";
					$params['direction'] = 'DESC';
					break;

				case 'most_liked':
					$params['order'] = "$p_name.like_count";
					$params['direction'] = 'DESC';
					break;

				case 'most_viewed':
					$params['order'] = "$p_name.view_count";
					$params['direction'] = 'DESC';
					break;

				case 'most_played':
					$params['order'] = "$p_name.play_count";
					$params['direction'] = 'DESC';
					break;

				case 'most_discussed':
					$params['order'] = "$p_name.comment_count";
					$params['direction'] = 'DESC';
					break;

				case 'a_z':
					$params['order'] = "$p_name.title";
					$params['direction'] = 'ASC';
					break;

				case 'z_a':
					$params['order'] = "$p_name.title";
					$params['direction'] = 'DESC';
					break;

				default:
					break;
			}
		}

		if(empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}

		if(!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order("$p_name.creation_date DESC");
		}
		return $select;
	}
}