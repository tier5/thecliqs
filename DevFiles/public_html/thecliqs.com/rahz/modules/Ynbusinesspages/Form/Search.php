<?php
class Ynbusinesspages_Form_Search extends Fields_Form_Search {
    public function init() {
    	
		$view = Zend_Registry::get('Zend_View');
		
        $this->setAttribs(array('class' => 'global_form_box search_form', 'id' => 'filter_form'))
                ->setMethod('GET');

        $this->addElement('Text', 'title', array(
            'label' => 'Keyword',
            'placeholder' => $view ->translate('Search businesses...'),
        ));
        
        $this->addElement('Select', 'order', array(
            'label' => 'Sort by',
            'multiOptions' => array(
                'business.business_id' => $view ->translate('Most Recent'),
                'business.like_count' => $view ->translate('Most Liked'),
                'business.view_count' => $view ->translate('Most Viewed'),
                'follow_count' => $view ->translate('Most Followed'),
                'business.name' => $view ->translate('Alphabetical'),
                'review_count' => $view ->translate('Most Reviewed'),
                'rating' => $view ->translate('Highest Rated'),
            ),
            'value' => 'business.business_id'
        ));
        
		$this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
                'all' => $view ->translate('All')	,
            ),
            'value' => 'all'
        ));
		
		$this->addElement('Select', 'status_claimed', array(
            'label' => 'Status',
            'multiOptions' => array(
                'all' => $view ->translate('All')	,
            ),
            'value' => 'all'
        ));
		
        $this -> addElement('Text', 'location', array(
            'label' => 'Location',
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search2.tpl',
                    'label' => 'Location',
                    'viewModule' => 'ynbusinesspages'
                )
            )), 
        ));
		$label = 'Radius (mile)';
		$placeholder = 'Radius (mile)..';
        if(Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynbusinesspages_radius_unit', 0))
		{
			$label = 'Radius (km)';
			$placeholder = 'Radius (km)..';
		}
        $this -> addElement('Text', 'within', array(
            'label' => $label,
            'placeholder' => $view -> translate($placeholder),
            'maxlength' => '60',
            'required' => false,
            'style' => "display: block",
            'validators' => array(
                array(
                    'Int',
                    true
                ),
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this -> addElement('hidden', 'lat', array(
            'value' => '0',
            'order' => '98'
        ));
        
        $this -> addElement('hidden', 'long', array(
            'value' => '0',
            'order' => '99'
        ));
        
        $this -> addElement('hidden', 'tag', array(
        ));
        
        $this->getCategoryElement();
        parent::init();
        // Buttons
       
        $subform = new Zend_Form_SubForm(array(
            'order' => 1000000,
            'name' => 'search_button',
            'decorators' => array(
                'FormElements',
            )
        ));
        
        Engine_Form::enableForm($subform);
        
        $subform->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));

        $this->addSubForm($subform, $subform->getName());
        $this->loadDefaultDecorators();
        
        $this->removeElement('separator1');
        $this->removeElement('separator2');
    }

    public function getCategoryElement() {
    $multiOptions = array('' => ' ');
    
    $categoryFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    
    if( count($categoryFields) !== 1 || !isset($categoryFields['profile_type']) ) return;
    
    
    $categoryField= $categoryFields['profile_type'];
    
    $options = $categoryField->getOptions();
    
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_'  . $categoryField->field_id  . ' ',
      //'onchange' => 'changeFields($(this));',
      'multiOptions' => array(
        'all' => 'All'
      ),
    ));
    }
    
    public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
        ->addDecorator('FormMessages', array('placement' => 'PREPEND'))
        ->addDecorator('FormErrors', array('placement' => 'PREPEND'))
        ->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'form-description'))
        ->addDecorator('FormTitle', array('placement' => 'PREPEND', 'tag' => 'h3'))
        ->addDecorator('FormWrapper', array('tag' => 'div'))
        ->addDecorator('FormContainer', array('tag' => 'div'))
        ->addDecorator('Form')
        ; //->addDecorator($decorator);
    }
  }
}