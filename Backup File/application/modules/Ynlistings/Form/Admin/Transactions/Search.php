<?php
class Ynlistings_Form_Admin_Transactions_Search extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box transaction_search',
            'id' => 'filter_form',
            'method'=>'GET',
        ));
        
        $this->addElement('Select', 'gateway_id', array(
            'label' => 'Payment Method',
            'multiOptions' => array(
                'all'   => 'All',
            ),
            'value' => 'all',
        ));
        
        $this->addElement('Text', 'listing', array(
            'label' => 'Listing',
        ));
        
        $this->addElement('Text', 'owner', array(
            'label' => 'Listing Owner',
        ));
        
        $this->addElement('Text', 'date_from', array(
            'label' => 'Purchased From',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Text', 'date_to', array(
            'label' => 'Purchased To',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->submit->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}