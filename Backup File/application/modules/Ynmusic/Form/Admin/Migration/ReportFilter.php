<?php
class Ynmusic_Form_Admin_Migration_ReportFilter extends Engine_Form {
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
        
        $this->addElement('Text', 'item_title', array(
            'label' => 'Item',
        ));
		
		$this->addElement('Text', 'ori_title', array(
            'label' => 'Orginal Item',
        ));
		
		$this->addElement('Select', 'type', array(
            'label' => 'Type',
            'multiOptions' => array(
				'all' => 'all',
				'ynmusic_album' => 'album',
				'ynmusic_playlist' => 'playlist',
			),
            'value' => 'all',
        ));
		
		$this->addElement('Text', 'owner', array(
            'label' => 'Owner',
        ));
		
		$this->addElement('Select', 'status', array(
            'label' => 'Status',
            'multiOptions' => array(
				'all' => 'all',
				'processing' => 'processing',
				'imported' => 'imported',
				'updating' => 'updating',
			),
            'value' => 'all',
        ));
        
		$this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'report.modified_date'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
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