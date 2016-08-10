<?php
class Ynlistings_Form_Edit extends Engine_Form
{
	protected $_category;
	protected $_formArgs;
	protected $_formArgsParent;
	protected $_item;
	protected $_theme;
    protected $_canSelectTheme;
	
	public function getTheme()
	{
	return $this -> _category;
	}
	
	public function setTheme($theme)
	{
	$this -> _theme = $theme;
	} 
	
	public function getItem()
	{
	    return $this->_item;
	}
	
	public function setItem(Core_Model_Item_Abstract $item)
	{
	    $this->_item = $item;
	    return $this;
	}
	
	public function getCategory()
	{
	return $this -> _category;
	}
	
	public function setCategory($category)
	{
	$this -> _category = $category;
	} 
	
	public function getFormArgs()
	{
	return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
	$this -> _formArgs = $formArgs;
	} 
	
	public function getFormArgsParent()
	{
	return $this -> _formArgsParent;
	}
	
	public function setFormArgsParent($formArgsParent)
	{
	$this -> _formArgsParent = $formArgsParent;
	} 
	
    public function getCanSelectTheme()
    {
    return $this -> _canSelectTheme;
    }
    
    public function setCanSelectTheme($canSelectTheme)
    {
        $this -> _canSelectTheme = $canSelectTheme;
    }	 
  public function init()
  {
	 
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Edit Listing');
	
	 $this->addElement('Select', 'category_id', array(
	  'required'  => true,
      'allowEmpty'=> false,
      'label' => 'Category',
    ));
	if($this -> _category){
		$this -> addElement('dummy', 'theme', array(
				'label'     => 'Select Themes',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_post_listings_themes.tpl',
						'category' =>  $this -> _category,
						'theme' => $this ->_theme,
						'canSelectTheme' => $this->_canSelectTheme,
						'class' => 'form element',
					)
				)), 
		));  
    }
    $this->addElement('Text', 'title', array(
      'label' => 'Listing Title',
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

    $this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
	
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	
	$this->addElement('TinyMce', 'short_description', array(
      'label' => 'Short Description',
      'editorOptions' => array(
         'mode'=> 'exact',
      	 'elements'=>"short_description,description,about_us",
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
      'validators' => array(
	        array('StringLength', false, array(1, 400)),
       ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
	
    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'editorOptions' => array(
         'mode'=> 'exact',
      	 'elements'=>"short_description,description,about_us",
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
	
   $this->addElement('TinyMce', 'about_us', array(
      'label' => 'About Us',
      'editorOptions' => array(
         'mode'=> 'exact',
      	 'elements'=>"short_description,description,about_us",
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
	
	$this->addElement('File', 'photo', array(
      'label' => 'Main Photo'
    ));
	$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	$this -> addElement('Text', 'to', array(
	'label'=>'Main Video', 
	'description' => 'Please choose one video.',
	'autocomplete' => 'off'));
	Engine_Form::addDefaultDecorators($this -> to);

	// Init to Values
	$this -> addElement('Hidden', 'toValues', array(
	    'label' => 'Main Video',
		'style' => 'margin-top:-5px',
		'order' => 8,
		'validators' => array('NotEmpty'),
		'filters' => array('HtmlEntities'),
	));
	Engine_Form::addDefaultDecorators($this -> toValues);
	
	$this->addElement('Float', 'price', array(
      'label' => 'Price',
      'required' => true,
      'allowEmpty' => false,
    ));
	
    $this->addElement('Select', 'currency', array(
      'label' => 'Currency',
      'value' => 'USD',
    ));
    $this->getElement('currency')->getDecorator('Description')->setOption('placement', 'APPEND');
	
	$this -> addElement('Dummy', 'location_map', array(
			'label' => 'Location',
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_location_search.tpl',
					'class' => 'form element',
				)
			)), 
		));
		
		$this -> addElement('hidden', 'location_address', array(
			'value' => '0',
			'order' => '97'
		));

		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
	
	// Add subforms
    if( !$this->_item ) {
      $customFields = new Ynlistings_Form_Custom_Fields($this -> _formArgs);
    } else {
      $customFields = new Ynlistings_Form_Custom_Fields(array_merge(array(
        'item' => $this->_item,
      ),$this -> _formArgs));
    }
    $this->addSubForms(array(
      'fields' => $customFields
    ));
	
	// Add subformsParent
	if(!empty($this -> _formArgsParent))
	{
	    if( !$this->_item ) {
	      $customFieldsParent = new Ynlistings_Form_Custom_FieldsParent($this -> _formArgsParent);
	    } else {
	       $customFieldsParent = new Ynlistings_Form_Custom_FieldsParent(array_merge(array(
	        'item' => $this->_item,
	      ),$this -> _formArgsParent));
	    }
	    $this->addSubForms(array(
	      'fieldsParent' => $customFieldsParent
	    ));
	}
	
	$this->addElement('Radio', 'is_end', array(
      'label' => 'End date',
      'multiOptions' => array(
        '0' => 'No end date.',
        '1' => 'End this listing on a specific day.',
      ),
      'value' => '0',
    ));
	
    $end = new Engine_Form_Element_CalendarDateTime('end_date');
    $this->addElement($end);
		
    $this->addElement('Radio', 'search', array(
      'label' => 'Include in search results?',
      'multiOptions' => array(
        '1' => 'Yes, include in search results.',
        '0' => 'No, hide from search results.',
      ),
      'value' => '1',
    ));


    // Privacy
    $availableLabels = array(
      'everyone' => 'Everyone',
      'registered' => 'All Registered',
      'network' => 'My Network',
      'owner_member' => 'My Friends',
      'owner' => 'Only Me',
    );

    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'view_listings');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'view', array(
            'label' => 'View Privacy',
            'description' => 'Who may view this listing?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Post activities
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'comment', array(
            'label' => 'Post Activities Privacy',
            'description' => 'Who may post activities on this listing?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
	// Share
    $shareOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'sharing');
    $shareOptions = array_intersect_key($availableLabels, array_flip($shareOptions));

    if( !empty($shareOptions) && count($shareOptions) >= 1 ) {
      // Make a hidden field
      if(count($shareOptions) == 1) {
        $this->addElement('hidden', 'share', array('value' => key($shareOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'share', array(
            'label' => 'Share Privacy',
            'description' => 'Who may share this listing',
            'multiOptions' => $shareOptions,
            'value' => key($shareOptions),
        ));
        $this->share->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
    //Printing
    $printingOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'printing');
    $printingOptions = array_intersect_key($availableLabels, array_flip($printingOptions));

    if( !empty($printingOptions) && count($printingOptions) >= 1 ) {
      // Make a hidden field
      if(count($printingOptions) == 1) {
        $this->addElement('hidden', 'print', array('value' => key($printingOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'print', array(
            'label' => 'Print Privacy',
            'description' => 'Who may print this listing?',
            'multiOptions' => $printingOptions,
            'value' => key($printingOptions),
        ));
        $this->print->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    
	//Photo
    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'add_photos');
    $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));

    if( !empty($photoOptions) && count($photoOptions) >= 1 ) {
      // Make a hidden field
      if(count($photoOptions) == 1) {
        $this->addElement('hidden', 'upload_photos', array('value' => key($photoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'upload_photos', array(
            'label' => 'Photo Creation',
            'description' => 'Who may add photos to this listing?',
            'multiOptions' => $photoOptions,
            'value' => key($photoOptions),
        ));
        $this->upload_photos->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
    //Video
    $videoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'add_videos');
    $videoOptions = array_intersect_key($availableLabels, array_flip($videoOptions));

    if( !empty($videoOptions) && count($videoOptions) >= 1 ) {
      // Make a hidden field
      if(count($videoOptions) == 1) {
        $this->addElement('hidden', 'upload_videos', array('value' => key($videoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'upload_videos', array(
            'label' => 'Video Creation',
            'description' => 'Who may add videos to this listing?',
            'multiOptions' => $videoOptions,
            'value' => key($videoOptions),
        ));
        $this->upload_videos->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
	//Discussion
    $discussionOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'add_discussions');
    $discussionOptions = array_intersect_key($availableLabels, array_flip($discussionOptions));
	
    if( !empty($discussionOptions) && count($discussionOptions) >= 1 ) {
      // Make a hidden field
      if(count($discussionOptions) == 1) {
        $this->addElement('hidden', 'discussion', array('value' => key($discussionOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'discussion', array(
            'label' => 'Discussion Creation',
            'description' => 'Who may add discussions to this listing?',
            'multiOptions' => $discussionOptions,
            'value' => key($discussionOptions),
        ));
        $this->discussion->getDecorator('Description')->setOption('placement', 'append');
      }
    }

	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Publish Listing',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
	 // Buttons
    $this->addElement('Button', 'edit_button', array(
      'value' => 'edit_button',
      'label' => 'Edit',
      'onclick' => 'removeSubmit()',
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

    $this->addDisplayGroup(array('submit_button', 'edit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
