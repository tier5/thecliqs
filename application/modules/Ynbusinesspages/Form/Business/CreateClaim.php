<?php
class Ynbusinesspages_Form_Business_CreateClaim extends Engine_Form
{
	protected $_formArgs;
	
	public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Create New Business');
	
	//Themes
	$this -> addElement('dummy', 'theme', array(
			'label'     => 'Select Themes',
	        'required'  => true,
	        'allowEmpty'=> false,
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_post_claim_business_themes.tpl',
					'class' => 'form element',
				)
			)), 
	));  
	
	//Category
	 $this->addElement('Select', 'category_id', array(
	  'required'  => true,
      'allowEmpty'=> false,
      'label' => '*Category',
      'class' => 'btn_form_inline',
      'description' => '<a name="add_more_category" id="add_more_category" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
 	$this -> category_id -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
	
	//Default custom field
	$defaultFormArgs = array('topLevelId' => '1', 'topLevelValue' => '1');
	if( !$this->_item ) {
      $defaultCustomFields = new Ynbusinesspages_Form_Business_Fields(array_merge(array(
        'labelField' => 'defaultField',
      	), $defaultFormArgs));
	}
    if( get_class($this) == 'Ynbusinesspages_Form_Business_Create' ) {
      $defaultCustomFields->setIsCreation(true);
    }
	
    $this->addSubForms(array(
      'defaultFields' => $defaultCustomFields
    ));
	
	//Custom field
    if( !$this->_item ) {
      $customFields = new Ynbusinesspages_Form_Business_Fields(array_merge(array(
        'labelField' => 'field',
      	),$this -> _formArgs));
	}
    if( get_class($this) == 'Ynbusinesspages_Form_Business_Create' ) {
      $customFields->setIsCreation(true);
    }
	
    $this->addSubForms(array(
      'fields' => $customFields
    ));
	
	//Business name
    $this->addElement('Text', 'name', array(
      'label' => '*Business Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	//Short description
	$this->addElement('Textarea', 'short_description', array(
        'label' => '*Short Description',
        'description' => $view->translate('Maximum 500 characters'),
        'allowEmpty' => false,
      	'required' => true,
        'validators' => array(
	        array('NotEmpty', true),
	        array('StringLength', false, array(1, 500)),
		),
    ));
	
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	
	$editorOptions['plugins'] =  array(
		   		'table', 'fullscreen', 'media', 'preview', 'paste',
		   		'code', 'image', 'textcolor'
    );
    $editorOptions['toolbar1'] = array(
	      'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
	      'image', '|', 'link', '|', 'fullscreen', '|', 'preview'
    );       
    $editorOptions['html'] = 1;
    $editorOptions['bbcode'] = 1;
	$editorOptions['mode'] = 'exact';
	$editorOptions['elements'] = 'description, short_description';
	  
	//Description
    $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'editorOptions' => $editorOptions,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
	
	//Search Result
	$this->addElement('Radio', 'search', array(
      'label' => 'Include in search results?',
      'multiOptions' => array(
        '1' => 'Yes, include in search results.',
        '0' => 'No, hide from search results.',
      ),
      'value' => '1',
    ));
	
	//Headquarter
	$this ->addElement('heading', 'headquarter', array(
		'label' => 'Headquarter',
	));
	
	//Adress map
	$this -> addElement('Dummy', 'location_map', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_location_search.tpl',
				'class' => 'form element',
			)
		)), 
	));
	
	 // add more location
    $this->addElement('Cancel', 'add_more_location', array(
        'link' => true,
        'ignore' => true,
        'label' => '',
        'onclick' => 'javascript:void(0)',
        'class' => 'fa fa-plus-circle',
    )); 
	
	
	$this -> addElement('hidden', 'number_location', array(
		'value' => '0',
		'order' => '95'
	));
	
	$this -> addElement('hidden', 'number_location_index', array(
		'value' => '0',
		'order' => '96'
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
	
	
	// Logo
	$this->addElement('File', 'photo', array(
      'label' => 'Logo',
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Publish Business',
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

    $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
