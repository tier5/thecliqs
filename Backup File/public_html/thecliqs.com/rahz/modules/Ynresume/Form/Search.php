<?php
class Ynresume_Form_Search extends Engine_Form {
    public function init() {
		$view = Zend_Registry::get('Zend_View');
		
        $this->setAttribs(array('class' => 'global_form_box search_form', 'id' => 'filter_form'))
            ->setMethod('GET');

        $this->addElement('Text', 'title', array(
            'label' => 'Resume Name',
            'placeholder' => $view ->translate('Search resumes...'),
        ));
        
        $this->addElement('Text', 'headline', array(
            'label' => 'Professional Headline',
        ));
        
        $arr_industry_id = array('all' => 'All');
        //Industry
        $this->addElement('Select', 'industry_id', array(
            'label' => 'Industry',
            'multiOptions' => $arr_industry_id,
        ));
        
        //Adress map
        $this -> addElement('Dummy', 'location_map', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search.tpl',
                    'class' => 'form element',
                    'label' => 'Location',
                )
            )), 
        ));
        
        $this -> addElement('Integer', 'within', array(
            'label' => 'Radius (mile)',
            'maxlength' => '60',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 50,
        ));
        
        $this->addElement('Text', 'job_title', array(
            'label' => 'Job Title',
        ));
        
        $this->addElement('Text', 'company', array(
            'label' => 'Company',
        ));
        
        $this->addElement('Text', 'school', array(
            'label' => 'School',
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
        
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}