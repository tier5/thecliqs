<?php
class Ynsocialads_Form_Moneyrequests_Filter extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'method'=>'GET',
            'id' =>'filter_form'
        ));
    
        //Feature Filter
         
        $this->addElement('Heading', 'total', array(
            'label' => 'Total Virtual Money ($):',
            'class' => 'filter_elem',
        ));
        
        $this->addElement('Heading', 'remaining', array(
            'label' => 'Remaining ($):',
            'class' => 'filter_elem',
        ));
        
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
                'All'   => 'All',
                'pending' => 'pending',
                'approved' => 'approved',
                'rejected' => 'rejected',
            ),
            'value' => 'All',
            'class' => 'filter_elem',
            'onchange' => 'submit()'
        ));
    }
}