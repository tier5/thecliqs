<?php
class Ynmusic_Form_Create extends Engine_Form
{
	protected $_album;
	
	public function getAlbum() {
		return $this->_album;
	}
	
	public function setAlbum($album) {
		return $this->_album = $album;
	}
	
	public function init()
	{
		$view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// Init form
		$this -> setAttrib('id', 'form-upload-music') -> setAttrib('name', 'album_create') -> setAttrib('enctype', 'multipart/form-data');
		
		//Upload songs
		$this ->addElement('heading', 'info_privacy_header', array(
			'label' => 'Information and Privacy',
		));
		
		$info_privacy = array();
		//Album
		$this->addElement('Select', 'album_id', array(
		      'label' => 'Album',
	    ));
		$info_privacy[] = 'album_id';
		//add value for upload song only & create new album
		if(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynmusic_song', null, 'create')->checkRequire()) {
			$this -> album_id -> addMultiOption('none', $view -> translate('None'));
		}
		
		if(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynmusic_album', null, 'create')->checkRequire()) {
			$this -> album_id -> addMultiOption('create', $view -> translate('Create New Album'));
		}
		
		// Init name
		$this -> addElement('Text', 'title', array(
			'label' => 'Album Name',
			'maxlength' => '128',
			'filters' => array(
        		'StripTags',
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_StringLength( array('max' => '128')), )
		));
		$info_privacy[] = 'title';
		
		// Init descriptions
		$this -> addElement('Textarea', 'description', array(
			'label' => 'Album Description',
			'maxlength' => '3000',
			'filters' => array(
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_Censor(),
				new Engine_Filter_StringLength( array('max' => '3000')),
				new Engine_Filter_EnableLinks(),
			),
		));
		$info_privacy[] = 'description';
		
		//genres
		 $this -> addElement('Text', 'genre', array(
            'label' => 'Genres',
            'autocomplete' => 'off',
            'description' => 'Can add up to 3 genres. Press \'Enter\' to input data',
            'order' => '2'
        ));
        $info_privacy[] = 'genre';
		
        $this -> addElement('Hidden', 'genre_ids', array(
            'filters' => array('HtmlEntities'),
            'order' => '3'
        ));
		$this->genre->getDecorator("Description")->setOption("placement", "append");
		Engine_Form::addDefaultDecorators($this -> genre_ids);
		$info_privacy[] = 'genre_ids';
		
		//tags
	    $this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
	    $this->tags->getDecorator("Description")->setOption("placement", "append");
		$info_privacy[] = 'tags';

		// Downloadable
		$this -> addElement('Checkbox', 'downloadable', array(
			'label' => 'Downloadable',
		));
		$info_privacy[] = 'downloadable';
		
		//artists
		 $this -> addElement('Text', 'artist', array(
            'label' => 'Artist',
            'description' => 'Press \'Enter\' to input data',
            'autocomplete' => 'off',
            'order' => '4'
        ));
		$this->artist->getDecorator("Description")->setOption("placement", "append");
        $info_privacy[] = 'artist';
		
        $this -> addElement('Hidden', 'artist_ids', array(
            'filters' => array('HtmlEntities'),
            'order' => '5'
        ));
		$info_privacy[] = 'artist_ids';
		
        Engine_Form::addDefaultDecorators($this -> artist_ids);
		
		// released_date 
		$released_date = new Engine_Form_Element_CalendarDateTime('released_date');
		$released_date -> setLabel("Released Date");
		$released_date -> setAllowEmpty(true);
		$this -> addElement($released_date);
		$info_privacy[] = 'released_date';
		
		// Logo
		$this->addElement('File', 'photo', array(
	      'label' => 'Photo',
	      'onchange'=>'javascript:uploadPhoto("photo", "uploadPreviewMain");',
	      'description' => '<img id="uploadPreviewMain"/>',
	    ));
		$this -> photo -> setAttrib('accept', 'image/*');
	    $this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this -> photo -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
		$info_privacy[] = 'photo';
			
		// Cover
		$this->addElement('File', 'cover', array(
	      'label' => 'Cover',
	      'onchange'=>'javascript:uploadPhoto("cover", "uploadPreviewCover");',
	      'description' => '<img id="uploadPreviewCover"/>',
	      
	    ));
		$this -> cover -> setAttrib('accept', 'image/*');
	    $this -> cover -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this -> cover -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
		$info_privacy[] = 'cover';
		
		if( $this->_album ) {
			if ($this->_artist->photo_id)
			{
				$photoFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->_artist->photo_id)->current();
				$photoUrl = $photoFile->map();
				if($photoUrl){
					$this -> photo -> setDescription('<img src="'.$photoUrl.'" id="uploadPreviewMain"/>');
				}	
			}	
			if ($this->_artist->cover_id)
			{
				$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->_artist->cover_id)->current();
				$coverPhotoUrl= null;
				if($coverFile) {
					$coverPhotoUrl = $coverFile->map();
				}	
				if($coverPhotoUrl){
					$this -> cover -> setDescription('<img src="'.$coverPhotoUrl.'" id="uploadPreviewCover"/>');
				}	
			}
	    }
		
		$availableLabels = array(
			'everyone' => 'Everyone', 
			'registered' => 'All Registered Members', 
			'owner_network' => 'Friends and Networks', 
			'owner_member_member' => 'Friends of Friends', 
			'owner_member' => 'Friends Only', 
			'owner' => 'Just Me'
		);
		  
		//-------------AUTH FOR ALBUM--------------
		
		// Privacy
	    $albumViewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_album', $viewer, 'auth_view');
	    $albumCommentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_album', $viewer, 'auth_comment');
		$albumDownloadOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_album', $viewer, 'auth_download');
		  
	    $albumViewOptions = array_intersect_key($availableLabels, array_flip($albumViewOptions));
	    $albumCommentOptions = array_intersect_key($availableLabels, array_flip($albumCommentOptions));
		$albumDownloadOptions = array_intersect_key($availableLabels, array_flip($albumDownloadOptions));
		
		// View Album
	    if( !empty($albumViewOptions) && count($albumViewOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($albumViewOptions) == 1) {
	        $this->addElement('hidden', 'album_auth_view', array('value' => key($albumViewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'album_auth_view', array(
	            'label' => 'Privacy',
	            'multiOptions' => $albumViewOptions,
	            'value' => key($albumViewOptions),
	        ));
	      }
		  
		  $info_privacy[] = 'album_auth_view';
	    }
	
	    // Comment Album
	    if( !empty($albumCommentOptions) && count($albumCommentOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($albumCommentOptions) == 1) {
	        $this->addElement('hidden', 'album_auth_comment', array('value' => key($albumCommentOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'album_auth_comment', array(
	            'label' => 'Comment Privacy',
	            'multiOptions' => $albumCommentOptions,
	            'value' => key($albumCommentOptions),
	        ));
	      }
		  
		  $info_privacy[] = 'album_auth_comment';
	    }

		// Download Album
		if( !empty($albumDownloadOptions) && count($albumDownloadOptions) >= 1 ) {
			// Make a hidden field
			if(count($albumDownloadOptions) == 1) {
				$this->addElement('hidden', 'album_auth_download', array('value' => key($albumDownloadOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'album_auth_download', array(
					'label' => 'Downloadable Privacy',
					'multiOptions' => $albumDownloadOptions,
					'value' => key($albumDownloadOptions),
				));
			}

			$info_privacy[] = 'album_auth_download';
		}
		
		//-------------AUTH FOR SONG--------------
		
		// Privacy
	    $songViewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_song', $viewer, 'auth_view');
	    $songCommentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_song', $viewer, 'auth_comment');
		$songDownloadOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_song', $viewer, 'auth_download');
		  
	    $songViewOptions = array_intersect_key($availableLabels, array_flip($songViewOptions));
	    $songCommentOptions = array_intersect_key($availableLabels, array_flip($songCommentOptions));
		$songDownloadOptions = array_intersect_key($availableLabels, array_flip($songDownloadOptions));
		
		// View Album
	    if( !empty($songViewOptions) && count($songViewOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($songViewOptions) == 1) {
	        $this->addElement('hidden', 'song_auth_view', array('value' => key($songViewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'song_auth_view', array(
	            'label' => 'Privacy',
	            'multiOptions' => $songViewOptions,
	            'value' => key($songViewOptions),
	        ));
	      }
		  
		  $info_privacy[] = 'song_auth_view';
	    }
	
	    // Comment Album
	    if( !empty($songCommentOptions) && count($songCommentOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($songCommentOptions) == 1) {
	        $this->addElement('hidden', 'song_auth_comment', array('value' => key($songCommentOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'song_auth_comment', array(
	            'label' => 'Comment Privacy',
	            'multiOptions' => $songCommentOptions,
	            'value' => key($songCommentOptions),
	        ));
	      }
		  
		  $info_privacy[] = 'song_auth_comment';
	    }

		// Download Album
		if( !empty($songDownloadOptions) && count($songDownloadOptions) >= 1 ) {
			// Make a hidden field
			if(count($songDownloadOptions) == 1) {
				$this->addElement('hidden', 'song_auth_download', array('value' => key($songDownloadOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'song_auth_download', array(
					'label' => 'Downloadable Privacy',
					'multiOptions' => $songDownloadOptions,
					'value' => key($songDownloadOptions),
				));
			}

			$info_privacy[] = 'song_auth_download';
		}
	    
		$this->addDisplayGroup($info_privacy, 'info_privacy', array(
	    ));
		$this->info_privacy->removeDecorator('Fieldset');
		
		//Upload songs
		$this ->addElement('heading', 'upload_song_header', array(
			'label' => 'Upload songs',
		));
		
		$upload_song = array();
		// Init file uploader
		$this -> addElement('Dummy', 'html5_upload', array('decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_Html5Upload.tpl',
				'class' => 'form element',
			)
		)), ));
		$upload_song[] = 'html5_upload';
		// Init hidden file IDs
	    $this -> addElement('Hidden', 'html5uploadfileids', array('value' => '', 'order' => 300));
		$upload_song[] = 'html5uploadfileids';
		
		$this->addDisplayGroup($upload_song, 'upload_song', array(
	    ));
		$this->upload_song->removeDecorator('Fieldset');
		
		require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/Soundcloud.php';	
		$setting = Engine_Api::_()->getApi('settings', 'core');
		$cliendId = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientid', "");
		$cliendSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientsecret', "");
		
		
		if(!empty($cliendId) && !empty($cliendSecret)){
			$canSoundCloud = true;
		} else {
			$canSoundCloud = false;
		}
		
		if($canSoundCloud)
		{
			//Upload songs
			$this ->addElement('heading', 'upload_song_soundcloud', array(
				'label' => 'Upload songs from Soundcloud',
			));
			
			$upload_soundcloud = array();
			// Init hidden file songcloud_count
		    $this -> addElement('Hidden', 'songcloud_count', array('value' => '', 'order' => 301));
			$upload_soundcloud[] = 'songcloud_count';
			// add more soundcloud
			$this -> addElement('Button', 'add_more_soundcloud', array(
				'label' => 'Add Song',
				'type' => 'button',
			));
			$upload_soundcloud[] = 'add_more_soundcloud';
			
			$this->addDisplayGroup($upload_soundcloud, 'upload_soundcloud', array(
	    	));
			$this->upload_soundcloud->removeDecorator('Fieldset');
		}
		
		// Init submit
		$this -> addElement('Button', 'btn_submit', array(
			'label' => 'Save',
			'type' => 'submit',
			'onclick' => 'return validateFormCreate(event)',
			'ignore' => true,
			'decorators' => array(
		        'ViewHelper',
		      ),
		));
		
		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => ' or ',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
	
	    $this->addDisplayGroup(array('btn_submit', 'cancel'), 'buttons', array(
	      'decorators' => array(
	        'FormElements',
	        'DivDivDivWrapper',
	      ),
	    ));
	}

	public function clearUploads()
	{
		$this -> getElement('fancyuploadfileids') -> setValue('');
	}


}
