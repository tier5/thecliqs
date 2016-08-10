<?php

class Ynjobposting_Form_Admin_Mail_Search extends Engine_Form {

  public function init() {
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
     		->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
	
	// search by title
    //$title = new Zend_Form_Element_Text('title');
	
	$this->addElement('text','title', array(
		'label'=>'Keywords',
		'decorators'=>array(
			array('ViewHelper'), 
			array('Label', array('tag'=>null,'placement'=>'PREPEND')),
			array('HtmlTag', array('tag'=>'div'))
		)
	));
	
	
		
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'btn_submit'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $from = new Engine_Form_Element_Date('fromDate');
    $from->setLabel("From");
    $from->setAllowEmpty(false);
    $this->addElement($from);
    
    $to = new Engine_Form_Element_Date('toDate');
    $to->setLabel("To");
    $to->setAllowEmpty(false);
    $this->addElement($to);    
		
     $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        ' ' => 'All',
        '1' => 'Success',
        '0' => 'Failure',
       ),
       'onchange' => 'this.form.submit();',
    ));
    
    // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));
	
    $this->addElements(array(
        $submit
    ));

  }

}