<?php
class Ynresume_Form_Admin_Search extends Engine_Form
{
	public function init() 
	{
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
            'label' => 'Resume',
        ));
		
        // Element: order
		$this->addElement('Hidden', 'order', array(
            'order' => 998,
            'value' => 'resume.name'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 999,
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