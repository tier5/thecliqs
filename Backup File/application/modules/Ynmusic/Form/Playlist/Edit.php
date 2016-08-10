<?php
class Ynmusic_Form_Playlist_Edit extends Engine_Form {
	protected $_playlist;
	
	public function getPlaylist() {
		return $this->_playlist;
	}
	
	public function setPlaylist($playlist) {
		return $this->_playlist = $playlist;
	}
	
	public function init() {
		$view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// Init form
		$title = $view->translate('Edit Playlist %s', $this->getPlaylist());
		$this -> setTitle($title) -> setAttrib('id', 'form-edit-playlist') -> setAttrib('enctype', 'multipart/form-data');
		
		
		$this ->addElement('heading', 'info_privacy_header', array(
			'label' => 'Information and Privacy',
		));
		
		$info_privacy = array();
		// Init name
		$this -> addElement('Text', 'title', array(
			'label' => 'Playlist Title *',
			'maxlength' => '128',
			'required' => true,
			'allowEmpty' => false,
			'filters' => array(
        		'StripTags',
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_StringLength( array('max' => '128')), )
		));
		$this->getElement('title')->setAttrib('required', true);
		$info_privacy[] = 'title';
		
		// Init descriptions
		$this -> addElement('Textarea', 'description', array(
			'label' => 'Playlist Description',
			'maxlength' => '300',
			'filters' => array(
				'StripTags',
				new Engine_Filter_StringLength( array('max' => '300')),
			),
		));
		$info_privacy[] = 'description';
		
		//genres
		 $this -> addElement('Text', 'genre', array(
            'label' => 'Genres',
            'autocomplete' => 'off',
            'description' => 'Can add up to 3 genres. Press \'Enter\' to input data.',
            'order' => '4'
        ));
        $info_privacy[] = 'genre';
		
        $this -> addElement('Hidden', 'genre_ids', array(
            'filters' => array('HtmlEntities'),
            'order' => '5'
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
		
		// Privacy
		$availableLabels = array(
			'everyone' => 'Everyone', 
			'registered' => 'All Registered Members', 
			'owner_network' => 'Friends and Networks', 
			'owner_member_member' => 'Friends of Friends', 
			'owner_member' => 'Friends Only', 
			'owner' => 'Just Me'
		);
		
	    $playlistViewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_playlist', $viewer, 'auth_view');
	    $playlistCommentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_playlist', $viewer, 'auth_comment');
		$playlistDownloadOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmusic_playlist', $viewer, 'auth_download');

	    $playlistViewOptions = array_intersect_key($availableLabels, array_flip($playlistViewOptions));
	    $playlistCommentOptions = array_intersect_key($availableLabels, array_flip($playlistCommentOptions));
		$playlistDownloadOptions = array_intersect_key($availableLabels, array_flip($playlistDownloadOptions));
		
		// View Playlist
	    if( !empty($playlistViewOptions) && count($playlistViewOptions) >= 1 ) {
	      	// Make a hidden field
	      	if(count($playlistViewOptions) == 1) {
	        	$this->addElement('hidden', 'view', array('value' => key($playlistViewOptions)));
	      	// Make select box
	      	} else {
	        	$this->addElement('Select', 'view', array(
	            	'label' => 'Privacy',
	            	'multiOptions' => $playlistViewOptions,
	            	'value' => key($playlistViewOptions),
	        	));
	      	}
			
			$info_privacy[] = 'view';
	    }
	
	    // Comment Playlist
		if( !empty($playlistCommentOptions) && count($playlistCommentOptions) >= 1 ) {
			// Make a hidden field
			if(count($playlistCommentOptions) == 1) {
				$this->addElement('hidden', 'comment', array('value' => key($playlistCommentOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'comment', array(
					'label' => 'Comment Privacy',
					'multiOptions' => $playlistCommentOptions,
					'value' => key($playlistCommentOptions),
				));
			}

			$info_privacy[] = 'comment';
		}

		// Download Playlist
		if( !empty($playlistDownloadOptions) && count($playlistDownloadOptions) >= 1 ) {
			// Make a hidden field
			if(count($playlistDownloadOptions) == 1) {
				$this->addElement('hidden', 'download', array('value' => key($playlistDownloadOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'download', array(
					'label' => 'Downloadable Privacy',
					'multiOptions' => $playlistDownloadOptions,
					'value' => key($playlistDownloadOptions),
				));
			}

			$info_privacy[] = 'download';
		}
		
		// Logo
		$this->addElement('File', 'photo', array(
	      	'label' => 'Photo',
	      	'onchange'=>'javascript:uploadPhoto("photo", "uploadPreviewMain");',
	      	'description' => '<img id="uploadPreviewMain"/>',
	    ));
	    $this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this -> photo -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
		$info_privacy[] = 'photo';
			
		// Cover
		$this->addElement('File', 'cover', array(
	      	'label' => 'Cover',
	      	'onchange'=>'javascript:uploadPhoto("cover", "uploadPreviewCover");',
	      	'description' => '<img id="uploadPreviewCover"/>',
	    ));
	    $this -> cover -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this -> cover -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
		$info_privacy[] = 'cover';
		
		if($this->getPlaylist()) {
			if ($this->getPlaylist()->photo_id) {
				$photoFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->getPlaylist()->photo_id)->current();
				$photoUrl = $photoFile->map();
				if($photoUrl){
					$this -> photo -> setDescription('<img src="'.$photoUrl.'" id="uploadPreviewMain"/>');
				}	
			}	
			if ($this->getPlaylist()->cover_id) {
				$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->getPlaylist()->cover_id)->current();
				$coverPhotoUrl = null;
				if($coverFile){
					$coverPhotoUrl = $coverFile->map();
				}	
				if($coverPhotoUrl){
					$this -> cover -> setDescription('<img src="'.$coverPhotoUrl.'" id="uploadPreviewCover"/>');
				}	
			}
	    }
		
		$this->addDisplayGroup($info_privacy, 'info_privacy', array(
	    ));
		$this->info_privacy->removeDecorator('Fieldset');
		
		if ($this->getPlaylist()->getCountSongs()) {
			// Init manage songs
			$this ->addElement('heading', 'edit_songs_header', array(
				'label' => 'Edit Songs',
				'description' => 'You can drag & drop the songs in this set to reorder them'
			));
			
			$edit_songs = array();
			
			$this -> addElement('Dummy', 'manage_songs', array('decorators' => array( 
				array(
					'ViewScript',
					array(
						'viewScript' => '_manage_songs_playlist.tpl',
						'class' => 'form element',
						'playlist' => $this -> getPlaylist(),
						'noedit' => true
					)
				)), 
			));
			
			$edit_songs[] = 'manage_songs';
			$this->addDisplayGroup($edit_songs, 'edit_songs', array(
		    ));
			$this->edit_songs->removeDecorator('Fieldset');
		}
		
		// Init submit
		$this -> addElement('Button', 'btn_submit', array(
			'label' => 'Save',
			'type' => 'submit',
			'onclick' => 'return validateFormCreate("photo", "cover")',
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
}
