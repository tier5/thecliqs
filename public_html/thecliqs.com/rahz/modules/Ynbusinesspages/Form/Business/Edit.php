<?php
class Ynbusinesspages_Form_Business_Edit extends Engine_Form
{
	protected $_item;
	protected $_formArgs;
	protected $_businessInfo;
	
	public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	} 
	
	public function getBusinessInfo()
	{
		return $this -> _businessInfo;
	}
	
	public function setBusinessInfo($businessInfo)
	{
		$this -> _businessInfo = $businessInfo;
	} 
	
	public function getItem()
	{
		return $this -> _item;
	}
	
	public function setItem($item)
	{
		$this -> _item = $item;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Edit Business');
	
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
	
	$defaultCustomFields = new Ynbusinesspages_Form_Business_FieldsParent(array_merge(array(
	  	'item' => $this->_item,
	 ), $defaultFormArgs));
	
	 $this->addSubForms(array(
      'fieldsParent' => $defaultCustomFields
    ));	  
	
	//Custom field
	
	$customFields = new Ynbusinesspages_Form_Business_Fields(array_merge(array(
	  	'item' => $this->_item,
	 ), $this -> _formArgs));
	
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
	
	//Adress map
	$this -> addElement('Dummy', 'location_map', array(
		'description' => 'Full Address',
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
        'class' =>'fa fa-plus-circle',
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
	
	//Approve member
	$this->addElement('Radio', 'approval', array(
      'label' => 'Approve members?',
      'description' => ' When people try to join this business, should they be allowed '.
        'to join immediately, or should they be forced to wait for approval?',
      'multiOptions' => array(
        '0' => 'New members can join immediately.',
        '1' => 'New members must be approved.',
      ),
      'value' => '0',
    ));
	
	//Size
	$this->addElement('Integer', 'size', array(
        'label' => 'Size',
        'validators' => array(
            new Engine_Validate_AtLeast(1),
        ),
        'value' => 1,
    ));
	
	//Contact Information
	$this ->addElement('heading', 'contact_info', array(
		'label' => 'Contact Information',
	));
	
	//Phone
	$this->addElement('Text', 'phone', array(
        'label' => 'Phone',
        'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
	    'class' => 'btn_form_inline',
	    'description' => '<a name="add_more_phone" id="add_more_phone" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
	$this -> phone -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
	
	//Fax
	$this->addElement('Text', 'fax', array(
        'label' => 'Fax',
        'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
	    'class' => 'btn_form_inline',
	    'description' => '<a name="add_more_fax" id="add_more_fax" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
	$this -> fax -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);

	//Email
    $this->addElement('Text', 'email', array(
        'label' => '*Email',
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
    
	//Country
	$locale = Zend_Registry::get('Zend_Translate')->getLocale();
	$territories = Zend_Locale::getTranslationList('territory', $locale, 2);
    asort($territories);
    //if( !$this->isRequired() ) {
      $territories = array_merge(array(
        '' => '',
      ), $territories);
    //}
    $arr_countries = array();
    foreach($territories as $key => $value)
	{
		$arr_countries[$value] = $value;;
	}
    
	$this -> addElement('select', 'country', array(
		'label' => 'Country',
		'multiOptions' => $arr_countries,
	));	
	
	//City
    $this->addElement('Text', 'city', array(
        'label' => 'City',
     	'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
    ));
	
	//Province
    $this->addElement('Text', 'province', array(
        'label' => 'Province',
     	'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
    ));
	
	//Zip Code
		$this->addElement('Text', 'zip_code', array(
        'label' => 'Zip Code',
        'validators' => array(
				array('StringLength', false, array(1, 64)),
			),
			'filters' => array(
				'StripTags',
				new Engine_Filter_Censor(),
        ),
    ));
	
	//Web address
	$this->addElement('Text', 'web_address', array(
        'label' => 'Web Address',
     	'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
	    'class' => 'btn_form_inline',
	    'description' => '<a name="add_more_web_address" id="add_more_web_address" type="button" class="fa fa-plus-circle" href="javascript:void(0);" onclick="javascript:void(0)"></a>',
    ));
    $this -> web_address -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
	
	
	// Logo
	$this->addElement('File', 'photo', array(
      'label' => 'Logo',
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	//Facebook
	$this->addElement('Text', 'facebook_link', array(
        'label' => 'Facebook',
     	'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
    ));
	
	//Twitter
	$this->addElement('Text', 'twitter_link', array(
        'label' => 'Twitter',
     	'validators' => array(
	        array('StringLength', false, array(1, 64)),
		),
	    'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	    ),
    ));
	
	//Operating Hours
	$this ->addElement('heading', 'operating_hours', array(
		'label' => 'Operating Hours',
	));
	
	if(Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynbusinesspages_time_format', 0))
	{
		$times = array(
			""          => '',
			"CLOSED"	=> 'CLOSED',
			"12:00 AM"	=> '00:00',
			"12:30 AM"	=> '00:30',
			"01:00 AM"	=> '01:00',
			"01:30 AM"	=> '01:30',
			"02:00 AM"	=> '02:00',
			"02:30 AM"	=> '02:30',
			"03:00 AM"	=> '03:00',
			"03:30 AM"	=> '03:30',
			"04:00 AM"	=> '04:00',
			"04:30 AM"	=> '04:30',
			"05:00 AM"	=> '05:00',
			"05:30 AM"	=> '05:30',
			"06:00 AM"	=> '06:00',
			"06:30 AM"	=> '06:30',
			"07:00 AM"	=> '07:00',
			"07:30 AM"	=> '07:30',
			"08:00 AM"	=> '08:00',
			"08:30 AM"	=> '08:30',
			"09:00 AM"	=> '09:00',
			"09:30 AM"	=> '09:30',
			"10:00 AM"	=> '10:00',
			"10:30 AM"	=> '10:30',
			"11:00 AM"	=> '11:00',
			"11:30 AM"	=> '11:30',
			"12:00 PM"	=> '12:00',
			"12:30 PM"	=> '12:30',
			"01:00 PM"	=> '13:00',
			"01:30 PM"	=> '13:30',
			"02:00 PM"	=> '14:00',
			"02:30 PM"	=> '14:30',
			"03:00 PM"	=> '15:00',
			"03:30 PM"	=> '15:30',
			"04:00 PM"	=> '16:00',
			"04:30 PM"	=> '16:30',
			"05:00 PM"	=> '17:00',
			"05:30 PM"	=> '17:30',
			"06:00 PM"	=> '18:00',
			"06:30 PM"	=> '18:30',
			"07:00 PM"	=> '19:00',
			"07:30 PM"	=> '19:30',
			"08:00 PM"	=> '20:00',
			"08:30 PM"	=> '20:30',
			"09:00 PM"	=> '21:00',
			"09:30 PM"	=> '21:30',
			"10:00 PM"	=> '22:00',
			"10:30 PM"	=> '22:30',
			"11:00 PM"	=> '23:00',
			"11:30 PM"	=> '23:30',
		);
	}
	else {
		$times = array(
			""          => '',
			"CLOSED"	=> 'CLOSED',
			"12:00 AM"	=> '12:00 AM',
			"12:30 AM"	=> '12:30 AM',
			"01:00 AM"	=> '01:00 AM',
			"01:30 AM"	=> '01:30 AM',
			"02:00 AM"	=> '02:00 AM',
			"02:30 AM"	=> '02:30 AM',
			"03:00 AM"	=> '03:00 AM',
			"03:30 AM"	=> '03:30 AM',
			"04:00 AM"	=> '04:00 AM',
			"04:30 AM"	=> '04:30 AM',
			"05:00 AM"	=> '05:00 AM',
			"05:30 AM"	=> '05:30 AM',
			"06:00 AM"	=> '06:00 AM',
			"06:30 AM"	=> '06:30 AM',
			"07:00 AM"	=> '07:00 AM',
			"07:30 AM"	=> '07:30 AM',
			"08:00 AM"	=> '08:00 AM',
			"08:30 AM"	=> '08:30 AM',
			"09:00 AM"	=> '09:00 AM',
			"09:30 AM"	=> '09:30 AM',
			"10:00 AM"	=> '10:00 AM',
			"10:30 AM"	=> '10:30 AM',
			"11:00 AM"	=> '11:00 AM',
			"11:30 AM"	=> '11:30 AM',
			"12:00 PM"	=> '12:00 PM',
			"12:30 PM"	=> '12:30 PM',
			"01:00 PM"	=> '01:00 PM',
			"01:30 PM"	=> '01:30 PM',
			"02:00 PM"	=> '02:00 PM',
			"02:30 PM"	=> '02:30 PM',
			"03:00 PM"	=> '03:00 PM',
			"03:30 PM"	=> '03:30 PM',
			"04:00 PM"	=> '04:00 PM',
			"04:30 PM"	=> '04:30 PM',
			"05:00 PM"	=> '05:00 PM',
			"05:30 PM"	=> '05:30 PM',
			"06:00 PM"	=> '06:00 PM',
			"06:30 PM"	=> '06:30 PM',
			"07:00 PM"	=> '07:00 PM',
			"07:30 PM"	=> '07:30 PM',
			"08:00 PM"	=> '08:00 PM',
			"08:30 PM"	=> '08:30 PM',
			"09:00 PM"	=> '09:00 PM',
			"09:30 PM"	=> '09:30 PM',
			"10:00 PM"	=> '10:00 PM',
			"10:30 PM"	=> '10:30 PM',
			"11:00 PM"	=> '11:00 PM',
			"11:30 PM"	=> '11:30 PM',
		);
	}
			
	//Monday
	$this -> addElement('select', 'monday_from', array(
		'label' => 'Monday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'monday_to', array(
		'multiOptions' => $times,
	));	
	
	//Tuesday
	$this -> addElement('select', 'tuesday_from', array(
		'label' => 'Tuesday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'tuesday_to', array(
		'multiOptions' => $times,
	));	
	
	//Wednesday
	$this -> addElement('select', 'wednesday_from', array(
		'label' => 'Wednesday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'wednesday_to', array(
		'multiOptions' => $times,
	));	
	
	//Thursday
	$this -> addElement('select', 'thursday_from', array(
		'label' => 'Thursday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'thursday_to', array(
		'multiOptions' => $times,
	));	
	
	//Friday
	$this -> addElement('select', 'friday_from', array(
		'label' => 'Friday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'friday_to', array(
		'multiOptions' => $times,
	));	
	
	//Saturday
	$this -> addElement('select', 'saturday_from', array(
		'label' => 'Saturday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'saturday_to', array(
		'multiOptions' => $times,
	));	
	
	//Sunday
	$this -> addElement('select', 'sunday_from', array(
		'label' => 'Sunday',
		'multiOptions' => $times,
	));	
	
	$this -> addElement('select', 'sunday_to', array(
		'multiOptions' => $times,
	));	
	
	// Init timezeon
    $this->addElement('Select', 'timezone', array(
      'label' => 'Default Timezone',
      'multiOptions' => array(
        '(UTC-8) Pacific Time (US & Canada)'  => '(UTC-8) Pacific Time (US & Canada)',
        '(UTC-7) Mountain Time (US & Canada)' => '(UTC-7) Mountain Time (US & Canada)',
        '(UTC-6) Central Time (US & Canada)'  => '(UTC-6) Central Time (US & Canada)',
        '(UTC-5) Eastern Time (US & Canada)'  => '(UTC-5) Eastern Time (US & Canada)',
        '(UTC-4)  Atlantic Time (Canada)'   => '(UTC-4)  Atlantic Time (Canada)',
        '(UTC-9)  Alaska (US & Canada)' => '(UTC-9)  Alaska (US & Canada)',
        '(UTC-10) Hawaii (US)'  => '(UTC-10) Hawaii (US)',
        '(UTC-11) Midway Island, Samoa'     => '(UTC-11) Midway Island, Samoa',
        '(UTC-12) Eniwetok, Kwajalein' => '(UTC-12) Eniwetok, Kwajalein',
        '(UTC-3:30) Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
        '(UTC-3) Brasilia, Buenos Aires, Georgetown' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
        '(UTC-2) Mid-Atlantic' => '(UTC-2) Mid-Atlantic',
        '(UTC-1) Azores, Cape Verde Is' => '(UTC-1) Azores, Cape Verde Is',
        'Greenwich Mean Time (Lisbon, London)' => 'Greenwich Mean Time (Lisbon, London)',
        '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
        '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
        '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
        '(UTC+3:30) Tehran' => '(UTC+3:30) Tehran',
        '(UTC+4) Abu Dhabi, Kazan, Muscat' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
        '(UTC+4:30) Kabul' => '(UTC+4:30) Kabul',
        '(UTC+5) Islamabad, Karachi, Tashkent' => '(UTC+5) Islamabad, Karachi, Tashkent',
        '(UTC+5:30) Bombay, Calcutta, New Delhi' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
        '(UTC+5:45) Nepal' => '(UTC+5:45) Nepal',
        '(UTC+6) Almaty, Dhaka' => '(UTC+6) Almaty, Dhaka',
        '(UTC+6:30) Cocos Islands, Yangon' => '(UTC+6:30) Cocos Islands, Yangon',
        '(UTC+7) Bangkok, Jakarta, Hanoi' => '(UTC+7) Bangkok, Jakarta, Hanoi',
        '(UTC+8) Beijing, Hong Kong, Singapore, Taipei' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
        '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
        '(UTC+9:30) Adelaide, Darwin' => '(UTC+9:30) Adelaide, Darwin',
        '(UTC+10) Brisbane, Melbourne, Sydney, Guam' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
        '(UTC+11) Magadan, Soloman Is., New Caledonia' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
        '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
      )
    ));
	
	//founder 
	$this -> addElement('Text', 'to', array('label' => 'Founder', 'autocomplete' => 'off'));
	Engine_Form::addDefaultDecorators($this -> to);
	$this -> to -> setDescription($view -> translate("YNBUSINESSPAGES_FOUNDER_DESC"));
	
	// Init to Values
	$this -> addElement('hidden', 'toValues', array(
		'style' => 'margin-top:-5px',
		'order' => 40,
		'filters' => array('HtmlEntities'),
	));
	
	Engine_Form::addDefaultDecorators($this -> toValues);
	
	$package = $this ->_item -> getPackage();
	
	if(($package -> getIdentity() > 0) && ($package -> allow_owner_add_customfield))
	{
		//Additional Information
		$index_info = 1;
		if(count($this -> _businessInfo) > 0)
		{
			foreach($this -> _businessInfo as $info)
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
    }
	
	//Tags
    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
	
    $this->tags->getDecorator("Description")->setOption("placement", "append");
	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Save Changes',
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
