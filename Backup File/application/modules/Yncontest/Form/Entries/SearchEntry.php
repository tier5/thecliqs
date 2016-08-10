<?php
class Yncontest_Form_Entries_SearchEntry extends Engine_Form {
  public function init()
  {
    $this->clearDecorators()
         ->addDecorator('FormElements')
         ->addDecorator('Form')
         ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
         ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

     $this->setAttribs(array(
                 'id' => 'filter_form',
                 'class' => 'global_form_box',
                 'method'=>'POST',
             ));

    //Search Title
    $this->addElement('Text', 'entry_id', array(
      'label' => 'ID',
    ));
    $this->addElement('Text', 'entry_name', array(
    		'label' => "Entry's Title",
    ));
       
		
	//Feature Filter
    $this->addElement('Select', 'entry_status', array(
      'label' => 'Status',
      'multiOptions' => array(       
      	'' => '',		
        'published' => 'published',       
      	'draft' => 'draft',
      	'win' => 'win',     
    ),
      'value' => 'published',
     // 'onchange' => 'this.form.submit();',
    ));
	
	$this->addElement('Text', 'owner', array(
    	'label' => 'Owner',
    ));
       
	//Feature Filter
    $this->addElement('Select', 'awards', array(
      'label' => 'Awards',
      'multiOptions' => array(            
    	),      
     // 'onchange' => 'this.form.submit();',
    ));   	
	
     // Element: order
    $this->addElement('Hidden', 'orderby', array(
      'order' => 101,
      'value' => 'start_date'
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 102,
      'value' => 'DESC',
    ));

     // Element: direction
    $this->addElement('Hidden', 'page', array(
      'order' => 103,
    ));

     // Buttons
    $this->addElement('Button', 'button', array(
      'label' => 'Search',
      'type' => 'submit',
    ));

    $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
  }
}