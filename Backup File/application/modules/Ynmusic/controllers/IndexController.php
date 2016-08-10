<?php

class Ynmusic_IndexController extends Core_Controller_Action_Standard {
	public function indexAction() {
		// $table = Engine_Api::_()->getDbTable('songs', 'ynmusic');
		// $songs = $table->fetchAll($table->getSongsSelect(array()));
		// require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/SongWave.php';
		// $waveParh = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.'waves.png';
		// $play_top = '#00d6ff';
		// $play_bot = '#80ebff';
		// $noplay_top = '#666666';
		// $noplay_bot = '#b3b3b3';
		// $storage = Engine_Api::_() -> storage();
		// $waveApi = new SongWave();
		// foreach ($songs as $song) {
			// $params = array(
	            // 'parent_type' => 'ynmusic_song',
	            // 'parent_id' => $song->getIdentity(),
	        // );
			// $waveApi->createWavePhoto($song->getFilePath(), $play_top, $play_bot);
	        // $aMain = $storage -> create($waveParh, $params);
	        // $song->wave_play = $aMain -> file_id;
			// $waveApi->createWavePhoto($song->getFilePath(), $noplay_top, $noplay_bot);
	        // $aMain = $storage -> create($waveParh, $params);
	        // $song->wave_noplay = $aMain -> file_id;
			// $song->save();
		// }
		$this->_helper->content->setEnabled()->setNoRender();
	}
	
	public function listingAction() {
		$this->_helper->content->setEnabled()->setNoRender();
	}
	
	public function uploadPhotoAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$user = Engine_Api::_() -> user() -> getViewer();
		if (!$user || !$user -> getIdentity())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}
		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $error
					)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload File.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}

		if ($_FILES['files']['size'][0] > 1000 * 1024)
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Exceeded filesize limit.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}
		$temp_file = array(
			'type' => $_FILES['files']['type'][0],
			'tmp_name' => $_FILES['files']['tmp_name'][0],
			'name' => $_FILES['files']['name'][0],
		);
		$item = Engine_Api::_() -> getItem($this ->_getParam('item_type'), $this ->_getParam('item_id'));
		$save = $this ->_getParam('save', 1);
		$upload_type = $this ->_getParam('upload_type');
		$photo_id = $item -> setPhoto($temp_file, $upload_type, $save);
		
		$file = Engine_Api::_()->getDbtable('files', 'storage')->find($photo_id)->current();
		if($file) {
			$photo_url = $file->map();
		}
		
		$status = true;
		$name = $_FILES['files']['name'][0];
		
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
				'status' => $status,
				'name' => $name,
				'photo_url' => $photo_url,
				'photo_id' => $photo_id
		)))));
	}
	
	public function validateAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/Soundcloud.php';
		
		$setting = Engine_Api::_()->getApi('settings', 'core');
		$clientId = $setting->getSetting('ynmusic_sound_clientid', "");
		$clientSecret = $setting->getSetting('ynmusic_sound_clientsecret', "");
		try {
			$client = new Services_Soundcloud($clientId, $clientSecret);
			$track_url = $this -> _getParam('url');
			
			// options to prevent HTTP 302 response when sending apis
			$curlOptions = array(
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_SSL_VERIFYPEER => false
			);
			$track = json_decode($client -> get("resolve", array('url' => $track_url), $curlOptions));
		} catch (Exception $e) {
	      	echo Zend_Json::encode(array('status' => 'false'));
			exit ;
	    }
		if ($track -> permalink) {
			echo Zend_Json::encode(array('status' => 'true', 'title' => $track -> title, 'permalink' => $track -> id, 'idValue' => $this -> _getParam('idValue'), 'idTitle' => $this -> _getParam('idTitle'), 'idEdit' => $this -> _getParam('idEdit'), 'idAction' => $this -> _getParam('idAction'), 'idSave' => $this -> _getParam('idSave')));
		} else {
			echo Zend_Json::encode(array('status' => 'false'));
		} exit ;
	}

	public function suggestArtistAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$table = Engine_Api::_() -> getItemTable('ynmusic_artist');

		// Get params
		$text = $this -> _getParam('text', $this -> _getParam('search', $this -> _getParam('value')));
		$limit = (int)$this -> _getParam('limit', 10);

		// Generate query
		$select = $table -> select();

		if (null !== $text) {
			$select -> where('`' . $table -> info('name') . '`.`title` LIKE ?', '%' . $text . '%');
		}
		$select -> limit($limit);

		// Retv data
		$data = array();
		foreach ($select->getTable()->fetchAll($select) as $artist) {
			$data[] = array('id' => $artist -> getIdentity(), 'label' => $artist -> getTitle(), // We should recode this to use title instead of label
			'title' => $artist -> getTitle(), 'url' => $artist -> getHref(), 'type' => 'artist', );
		}
		// send data
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}

	public function suggestGenreAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$table = Engine_Api::_() -> getItemTable('ynmusic_genre');

		// Get params
		$text = $this -> _getParam('text', $this -> _getParam('search', $this -> _getParam('value')));
		$limit = (int)$this -> _getParam('limit', 10);

		// Generate query
		$select = $table -> select();

		if (null !== $text) {
			$select -> where('`' . $table -> info('name') . '`.`title` LIKE ?', '%' . $text . '%');
		}
		
		$isAdmin = $this -> _getParam('isAdmin');
		if (!empty($isAdmin)) {
			$select -> where('`' . $table -> info('name') . '`.`isAdmin` = ?', '1');
		}
		
		$select -> limit($limit);

		// Retv data
		$data = array();
		foreach ($select->getTable()->fetchAll($select) as $genre) {
			$data[] = array('id' => $genre -> getIdentity(), 'label' => $genre -> getTitle(), // We should recode this to use title instead of label
			'title' => $genre -> getTitle(), 'url' => $genre -> getHref(), 'type' => 'genre', );
		}

		// send data
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}
	
	public function updatePlayCountAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		if(Engine_Api::_()->core()->hasSubject()) {
	        $subject = Engine_Api::_()->core()->getSubject();
			if (isset($subject->play_count)) {
				$subject->play_count++;
				$subject->save();
			}
		}
	}
	
	public function repositionAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		if(Engine_Api::_()->core()->hasSubject()) {
	        $subject = Engine_Api::_()->core()->getSubject();
			if (!$subject->isEditable()) {
            	echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have permission to do this.')));
        	}
			$position = $this->_getParam('position', null);
	        if (is_null($position)) {
	            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The request is invalid.')));
	        }
			$subject->cover_top = $position;
	        $subject->save();
	        echo Zend_Json::encode(array('status' => true));
		}
		else {
			echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The object can not be found.')));
		}
    }
}
