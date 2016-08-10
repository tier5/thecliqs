<?php
class Ynultimatevideo_Model_DbTable_History extends Engine_Db_Table {
	protected $_name = 'ynultimatevideo_history';
	protected $_rowClass = 'Ynultimatevideo_Model_History';

	public function getVideoHistoryPaginator($params = array())
	{
		return Zend_Paginator::factory($this -> getVideoHistorySelect($params));
	}

	public function getHistoryPaginator($params = array())
	{
		return Zend_Paginator::factory($this -> getHistorySelect($params));
	}

	public function getHistorySelect($params = array()) {
		//init value
		$videoTbl = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$videoTblName = $videoTbl->info('name');
		$playlistTbl = Engine_Api::_()->getItemTable('ynultimatevideo_playlist');
		$playlistTblName = $playlistTbl->info('name');
		$tblName = $this->info('name');
		$user_id = $params['user_id'];
		$categoryTbl = Engine_Api::_() -> getItemTable('ynultimatevideo_category');

		// get video select
		$videoSelect = $videoTbl->select()
			->from($videoTblName, array(
				'item_id' => 'video_id',
				'title',
				'search',
				'like_count',
				'view_count',
				'comment_count',
				'favorite_count',
				'creation_date',
				'status',
				'category_id',
				'rating',
				'featured',
				new Zend_Db_Expr ("'ynultimatevideo_video' AS item_type")
			))
			->setIntegrityCheck(false);

		$videoSelect->join($tblName, "$tblName.user_id = $user_id AND $tblName.item_id = video_id AND $tblName.item_type = 'ynultimatevideo_video'", array("$tblName.modified_date", "$tblName.history_id"));

		// get playlist select
		$playlistSelect = $playlistTbl->select()
			->from($playlistTblName, array(
				'item_id' => 'playlist_id',
				'title',
				new Zend_Db_Expr ("1 AS search"),
				'like_count',
				'view_count',
				'comment_count',
				new Zend_Db_Expr ("'0' AS favorite_count"),
				'creation_date',
				new Zend_Db_Expr ("'0' AS status"),
				'category_id',
				new Zend_Db_Expr ("'0' AS rating"),
				new Zend_Db_Expr ("'0' AS featured"),
				new Zend_Db_Expr ("'ynultimatevideo_playlist' AS item_type")
			))
			->setIntegrityCheck(false);

		$playlistSelect->join($tblName, "$tblName.user_id = $user_id AND $tblName.item_id = playlist_id AND $tblName.item_type = 'ynultimatevideo_playlist'", array("$tblName.modified_date", "$tblName.history_id"));

		$selects = array();
		if (empty($params['type']) || $params['type'] == 'all' || $params['type'] == 'ynultimatevideo_video') {
			$selects[] = $videoSelect;
		}
		if (empty($params['type']) || $params['type'] == 'all' || $params['type'] == 'ynultimatevideo_playlist') {
			$selects[] = $playlistSelect;
		}

		foreach ($selects as $select) {
			if(isset($params['keyword']) && !empty($params['keyword'])) {
				$select->where('title LIKE ?', '%'.$params['keyword'].'%');
			}

			if (isset($params['category']) && !empty($params['category']) && is_numeric($params['category'])) {
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
					$select->where('category_id IN (?)', $categories);
				}
			}
		}

		$db = $this->getAdapter();
		$select = $db->select()->union($selects);

		if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = 'creation_date';
					$params['direction'] = 'DESC';
					break;

				case 'most_liked':
					$params['order'] = 'like_count';
					$params['direction'] = 'DESC';
					break;

				case 'most_viewed':
					$params['order'] = 'view_count';
					$params['direction'] = 'DESC';
					break;

				case 'most_commented':
					$params['order'] = 'view_count';
					$params['direction'] = 'DESC';
					break;

				case 'featured':
					$params['order'] = 'featured';
					$params['direction'] = 'DESC';
					break;

				// may support direction
//				case 'a_z':
//					$params['order'] = 'title';
//					$params['direction'] = 'ASC';
//					break;
//
//				case 'z_a':
//					$params['order'] = 'title';
//					$params['direction'] = 'DESC';
//					break;

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
			$select -> order('modified_date DESC');
		}
		return $select;
	}

	public function getVideoHistorySelect($params = array())
	{
		$videoTbl = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$videoTblName = $videoTbl->info('name');
		$user_id = $params['user_id'];
		$tblName = $this->info('name');

		$videoSelect = $videoTbl->select()
			->from($videoTblName, array(
				'video_id',
				'title',
				'description',
				'search',
				'like_count',
				'view_count',
				'comment_count',
				'duration',
				'owner_id',
				'photo_id',
				'favorite_count',
				'status',
				'type',
				'creation_date',
				'description',
				'rating',
				'category_id',
			))
			->where("$videoTblName.search = 1")
			->where("$videoTblName.status = 1")
			->setIntegrityCheck(false);

		if ($user_id) {
			$videoSelect->join($tblName, "$tblName.user_id = $user_id AND $tblName.item_id = video_id AND $tblName.item_type = 'ynultimatevideo_video'", array("$tblName.modified_date", "$tblName.history_id"));
		}
		else {
			$videoSelect->join($tblName, "$tblName.item_id = video_id AND $tblName.item_type = 'ynultimatevideo_video'", array("$tblName.modified_date", "$tblName.history_id"));
		}

//		$videoSelect -> order('modified_date DESC');

		return $videoSelect;
	}

	public function updateItem($user, $item)
	{
		$select = $this->select()
			->where('user_id = ?', $user->getIdentity())
			->where('item_id = ?', $item->getIdentity())
			->where('item_type = ?', $item->getType());
		$row = $this->fetchRow($select);
		if (!$row) {
			$row = $this->createRow();
			$row->user_id = $user->getIdentity();
			$row->item_type = $item->getType();
			$row->item_id = $item->getIdentity();
			$row->creation_date = date('Y-m-d H:i:s');
		}
		$row->modified_date = date('Y-m-d H:i:s');
		$row->save();
	}

	public function removeHistory($user, $ids = array())
	{
		$where = array();
		$where[] = $this->getAdapter()->quoteInto('user_id = ?', $user->getIdentity());
		if (!empty($ids)) {
			$where[] = $this->getAdapter()->quoteInto('history_id IN (?)', $ids);
		}
		$this->delete($where);
	}

	public function getCategoryIdsHistory($userId)
	{
		$videoTbl = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$videoTblName = $videoTbl->info('name');

		$videoIds = $this->getVideoIdsHistory($userId);

		$categoryIds = $videoTbl->select()
			->distinct()
			->from($videoTblName, 'category_id')
			->where('category_id <> 0')
			->where('video_id IN (?)', $videoIds)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN);

		return $categoryIds;
	}

	public function getVideoIdsHistory($userId)
	{
		$videoIds = $this->select()
			->from($this, 'item_id')
			->where("user_id = $userId")
			->where("item_type = 'ynultimatevideo_video'")
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN);

		return $videoIds;
	}
}
