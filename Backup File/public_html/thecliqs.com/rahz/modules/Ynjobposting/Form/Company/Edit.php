<?php
class Ynjobposting_Form_Company_Edit extends Engine_Form
{
	protected $_item;
	protected $_location;
	protected $_formArgs;
	protected $_companyInfo;
	
	public function getItem()
	{
		return $this -> _item;
	}
	
	public function setItem($item)
	{
		$this -> _item = $item;
	} 
	
	public function getCompanyInfo()
	{
		return $this -> _companyInfo;
	}
	
	public function setCompanyInfo($companyInfo)
	{
		$this -> _companyInfo = $companyInfo;
	} 
	
	public function getLocation()
	{
		return $this -> _location;
	}
	
	public function setLocation($location)
	{
		$this -> _location = $location;
	} 
	
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
	 
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Edit Company');
	
	//Industry
	 $this->addElement('Select', 'industry_id', array(
	  'required'  => true,
      'allowEmpty'=> false,
      'label' => '*Industry',
      'class' => 'btn_form_inline',
      'description' => '<a name="add_more" id="add_more" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
	$this -> industry_id -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
	
	
	//Custom field
 	 $customFields = new Ynjobposting_Form_Company_Fields(array_merge(array(
	  	'item' => $this->_item,
	 ), $this -> _formArgs));
	
	 $this->addSubForms(array(
      'fields' => $customFields
    ));	  
	
	//Company name
    $this->addElement('Text', 'name', array(
      'label' => '*Company Name',
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
	$editorOptions['elements'] = 'description';
	  
	//Description
    $this->addElement('TinyMce', 'description', array(
      'label' => '*Description',
      'editorOptions' => $editorOptions,
      'required'   => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
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
				'location' => $this->_location,
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
	
	//Website
	
	$this->addElement('Text', 'website', array(
      'label' => 'Website',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	//Company size
	$this ->addElement('heading', 'company_size', array(
		'label' => 'Company Size',
	));
	
	$this->addElement('Integer', 'from', array(
        'label' => 'from',
        'required' =>true,
        'validators' => array(
            new Engine_Validate_AtLeast(0),
        ),
        'value' => 0,
    ));
            
    $this->addElement('Integer', 'to', array(
        'label' => 'to',
        'required' =>true,
        'validators' => array(
            new Engine_Validate_AtLeast(0),
        ),
        'value' => 0,
    ));
	
	$this ->addElement('heading', 'employees', array(
		'label' => 'employees',
	));
	
   
	//Contact Information
	$this ->addElement('heading', 'contact_info', array(
		'label' => 'Contact Information',
	));
	
	$this->addElement('Text', 'contact_name', array(
        'label' => '*Name',
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
    
    $this->addElement('Text', 'contact_email', array(
        'label' => '*Email Address',
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
    
    $this->addElement('Text', 'contact_phone', array(
        'label' => '*Phone',
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
	
    $this->addElement('Text', 'contact_fax', array(
        'label' => 'Fax',
     	'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
    ));
	
	//Additional Information
	$this ->addElement('heading', 'additional_information', array(
		'label' => 'Additional Information',
	));
	
	$index_info = 1;
	if(count($this -> _companyInfo) > 0)
	{
		foreach($this -> _companyInfo as $info)
		{
			$element_name = 'addmore_'.$index_info;
			$labelHeader = 'header_'.$index_info;
			$labelContent = 'content_'.$index_info;
			if($index_info == 1)
			{
				$this -> addElement('Dummy',$element_name, array(
					'decorators' => array( array(
						'ViewScript',
						array(
							'viewScript' => '_add_info.tpl',
							'class' => 'form element',
							'info' => $info,
						)
					)), 
				));
			}
			else 
			{
				$this -> addElement('Dummy',$element_name , array(
					'decorators' => array( array(
						'ViewScript',
						array(
							'viewScript' => '_add_more_info.tpl',
							'class' => 'form element',
							'labelHeader' => $labelHeader,
							'labelContent' => $labelContent,
							'info' => $info,
						)
					)), 
				));
			}
			$index_info++;
		}
	}
	else 
	{
		$this -> addElement('Dummy', 'addmore', array(
				'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_add_info.tpl',
					'class' => 'form element',
				)
			)), 
		));
	}
	
	$this->addElement('Text', 'number_add_more', array(
      'style' => 'display:none;',
      'value' => 1,
    ));
	
	$this->addElement('Text', 'number_add_more_index', array(
      'style' => 'display:none;',
      'value' => 1,
    ));
	
	// Logo
	
	$this->addElement('File', 'photo', array(
      'label' => 'Profile Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
    // Cover Photo
    
    $this->addElement('File', 'cover_thumb', array(
      'label' => 'Cover Photo'
    ));
    $this->cover_thumb->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
    // Privacy
    $availableLabels = array(
      'everyone' => 'Everyone',
      'registered' => 'All Registered',
      'network' => 'My Network',
      'owner_member' => 'My Friends',
      'owner' => 'Only Me',
    );

    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynjobposting_company', $id, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'view', array(
            'label' => 'View Privacy',
            'description' => 'Who can see this company?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Edit',
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

    $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
