<?php
class Ynresume_Form_Admin_Transactions_Search extends Engine_Form {
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
		
		 $this->addElement('Text', 'transaction_id', array(
            'label' => 'Transaction ID',
        ));
		
		 $this->addElement('Text', 'name', array(
            'label' => 'User Name',
        ));
		
        $this->addElement('Select', 'gateway_id', array(
            'label' => 'Payment Method',
            'multiOptions' => array(
                'all'   => 'All',
            ),
            'value' => 'all',
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
            'value' => 'transaction.transaction_id'
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