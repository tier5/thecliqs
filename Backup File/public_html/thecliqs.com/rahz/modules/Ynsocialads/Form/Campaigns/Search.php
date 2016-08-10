<?php
class Ynsocialads_Form_Campaigns_Search extends Engine_Form {
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
    
        //Search Title
        $this->addElement('Text', 'title', array(
            'label' => 'Search Campaign',
            'class' => 'search_elem',
            'filters' => array(
                'StripTags'
            )
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
            'class' => 'filter_elem'
        ));
    
         // Buttons
        $this->addElement('Button', 'button', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true
        ));
    
        $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}