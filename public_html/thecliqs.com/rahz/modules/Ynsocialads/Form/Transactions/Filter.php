<?php
class Ynsocialads_Form_Transactions_Filter extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'method'=>'GET',
            'id' => 'filter_form'
        ));
    
        //Feature Filter
         
        $this->addElement('Select', 'gateway_id', array(
            'label' => 'Payment Method',
            'multiOptions' => array(
                'All'   => 'All',
                -1 => 'Pay with Virtual Money',
                -2 => 'Pay Later',
            ),
            'value' => 'All',
            'class' => 'filter_elem',
        ));
        
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
                'All'   => 'All',
                'initialized'  => 'Initialized',
                'expired' => 'Expired',
                'pending' => 'Pending',
                'completed' => 'Completed',
                'canceled' => 'Canceled',
            ),
            'value' => 'All',
            'class' => 'filter_elem',
        ));
        
        $this->addElement('Button', 'submit', array(
            'label' => 'Filter',
            'type' => 'submit',
            'class' => 'filter_elem',
        ));
    }
}