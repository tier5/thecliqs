<?php
class Ynbusinesspages_Form_Admin_Claims_Search extends Engine_Form {
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
		
		 $this->addElement('Text', 'name', array(
            'label' => 'Business',
        ));
		
		 $this->addElement('Text', 'claimant', array(
            'label' => 'Claimed By',
        ));
		
		$this->addElement('Text', 'from_date', array(
            'label' => 'From Date',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Text', 'to_date', array(
            'label' => 'To Date',
            'class' => 'date_picker input_small',
        ));
		
		$this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'business.name'
        ));
    
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));
		
        $this->addElement('Button', 'btn_submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->btn_submit->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}