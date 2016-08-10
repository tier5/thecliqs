<?php
class Ynmusic_Form_Admin_Artist_Search extends Engine_Form {
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
        
		$locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
		
		$this -> addElement('Select', 'country', array(
			'label' => 'Country',
		));
		
		$this -> country -> addMultiOption('all', Zend_Registry::get("Zend_Translate")->_("All"));
		foreach($territories as $territory) {
			$this -> country -> addMultiOption($territory, $territory);
		}
		
       $this->addElement('Text', 'genre', array(
            'label' => 'Genre'
        ));

        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'artist_id'
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