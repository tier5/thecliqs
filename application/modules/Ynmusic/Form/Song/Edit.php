<?php
class Ynmusic_Form_Song_Edit extends Engine_Form
{
	protected $_song;
	
	public function getSong() {
		return $this->_song;
	}
	
	public function setSong($song) {
		return $this->_song = $song;
	}
	
	public function init()
	{
		$view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Init form
		$this -> setAttrib('id', 'form-edit-song'.'-'.$this->_song->getIdentity()) -> setAttrib('enctype', 'multipart/form-data') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));
		
		
		// Init name
		$this -> addElement('Text', 'title'.'_'.$this->_song->getIdentity(), array(
			'label' => 'Title',
			'filters' => array(
        		'StripTags',
				//new Engine_Filter_HtmlSpecialChars(),
				new Engine_Filter_StringLength( array('max' => '128')), )
		));
		
		// Init descriptions
		$this -> addElement('Textarea', 'description'.'_'.$this->_song->getIdentity(), array(
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
		$genreName = 'genre'.'_'.$this->_song->getIdentity();
		 $this -> addElement('Text', $genreName, array(
            'label' => 'Genres',
            'autocomplete' => 'off',
            'description' => 'Can add up to 3 genres. Press \'Enter\' to input data',
            'order' => '2'
        ));
        
		$genreIdsName = 'genre_ids'.'_'.$this->_song->getIdentity();
        $this -> addElement('Hidden', $genreIdsName, array(
            'filters' => array('HtmlEntities'),
            'order' => '3'
        ));
		$this->$genreName->setAttrib('class', 'genre_ids-wrapper');
		$this->$genreName->getDecorator("Description")->setOption("placement", "append");
		Engine_Form::addDefaultDecorators($this -> $genreIdsName);
		
		
		//tags
		$tagsName = 'tags'.'_'.$this->_song->getIdentity();
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
		 $this -> addElement('Text', 'artist'.'_'.$this->_song->getIdentity(), array(
            'label' => 'Artist',
            'autocomplete' => 'off',
            'description' => 'Press \'Enter\' to input data',
            'order' => '4'
        ));
		$this->getElement('artist'.'_'.$this->_song->getIdentity())->getDecorator("Description")->setOption("placement", "append");
        
		$artistIds = 'artist_ids'.'_'.$this->_song->getIdentity();
        $this -> addElement('Hidden', $artistIds, array(
            'filters' => array('HtmlEntities'),
            'order' => '5'
        ));
		
        Engine_Form::addDefaultDecorators($this -> $artistIds);
		
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
	        $this->addElement('hidden', 'auth_view'.'_'.$this->_song->getIdentity(), array('value' => key($songViewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_view'.'_'.$this->_song->getIdentity(), array(
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
	        $this->addElement('hidden', 'auth_comment'.'_'.$this->_song->getIdentity(), array('value' => key($songCommentOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_comment'.'_'.$this->_song->getIdentity(), array(
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
				$this->addElement('hidden', 'auth_download'.'_'.$this->_song->getIdentity(), array('value' => key($songDownloadOptions)));
				// Make select box
			} else {
				$this->addElement('Select', 'auth_download'.'_'.$this->_song->getIdentity(), array(
					'label' => 'Downloadable Privacy',
					'multiOptions' => $songDownloadOptions,
					'value' => key($songDownloadOptions),
				));
			}
		}
		
		// Downloadable
		$this -> addElement('Checkbox', 'downloadable'.'_'.$this->_song->getIdentity(), array(
			'label' => 'Downloadable',
		));
		
		//Move to album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($this->_song->isOwner($viewer)) {
			$albumMoveName = 'album_id'.'_'.$this->_song->getIdentity();
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
		
		// Init manage upload photo & cover
		$this -> addElement('Dummy', 'upload_photo', array('decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_upload_photo.tpl',
				'class' => 'form element',
				'item' => $this ->_song,
			)
		)), ));
		
		
		// Init submit
		$this -> addElement('Button', 'btn_submit', array(
			'label' => 'Save',
			'type' => 'button',
			'onclick' => "return submitForm('".$this->_song->getIdentity()."')",
			'ignore' => true,
			'decorators' => array(
		        'ViewHelper',
		      ),
		));
		
		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'ignore' => true,
	      'onclick' => "return clearForm('".$this->_song->getIdentity()."')",
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
