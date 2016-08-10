<?php
class Ynjobposting_Form_Admin_Company_Search extends Engine_Form {
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
        
        $this->addElement('Text', 'company_name', array(
            'label' => 'Company Name',
        ));
		
		$this->addElement('Text', 'owner', array(
            'label' => 'Company Creator',
        ));
		
		$arr_industries = array(
			'all'       => 'All',
		);
		$this->addElement('Select', 'industry_id', array(
            'label' => 'Industry',
            'multiOptions' => $arr_industries,
            'value' => 'all',
        ));
        
		$arr_status = array(
			'all'       => 'All',
			'published' => 'Published', 
			'closed'    => 'Closed', 
			'deleted'   => 'Deleted', 
		);
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => $arr_status,
            'value' => 'all',
        ));
        
		$arr_sponsors = array(
			'all'  => 'All',
			'1'    => 'Yes', 
			'0'    => 'No', 
		);
        $this->addElement('Select', 'sponsored', array(
            'label' => 'Sponsored',
            'multiOptions' => $arr_sponsors,
            'value' => 'all',
        ));
        
		$this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'company.company_id'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
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