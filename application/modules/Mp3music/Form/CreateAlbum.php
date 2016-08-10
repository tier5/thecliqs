<?php
class Mp3music_Form_CreateAlbum extends Engine_Form
{
	protected $_album;
	protected $_roles = array(
		'everyone' => 'Everyone',
		'registered' => 'All Registered Members',
		'owner_network' => 'Friends and Networks',
		'owner_member_member' => 'Friends of Friends',
		'owner_member' => 'Friends Only',
		'owner' => 'Just Me'
	);

	public function init()
	{
		// Init form
		$this -> setTitle('Add New Songs') -> setDescription('Choose music from your computer to add to this album.') -> setAttrib('id', 'form-upload-music') -> setAttrib('name', 'album_create') -> setAttrib('enctype', 'multipart/form-data') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));

		// Init name
		$this -> addElement('Text', 'title', array(
			'label' => 'Album Name',
			'maxlength' => '63',
			'filters' => array(
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_StringLength( array('max' => '63')), )
		));

		// Init descriptions
		$this -> addElement('Textarea', 'description', array(
			'label' => 'Album Description',
			'maxlength' => '300',
			'filters' => array(
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_Censor(),
				new Engine_Filter_StringLength( array('max' => '300')),
				new Engine_Filter_EnableLinks(),
			),
		));

		// Init search checkbox
		$this -> addElement('Checkbox', 'search', array(
			'label' => "Show this album in search results",
			'value' => 1,
			'checked' => true,
		));

		// Init download checkbox
		$this -> addElement('Checkbox', 'download', array(
			'label' => "Allow to download this album.",
			'value' => 1,
			'checked' => true,
		));

		// AUTHORIZATIONS
		$user_level = Engine_Api::_() -> user() -> getViewer() -> level_id;
		$allowed_view = @Engine_Api::_() -> getApi('core', 'authorization') -> getPermission($user_level, 'mp3music_album', 'auth_view');
		if (!empty($allowed_view) && strlen($allowed_view) > 2)
		{
			$allowed_view = Zend_Json_Decoder::decode($allowed_view);
			$viewPerms = array();
			foreach ($allowed_view as $allowed)
			{
				$viewPerms[$allowed] = $this -> _roles[$allowed];
			}
			$this -> addElement('Select', 'auth_view', array(
				'label' => 'Privacy',
				'description' => 'Who may see this album?',
				'multiOptions' => $viewPerms,
				'value' => array_shift(array_keys($viewPerms)),
			));
			$this -> auth_view -> getDecorator('Description') -> setOption('placement', 'append');
		}

		$allowed_comment = @Engine_Api::_() -> authorization() -> getPermission($user_level, 'mp3music_album', 'auth_comment');
		if (!empty($allowed_comment) && strlen($allowed_comment) > 2)
		{
			$allowed_comment = Zend_Json_Decoder::decode($allowed_comment);
			$commentPerms = array();
			foreach ($allowed_comment as $allowed)
			{
				$commentPerms[$allowed] = $this -> _roles[$allowed];
			}
			$this -> addElement('Select', 'auth_comment', array(
				'label' => 'Comment Privacy',
				'description' => 'Who may post comments on this album?',
				'multiOptions' => $commentPerms,
				'value' => array_shift(array_keys($commentPerms))
			));
			$this -> auth_comment -> getDecorator('Description') -> setOption('placement', 'append');
		}

		// Init album art
		$this -> addElement('File', 'art', array('label' => 'Album Artwork', ));
		$this -> art -> addValidator('Extension', false, 'jpg,png,gif,jpeg');

		// Init file uploader
		$this -> addElement('Dummy', 'html5_upload', array('decorators' => array( array(
						'ViewScript',
						array(
							'viewScript' => '_Html5Upload.tpl',
							'class' => 'form element',
						)
					)), ));
		// Init hidden file IDs
	  $this -> addElement('Hidden', 'html5uploadfileids', array('value' => '', 'order' => 1));

		$cats = Mp3music_Model_Cat::getCats(0);
		$catPerms = array();
		$catPerms[0] = "";
		foreach ($cats as $cat)
		{
			$catPerms[$cat -> cat_id] = $cat -> title;
			$subCats = Mp3music_Model_Cat::getCats($cat -> cat_id);
			foreach ($subCats as $subCat)
			{
				$catPerms[$subCat -> cat_id] = "-- " . $subCat -> title;
			}
		}
		$this -> addElement('Select', 'music_categorie_id', array(
			'label' => 'Select category',
			'description' => 'What is category of this album?',
			'multiOptions' => $catPerms,
			'style' => "width:160px",
			'value' => array_shift(array_keys($catPerms))
		));

		$allow_artist = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.artist', 1);
		if (!$allow_artist)
		{
			$artists = Mp3music_Api_Core::getArtistRows();
			$artPerms = array();
			$artPerms[0] = "";
			foreach ($artists as $artist)
			{
				$artPerms[$artist -> artist_id] = $artist -> title;
			}
			$this -> addElement('Select', 'music_artist_id', array(
				'label' => 'Select artist',
				'description' => 'What is artist of this album?',
				'multiOptions' => $artPerms,
				'style' => "width:160px",
				'value' => array_shift(array_keys($artPerms))
			));
		}

		$singerTypes = Mp3music_Model_SingerType::getSingerTypes();
		$singerPerms = array();
		$singerPerms[0] = 'Other Singer';
		foreach ($singerTypes as $singerType)
		{
			$singers = $singerType -> getSingers();
			//$artistPerms[] = $artistType->title;
			foreach ($singers as $singer)
			{
				$singerPerms[$singer -> singer_id] = "" . $singer -> title;
			}
		}
		$this -> addElement('Select', 'music_singer_id', array(
			'label' => 'Select singer',
			'description' => Zend_View_Helper_Translate::translate('What is singer of this album?'),
			'multiOptions' => $singerPerms,
			'style' => "width:160px",
			'onchange' => "updateTextFields()",
			'value' => array_shift(array_keys($singerPerms))
		));
		// Init Orther artist field
		$this -> addElement('Text', 'other_singer', array(
			'label' => Zend_View_Helper_Translate::translate('Other Singer Name'),
			'style' => "width:153px",
			'filters' => array(new Engine_Filter_Censor(), ),
		));
		// Init submit
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Music to Album',
			'type' => 'submit',
		));
	}

	public function clearUploads()
	{
		$this -> getElement('fancyuploadfileids') -> setValue('');
	}

	public function saveValues()
	{
		$album = null;
		$values = $this -> getValues();
		$translate = Zend_Registry::get('Zend_Translate');
		if (!empty($values['album_id']))
			$album = Engine_Api::_() -> getItem('mp3music_album', $values['album_id']);
		else
		{
			$album = $this -> _album = Engine_Api::_() -> getDbtable('albums', 'mp3music') -> createRow();
			$album -> title = trim(htmlspecialchars($values['title']));
			if (empty($album -> title))
				$album -> title = $translate -> _('_MUSIC_UNTITLED_ALBUM');
			$str = $album -> title;
			$str = strtolower($str);
			$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
			$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
			$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
			$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
			$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
			$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
			$str = preg_replace("/(đ)/", "d", $str);
			$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
			$str = preg_replace("/(-+-)/", "-", $str);
			//thay thế 2- thành 1-
			$str = preg_replace("/(^\-+|\-+$)/", "", $str);
			$str = preg_replace("/(-)/", " ", $str);
			$album -> title_url = $str;
			$album -> user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
			$album -> description = trim(htmlspecialchars($values['description']));
			$album -> search = $values['search'];
			$album -> is_download = $values['download'];
			$album -> save();
			$values['album_id'] = $album -> album_id;

			// Assign $album to a Core_Model_Item
			$album = $this -> _album = Engine_Api::_() -> getItem('mp3music_album', $values['album_id']);
			// get file_id list
			$file_ids = array();
			foreach (explode(' ', $values['html5uploadfileids']) as $file_id)
			{
				$file_id = trim($file_id);
				if (!empty($file_id))
					$file_ids[] = $file_id;
			}
			// Attach songs (file_ids) to album
			if (!empty($file_ids))
			{
				foreach ($file_ids as $file_id)
				{
					$user = Engine_Api::_() -> user() -> getViewer();
					$max_songs = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'max_songs');
					if ($max_songs == "")
						$max_songs = 5;
					$song_count = count($album -> getSongs());
					if ($song_count < $max_songs)
					{
						$allow_artist = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.artist', 1);
						if ($allow_artist)
							$album -> addSong($file_id, $values['music_categorie_id'], $values['music_singer_id'], $values['other_singer']);
						else
							$album -> addSong($file_id, $values['music_categorie_id'], $values['music_singer_id'], $values['other_singer'], $values['music_artist_id']);
					}
				}
			}
			// Only create activity feed item if "search" is checked
			if ($album -> search)
			{
				$activity = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activity -> addActivity(Engine_Api::_() -> user() -> getViewer(), $album, 'mp3music_album_new', null, array('count' => count($file_ids)));
				if (null !== $action)
					$activity -> attachActivity($action, $album);
			}
		}
		// Authorizations
		$auth = Engine_Api::_() -> authorization() -> context;
		$prev_allow_comment = $prev_allow_view = false;
		foreach ($this->_roles as $role => $role_label)
		{
			// allow viewers
			if ($values['auth_view'] == $role || $prev_allow_view)
			{
				$auth -> setAllowed($album, $role, 'view', true);
				$prev_allow_view = true;
			}
			else
				$auth -> setAllowed($album, $role, 'view', 0);

			// allow comments
			if ($values['auth_comment'] == $role || $prev_allow_comment)
			{
				$auth -> setAllowed($album, $role, 'comment', true);
				$prev_allow_comment = true;
			}
			else
				$auth -> setAllowed($album, $role, 'comment', 0);
		}

		// Rebuild privacy
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
		foreach ($actionTable->getActionsByObject($album) as $action)
		{
			$actionTable -> resetActivityBindings($action);
		}

		if (!empty($values['art']))
			$album -> setPhoto($this -> art);

		return $album;

	}

}
