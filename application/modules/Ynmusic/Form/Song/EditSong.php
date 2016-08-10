<?php
class Ynmusic_Form_Song_EditSong extends Engine_Form
{
	protected $_song;
	
	public function getSong() {
		return $this->_song;
	}
	
	public function setSong($song) {
		return $this->_song = $song;
	}
	
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
		$view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Init form
		$this -> setAttrib('id', 'form-edit-song') -> setAttrib('enctype', 'multipart/form-data') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));
		
		
		// Init name
		$this -> addElement('Text', 'title', array(
			'label' => 'Title',
			'filters' => array(
        		'StripTags',
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_StringLength( array('max' => '128')), )
		));
		
		// Init descriptions
		$this -> addElement('Textarea', 'description', array(
			'label' => 'Song Description',
			'maxlength' => '3000',
			'filters' => array(
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_Censor(),
				new Engine_Filter_StringLength( array('max' => '3000')),
				new Engine_Filter_EnableLinks(),
			),
		));
		
		//genres
		$genreName = 'genre';
		 $this -> addElement('Text', $genreName, array(
            'label' => 'Genres',
            'autocomplete' => 'off',
            'description' => 'Can add up to 3 genres. Press \'Enter\' to input data.',
            'order' => '2'
        ));
        
		$genreIdsName = 'genre_ids';
        $this -> addElement('Hidden', $genreIdsName, array(
            'filters' => array('HtmlEntities'),
            'order' => '3'
        ));
		$this->$genreName->getDecorator("Description")->setOption("placement", "append");
		Engine_Form::addDefaultDecorators($this -> $genreIdsName);
		
		
		//tags
		$tagsName = 'tags';
	    $this->addElement('Text', $tagsName,array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
	    $this->$tagsName->getDecorator("Description")->setOption("placement", "append");
		
		//artists
		 $this -> addElement('Text', 'artist', array(
            'label' => 'Artist',
            'description' => 'Press \'Enter\' to input data',
            'autocomplete' => 'off',
            'order' => '4'
        ));
        $this->artist->getDecorator("Description")->setOption("placement", "append");
		
		$artistIds = 'artist_ids';
        $this -> addElement('Hidden', $artistIds, array(
            'filters' => array('HtmlEntities'),
            'order' => '5'
        ));
		
        Engine_Form::addDefaultDecorators($this -> $artistIds);
		
		// Downloadable
		$this -> addElement('Checkbox', 'downloadable', array(
			'label' => 'Downloadable',
		));
		
		//Move to album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($this->_song->isOwner($viewer)) {
			$albumMoveName = 'album_id';
			$this->addElement('Select', $albumMoveName , array(
			      'label' => 'Move to Album',
			      'multiOptions' => array(
				  		0 => 'None'
				  )
		    ));
			
			$albumTable = Engine_Api::_() -> getDbTable('albums', 'ynmusic');
			$albums = $albumTable -> getAblumsByUser($viewer);
			foreach ($albums as $album) {
				if ($album->isEditable() && $album->canAddSongs()) $this -> $albumMoveName -> addMultiOption($album -> getIdentity(), $album -> getTitle());
			}
			
			$this -> $albumMoveName -> setValue($this->_song->album_id);
		}
		// Privacy
		
		  $availableLabels = array(
			'everyone' => 'Everyone', 
			'registered' => 'All Registered Members', 
			'owner_network' => 'Friends and Networks', 
			'owner_member_member' => 'Friends of Friends', 
			'owner_member' => 'Friends Only', 
			'owner' => 'Just Me'
		);
		
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
	        $this->addElement('hidden', 'auth_view', array('value' => key($songViewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_view', array(
	            'label' => 'Privacy',
	            'multiOptions' => $songViewOptions,
	            'value' => key($songViewOptions),
	        ));
	      }
	    }
	
	    // Comment Album
	    if( !empty($songCommentOptions) && count($songCommentOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($songCommentOptions) == 1) {
	        $this->addElement('hidden', 'auth_comment', array('value' => key($songCommentOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_comment', array(
	            'label' => 'Comment Privacy',
	            'multiOptions' => $songCommentOptions,
	            'value' => key($songCommentOptions),
	        ));
	      }
	    }

		// Download Album
		if( !empty($songDownloadOptions) && count($songDownloadOptions) >= 1 ) {
			// Make a hidden field
			if(count($songDownloadOptions) == 1) {
				$this->addElement('hidden', 'auth_download', array('value' => key($songDownloadOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'auth_download', array(
					'label' => 'Downloadable Privacy',
					'multiOptions' => $songDownloadOptions,
					'value' => key($songDownloadOptions),
				));
			}
		}
		
		// Logo
		$this->addElement('File', 'photo', array(
	      	'label' => 'Photo',
	      	'onchange'=>'javascript:uploadPhoto("photo", "uploadPreviewMain");',
	      	'description' => '<img id="uploadPreviewMain"/>',
	    ));
	    $this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this -> photo -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
			
		// Cover
		$this->addElement('File', 'cover', array(
	      	'label' => 'Cover',
	      	'onchange'=>'javascript:uploadPhoto("cover", "uploadPreviewCover");',
	      	'description' => '<img id="uploadPreviewCover"/>',
	    ));
	    $this -> cover -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this -> cover -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
		
		if($this->getSong()) {
			if ($this->getSong()->photo_id) {
				$photoFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->getSong()->photo_id)->current();
				$photoUrl = $photoFile->map();
				if($photoUrl){
					$this -> photo -> setDescription('<img src="'.$photoUrl.'" id="uploadPreviewMain"/>');
				}	
			}	
			if ($this->getSong()->cover_id) {
				$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->getSong()->cover_id)->current();
				$coverPhotoUrl = null;
				if($coverFile){
					$coverPhotoUrl = $coverFile->map();
				}	
				if($coverPhotoUrl){
					$this -> cover -> setDescription('<img src="'.$coverPhotoUrl.'" id="uploadPreviewCover"/>');
				}	
			}
	    }
		// Init submit
		$this -> addElement('Button', 'btn_submit', array(
			'label' => 'Save',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array(
		        'ViewHelper',
		      ),
		));
		
		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'Cancel',
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
