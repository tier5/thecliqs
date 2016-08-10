<?php
class Ynmusic_Form_Admin_Artist_Create extends Engine_Form {
	
	  protected $_artist;
	
	  public function getArtist()
	  {
	     return $this -> _artist;
	  }
	  public function setArtist($artist)
	  {
	     $this -> _artist = $artist;
	  } 
	
	public function init() {
		$this 
		  -> setTitle('Add New Artist')
          -> setAttrib('class', 'global_form');
          
		 $this->addElement('Text', 'title', array(
		      'label' => 'Artist Name',
		      'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
			        array('NotEmpty', true),
			        array('StringLength', false, array(1, 64)),
		      ),
		      'filters' => array(
			        'StripTags',
			        new Engine_Filter_Censor(),
		      ),
	    ));
	    
		$this->addElement('Textarea', 'short_description', array(
		      'label' => 'Short Description',
		      'description' => 'Maximum 1000 characters',
		      'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
			        array('NotEmpty', true),
			        array('StringLength', false, array(1, 1000)),
		      ),
		      'filters' => array(
			        'StripTags',
			        new Engine_Filter_Censor(),
		      ),
	    ));
		$this->getElement('short_description')->getDecorator('Description')->setOption('placement', 'APPEND');
		
		$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
		
		$this->addElement('TinyMce', 'description', array(
	      'label' => 'Description',
	      'editorOptions' => array(
	         'mode'=> 'exact',
	      	 'elements'=>"description",
	         'bbcode' => 1,
	          'html'   => 1,
	          'theme_advanced_buttons1' => array(
	              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
	              'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
	          ),
	          'theme_advanced_buttons2' => array(
	              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
	              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
	              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
	          ),
	        ),
	      'required'   => true,
	      'allowEmpty' => false,
	      'filters' => array(
	        new Engine_Filter_Censor(),
	        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
	    ));
		  
		$locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
		
		$this -> addElement('Select', 'country', array(
			'label' => 'Country',
		));
		
		foreach($territories as $territory) {
			$this -> country -> addMultiOption($territory, $territory);
		}
		
		$this -> addElement('Text', 'to', array('label' => 'Genre', 'autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);
		
		
		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'style' => 'margin-top:-5px',
			'order' => 5,
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);
		
		
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
		
		if( get_class($this) == 'Ynmusic_Form_Admin_Artist_Edit' && $this->_artist ) {
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
				$coverPhotoUrl = null;
				if($coverFile){
					$coverPhotoUrl = $coverFile->map();
				}	
				if($coverPhotoUrl){
					$this -> cover -> setDescription('<img src="'.$coverPhotoUrl.'" id="uploadPreviewCover"/>');
				}	
			}
	    }
		
		// Buttons
	    $this->addElement('Button', 'submit_btn', array(
	      'label' => 'Save',
	      'onclick' => 'removeSubmit()',
	      'type' => 'submit',
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

		$this -> addDisplayGroup(array(
			'submit_btn',
			'cancel'
		), 'buttons');
	}

}
