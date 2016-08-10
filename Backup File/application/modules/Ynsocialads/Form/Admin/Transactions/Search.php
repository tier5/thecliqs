<?php
class Ynsocialads_Form_Admin_Transactions_Search extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form',
            'method'=>'POST',
        ));
    
        //Feature Filter
         
        $this->addElement('Select', 'payment_method', array(
            'label' => 'Payment Method',
            'multiOptions' => array(
                'All'   => 'All',
                'Credit'  => 'Credit',
                '2Checkout' => '2Checkout',
                'Virtual Money' => 'Virtual Money',
                'Pay Later' => 'Pay Later',
                'PayPal' => 'PayPal',
            ),
            'value' => 'All',
            'class' => 'search_elem',
            'onchange' => 'submit()'
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
            'class' => 'search_elem',
            'onchange' => 'submit()'
        ));
    }
}