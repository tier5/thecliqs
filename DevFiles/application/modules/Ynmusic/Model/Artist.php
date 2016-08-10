<?php
class Ynmusic_Model_Artist extends Core_Model_Item_Abstract {
	
	public function getRelatedArtists() {
		
		$arrArtist = array();
		
		$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
		$genreIds = $genreMappingsTable -> getGenreIdsByItem($this);
		
   		$tableArtist = $this -> getTable();
		$select = $tableArtist -> select()
							   -> where('country = ?', $this -> country)
							   -> where('artist_id <> ?', $this -> getIdentity())
							   -> where('isAdmin = 1');
							   
		$artists = $tableArtist -> fetchAll($select);
		foreach($artists as $artist) {
			$genreItemIds = $genreMappingsTable -> getGenreIdsByItem($artist);
			foreach($genreItemIds as $genreItemId) {
				if(in_array($genreItemId, $genreIds)){
					$arrArtist[] = $artist;
					break;
				}
			}
		}			   
		return $arrArtist;					   
    }
	
	public function getSlug($str = NULL, $maxstrlen = 64)
	{
		$str = $this -> getTitle();
		if (strlen($str) > 32)
		{
			$str = Engine_String::substr($str, 0, 32) . '...';
		}
		$str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		$str = trim($str, '-');
		if (!$str)
		{
			$str = '-';
		}
		return $str;
	}
	
	public function getHref($params = array())
	{
		if (!$this->isAdmin) return false;
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynmusic_artist_profile',
			'reset' => true,
			'id' => $this -> getIdentity(),
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
		
	protected function _delete() {
		// get mapping table
		$artistGenreTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
		// Delete all genre with artist in mapping table
		$artistGenreTable -> deleteGenresByItem($this);

		parent::_delete();
	}

	public function getDescription() {
		return $this -> short_description;
	}

	public function setPhoto($photo, $fieldName, $save = 1) {
		if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
		else if( $photo instanceof Storage_Model_File ) {
      		$file = $photo->temporary();
      		$name = $photo->name;
    	}
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else
        if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $name = basename($file);
        }
        else {
            throw new Ynmusic_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
        }
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_type' => $this -> getType(), 'parent_id' => $this -> getIdentity());
		// Save
		$storage = Engine_Api::_() -> storage();
		// Resize image (main)
		$image = Engine_Image::factory();
		if ($fieldName == 'cover_id') {
			$image -> open($file) -> write($path.'/m_'.$name) -> destroy();
		}
		else {
			$image -> open($file) -> resize(720, 720) -> write($path.'/m_'.$name) -> destroy();
		}
		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();
		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(100, 100) -> write($path . '/in_' . $name) -> destroy();
		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;
		$image -> resample($x, $y, $size, $size, 65, 65) -> write($path . '/is_' . $name) -> destroy();
		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);
		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');
		// Update row
		if ($save) {
			$this -> $fieldName = $iMain -> getIdentity();
			if ($fieldName == 'cover_id') {
				$this->cover_top = 0;
			}
			$this -> save();
		}
		return $this;
	}

	public function getCountItems($type) {
		$count = 0;
		switch ($type) {
			case 'ynmusic_song':
				$paginator = Engine_Api::_()->getItemTable($type)->getSongsPaginator(array('artist_id'=>$this->getIdentity()));
				$count = $paginator->getTotalItemCount();
				break;
			case 'ynmusic_album':
				$paginator = Engine_Api::_()->getItemTable($type)->getAlbumsPaginator(array('artist_id'=>$this->getIdentity()));
				$count = $paginator->getTotalItemCount();
				break;
		}
		return $count;
	}
	
	public function getTitle() {
		$view = Zend_Registry::get('Zend_View');
		return $view -> translate($this -> title);
	}
	
	public function getGenres($params = array()) {
		$params['type'] = 'artist';
		$view = Zend_Registry::get('Zend_View');
		$genre_arr = array();
		$genreIds = Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getGenreIdsByItem($this);
		if (empty($genreIds)) return $genre_arr;
		$genresDbTbl = Engine_Api::_()->getDbTable('genres', 'ynmusic');
		$select = $genresDbTbl->getSelect(array('genre_ids'=>$genreIds));
		$genres = $genresDbTbl->fetchAll($select);
		foreach ($genres as $genre) {
			$genre_arr[] = ($genre->isAdmin) ? $view->htmlLink($genre->getHref($params), $genre->getTitle()) : $genre->getTitle();
		}
		return $genre_arr;
	}

	public function isEditable() {
		return Engine_Api::_()->user()->getViewer()->isAdmin();
	}
}
