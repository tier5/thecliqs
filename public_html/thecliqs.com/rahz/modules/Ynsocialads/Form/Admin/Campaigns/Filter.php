<?php
class Ynsocialads_Form_Admin_Campaigns_Filter extends Engine_Form {
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
        
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
                'All'   => 'All',
                'active'  => 'Active',
                'deleted' => 'Deleted',
            ),
            'value' => 'All',
            'class' => 'filter_elem',
            'onchange' => 'submit()'
        ));
    }
}