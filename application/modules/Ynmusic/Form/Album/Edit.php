<?php
class Ynmusic_Form_Album_Edit extends Engine_Form
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
		$title = $view->translate('Edit Album %s', $this->getAlbum());
		$this -> setTitle($title) -> setAttrib('id', 'form-edit-album') -> setAttrib('name', 'album_edit') -> setAttrib('enctype', 'multipart/form-data');
		
		$this ->addElement('heading', 'info_privacy_header', array(
			'label' => 'Information and Privacy',
		));
		
		$info_privacy = array();
		
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
		
        Engine_Form::addDefaultDecorators($this -> artist_ids);
		$info_privacy[] = 'artist_ids';
		
		// released_date 
		$released_date = new Engine_Form_Element_CalendarDateTime('released_date');
		$released_date -> setLabel("Released Date");
		$released_date -> setAllowEmpty(true);
		$this -> addElement($released_date);
		$info_privacy[] = 'released_date';
		
		
		// Privacy
		$availableLabels = array(
			'everyone' => 'Everyone', 
			'registered' => 'All Registered Members', 
			'owner_network' => 'Friends and Networks', 
			'owner_member_member' => 'Friends of Friends', 
			'owner_member' => 'Friends Only', 
			'owner' => 'Just Me'
		);
		
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
	        $this->addElement('hidden', 'auth_view', array('value' => key($albumViewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_view', array(
	            'label' => 'Privacy',
	            'multiOptions' => $albumViewOptions,
	            'value' => key($albumViewOptions),
	        ));
	      }
		  $info_privacy[] = 'auth_view';
	    }
	
	    // Comment Album
	    if( !empty($albumCommentOptions) && count($albumCommentOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($albumCommentOptions) == 1) {
	        $this->addElement('hidden', 'auth_comment', array('value' => key($albumCommentOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_comment', array(
	            'label' => 'Comment Privacy',
	            'multiOptions' => $albumCommentOptions,
	            'value' => key($albumCommentOptions),
	        ));
	      }
		  $info_privacy[] = 'auth_comment';
	    }

		// Comment Album
		if( !empty($albumDownloadOptions) && count($albumDownloadOptions) >= 1 ) {
			// Make a hidden field
			if(count($albumDownloadOptions) == 1) {
				$this->addElement('hidden', 'auth_download', array('value' => key($albumDownloadOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'auth_download', array(
					'label' => 'Downloadable Privacy',
					'multiOptions' => $albumDownloadOptions,
					'value' => key($albumDownloadOptions),
				));
			}
			$info_privacy[] = 'auth_download';
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
		
		if( $this->_album ) {
			if ($this->_album->photo_id)
			{
				$photoFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->_album->photo_id)->current();
				$photoUrl = $photoFile->map();
				if($photoUrl){
					$this -> photo -> setDescription('<img src="'.$photoUrl.'" id="uploadPreviewMain"/>');
				}	
			}	
			if ($this->_album->cover_id)
			{
				$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->_album->cover_id)->current();
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
		
		if ($this->_album->getCountSongs()) {
			// Init manage songs
			$this ->addElement('heading', 'edit_songs_header', array(
				'label' => 'Edit Songs',
				'description' => 'You can drag & drop the songs in this set to reorder them'
			));
			
			$edit_songs = array();
			
			$this -> addElement('Dummy', 'manage_songs', array('decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_manage_songs.tpl',
					'class' => 'form element',
					'album' => $this ->_album,
				)
			)), ));
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
