<?php
class Ynbusinesspages_Form_Admin_Business_Search extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form',
            'method'=>'GET',
        ));
        
        $this->addElement('Text', 'title', array(
            'label' => 'Business Name',
        ));
		
		$this->addElement('Text', 'owner', array(
            'label' => 'Owner',
        ));
		
		$arr_categories = array(
			'all'       => 'All',
		);
		$this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $arr_categories,
            'value' => 'all',
        ));
        
		$arr_status = array(
			'all'       => 'All',
			'draft' => 'Draft', 
			'pending' => 'Pending', 
			'published' => 'Published', 
			'closed' => 'Closed', 
			'denied' => 'Denied', 
			'unclaimed' => 'Unclaimed', 
			'claimed' => 'Claimed', 
		);
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => $arr_status,
            'value' => 'all',
        ));
        
		$arr_features = array(
			'all'  => 'All',
			'1'    => 'Yes', 
			'0'    => 'No', 
		);
        $this->addElement('Select', 'featured', array(
            'label' => 'Featured',
            'multiOptions' => $arr_features,
            'value' => 'all',
        ));
        
		$this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'business.business_id'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));
		
		$this->addElement('Text', 'from_date', array(
            'label' => 'Create From',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Text', 'to_date', array(
            'label' => 'To',
            'class' => 'date_picker input_small',
        ));
		
        $this->addElement('Button', 'button_submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->button_submit->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}