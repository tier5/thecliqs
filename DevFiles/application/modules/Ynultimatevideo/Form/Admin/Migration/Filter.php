<?php
class Ynultimatevideo_Form_Admin_Migration_Filter extends Engine_Form {
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
            'label' => 'Title',
        ));
		
		$this->addElement('Text', 'owner', array(
            'label' => 'Owner',
        ));
		
		$this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'object.creation_date'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));
		
		$this->addElement('Text', 'from_date', array(
            'label' => 'Created From',
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