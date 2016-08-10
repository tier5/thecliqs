<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Model_DbTable_Videos extends Engine_Db_Table {
	protected $_name = 'ynultimatevideo_videos';
	protected $_rowClass = "Ynultimatevideo_Model_Video";

	public function importItem($item, $category_id)
	{
		if ($item->getType() != 'video') return false;
		if ($this->getImportedItem($item)) {
			$video = $this->getImportedItem($item);
		}
		else {
			//import video info
			$values	= array(
					'title' => $item->title,
					'description' => ($item->description) ? $item->description : '',
					'owner_id' => $item->owner_id,
					'owner_type' => $item->owner_type,
					'parent_type' => $item -> parent_type,
					'parent_id' => $item -> parent_id,
					'search' => $item -> search,
					'category_id' => $category_id,
					'creation_date' => $item -> creation_date,
					'modified_date' => $item -> modified_date,
					'view_count' => $item -> view_count,
					'comment_count' => $item -> comment_count,
					'type' => $item -> type,
					'code' => $item -> code,
					'rating' => $item -> rating,
					'status' => $item -> status,
					'duration' => $item -> duration,
					'rotation' => $item -> rotation,
					'import_id' => $item->getIdentity(),
					'import_type' => $item -> getType()
			);
			if (Engine_Api::_()->hasModuleBootstrap('ynvideo'))
			{
				$values['featured'] = $item -> featured;
				$values['favorite_count'] = $item -> favorite_count;
			}
			$video = $this->createRow();
			$video->setFromArray($values);
			$video->save();

			//clone video file
			if ($item->file_id) {
				$videoFile = Engine_Api::_()->getItemTable('storage_file')->getFile($item->file_id);
				if ($videoFile) {
					$video -> setVideo($videoFile);
				}
			}

			//clone video photo
			if (Engine_Api::_()->hasModuleBootstrap('ynvideo') && $item -> large_photo_id)
			{
				$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($item->large_photo_id);
			}
			elseif ($item->photo_id)
			{
				$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($item->photo_id);
			}
			if ($photo) {
				$video->setPhoto($photo);
			}

			// get better thumbnail
			if($video -> type == 1)
			{
				$adapter = Ynultimatevideo_Plugin_Factory::getPlugin($video -> type);
				$adapter -> setParams(array(
					'code' => $video -> code,
					'video_id' => $video -> getIdentity()
				));

				if($adapter -> getVideoLargeImage())
					$video -> setPhoto($adapter -> getVideoLargeImage());
				$video->save();
			}

			//clone authorization
			Engine_Api::_()->ynultimatevideo()->cloneAuth($item, $video);
			try
			{
				$ul_rTable = Engine_Api::_()->getDbTable('ratings', 'ynultimatevideo');
				$ul_fTable = Engine_Api::_()->getDbTable('favorites', 'ynultimatevideo');
				$ul_wTable = Engine_Api::_()->getDbTable('watchlaters', 'ynultimatevideo');
				if (Engine_Api::_()->hasModuleBootstrap('ynvideo'))
				{
					// clone favorite
					$fTable = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
					$select = $fTable -> select() -> where('video_id = ?', $item -> getIdentity());
					$favorites = $fTable -> fetchAll($select);
					foreach($favorites as $favorite)
					{
						$row = $ul_fTable->createRow();
						$row -> video_id = $video -> getIdentity();
						$row -> user_id = $favorite -> user_id;
						$row -> save();
					}

					// clone watchlaters
					$wTable = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
					$select = $wTable -> select() -> where('video_id = ?', $item -> getIdentity());
					$watchlaters = $wTable -> fetchAll($select);
					foreach($watchlaters as $watchlater)
					{
						$row = $ul_wTable->createRow();
						$row -> video_id = $video -> getIdentity();
						$row -> user_id = $watchlater -> user_id;
						$row -> watched = $watchlater -> watched;
						$row -> watched_date = $watchlater -> watched_date;
						$row -> creation_date = $watchlater -> creation_date;
						$row -> save();
					}

					// clone ratings
					$rTable = Engine_Api::_()->getDbTable('ratings', 'ynvideo');
					$select = $rTable -> select() -> where('video_id = ?', $item -> getIdentity());
					$ratings = $select -> query() -> fetchAll();
					foreach($ratings as $rating)
					{
						Engine_Api::_()->ynultimatevideo() -> setRating($video -> getIdentity(), $rating['user_id'], $rating['rating']);
					}
				}
				else
				{
					// clone ratings
					$rTable = Engine_Api::_()->getDbTable('ratings', 'video');
					$select = $rTable -> select() -> where('video_id = ?', $item -> getIdentity());
					$ratings = $select -> query() -> fetchAll();
					foreach($ratings as $rating)
					{
						Engine_Api::_()->ynultimatevideo() -> setRating($video -> getIdentity(), $rating['user_id'], $rating['rating']);
					}
				}
			}
			catch(exception $e)
			{
				//throw new User_Model_Exception(print_r($e -> getTrace(), true));
			}
		}

		Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->updateItem($video, $item, 'imported');
	}

	public function hasImportedItem($item)
	{
		$result = $this -> getImportedItem($item);
		return ($result)?true:false;
	}

	public function getImportedItem($item) {
		$select = $this->select()->where('import_id = ?', $item->getIdentity())->where('import_type = ?', $item->getType());
		return $this->fetchRow($select);
	}

	public function getVideosByCategory($category_id)
	{
		$select = $this->select()->where('category_id = ?', $category_id);
		return $this->fetchAll($select);
	}

	public function getVideoIdsByCategory($categoryId, $excludes = array())
	{
		$videoIds = $this->select()
				->from($this, 'video_id')
				->where('category_id = ?', $categoryId)
				->where('status = 1')
				->where('search = 1')
				->where('video_id NOT IN (?)', $excludes)
				->query()
				->fetchAll(Zend_Db::FETCH_COLUMN);

		return $videoIds;
	}

	public function getVideosPaginator($params = array(), $order_by = true) {
		return Zend_Paginator::factory($this->getVideosSelect($params));
	}

	public function getVideosSelect($params = array(), $order_by = true) {

		$table = Engine_Api::_()->getDbtable('videos', 'ynultimatevideo');
		$rName = $table->info('name');

		$tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
		$tmName = $tmTable->info('name');

		$categoryTbl = Engine_Api::_() -> getItemTable('ynultimatevideo_category');

		$select = $table->select()->from($table->info('name'))->setIntegrityCheck(false);

		// order
		$order = 'creation_date';
		if (!empty($params['order']))
		{
			$order = $params['order'];
		}
		switch ($order) {
			case 'most_viewed' :
				$select->order("$rName.like_count DESC");
				break;
			case 'rating' :
				$select->order("$rName.rating DESC'");
				break;
			case 'most_liked' :
				$select->order("$rName.like_count DESC");
				break;
			case 'most_commented' :
				$select->order("$rName.comment_count DESC");
				break;
			case 'featured' :
				$select->order("$rName.featured DESC");
				break;
			default :
				$select->order("$rName.creation_date DESC");
		}

		if (!empty($params['keyword'])) {
			$searchTable = Engine_Api::_()->getDbtable('search', 'core');
			$db = $searchTable->getAdapter();
			$sName = $searchTable->info('name');
			$select
					->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
					->where($sName . '.type = ?', 'ynultimatevideo_video')
					->where($sName . '.title LIKE ?', "%{$params['keyword']}%")
			;
		}

		if (!empty($params['title'])) {
			$select->where("$rName.title LIKE ?", "%{$params['title']}%");
		}

		if (!empty($params['status']) && is_numeric($params['status'])) {
			$select->where($rName . '.status = ?', $params['status']);
		}
		if (!empty($params['search']) && is_numeric($params['search'])) {
			$select->where($rName . '.search = ?', $params['search']);
		}
		if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
			$select->where($rName . '.owner_id = ?', $params['user_id']);
		}

		if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
			$select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
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
				$select->where($rName.'.category_id IN (?)', $categories);
			}
		}

		if (!empty($params['tag'])) {
			$select->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
					->where($tmName . '.resource_type = ?', 'ynultimatevideo_video')
					->where($tmName . '.tag_id = ?', $params['tag']);
		}

		if (!empty($params['videoIds']) && is_array($params['videoIds']) && count($params['videoIds']) > 0) {
			$select->where('video_id in (?)', $params['videoIds']);
		}

		if (isset($params['type']) && is_numeric($params['type'])) {
			$select->where($table->info('name') . '.type = ?', $params['type']);

		}

		if (isset($params['featured']) && is_numeric($params['featured'])) {
			$select->where('featured = ?', $params['featured']);
		}

		//Owner in Admin Search
		if (!empty($params['owner'])) {
			$key = stripslashes($params['owner']);
			$select->setIntegrityCheck(false)
					->join('engine4_users as u1', "u1.user_id = $rName.owner_id", '')
					->where("u1.displayname LIKE ?", "%$key%");
		}

		if (!empty($params['fieldOrder'])) {
			if ($params['fieldOrder'] == 'owner') {
				$select->setIntegrityCheck(false)
						->join('engine4_users as u2', "u2.user_id = $rName.owner_id", '')
						->order("u2.displayname {$params['order']}");
			} else {
				$select->order("{$params['fieldOrder']} {$params['order']}");
			}
		}

		if (!empty($params['parent_type'])) {
			$select->where('parent_type = ?', $params['parent_type']);
		}

		if (!empty($params['parent_id'])) {
			$select->where('parent_id = ?', $params['parent_id']);
		}
		return $select;
	}

	public function getAllChildrenVideosByCategory($node)
	{
		$return_arr = array();
		$cur_arr = array();
		$list_categories = array();
		Engine_Api::_()->getItemTable('ynultimatevideo_category') -> appendChildToTree($node, $list_categories);
		foreach($list_categories as $category)
		{
			$select = $this->select()->where('category_id = ?', $category -> category_id);
			$cur_arr = $this->fetchAll($select);
			if(count($cur_arr) > 0)
			{
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}
}