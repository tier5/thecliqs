<?php
class Ynmusic_Form_Admin_Song_Search extends Engine_Form {
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
            'label' => 'Name'
        ));
		
		 $this->addElement('Text', 'owner', array(
            'label' => 'Added by'
        ));
		
		 $this->addElement('Text', 'artist', array(
            'label' => 'Artist'
        ));
        
       $this->addElement('Text', 'genre', array(
            'label' => 'Genre'
        ));
		
		 // $this->addElement('Select', 'featured', array(
            // 'label' => 'Featured',
            // 'multiOptions' => array(
                // 'all'   => 'All',
                // 1 => 'Yes',
                // 0 => 'No'
            // ),
            // 'value' => 'all'
        // ));
		
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'song.song_id'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));
        
        $this->search->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}