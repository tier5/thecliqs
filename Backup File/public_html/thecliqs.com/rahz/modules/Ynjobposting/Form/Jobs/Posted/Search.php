<?php
class Ynjobposting_Form_Jobs_Posted_Search extends Engine_Form 
{
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
    
        //Feature Filter
         
        $this->addElement('Text', 'job_title', array(
            'label' => 'Job Tittle'
        ));
        
        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
                'all' => 'All',
        		'draft' => 'Draft',
                'pending' => 'Pending',
                'published' => 'Published',
                'denied' => 'Denied',
                'ended' => 'Ended',
                'expired' => 'Expired',
                //'deleted' => 'Deleted'
            ),
            'value' => 'all'
        ));
        
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'job.job_id'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));
        
        $this->search->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons', 'style' => 'margin-top: 15px;'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
        	
    }
}