<?php
class Ynauction_Form_Admin_Transaction extends Engine_Form {

  public function init() {
  	$this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Errors')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;
     $this   ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
    $from = new Engine_Form_Element_Date('fromDate');
    $from->setLabel("From");
    $from->setAllowEmpty(false);
    $this->addElement($from);
    
    $to = new Engine_Form_Element_Date('toDate');
    $to->setLabel("To");
    $to->setAllowEmpty(false);
    $this->addElement($to);     
     $submit = new Zend_Form_Element_Button('fitter', array('type' => 'submit','name'=>'filter_tracking'));
     $submit
            ->setLabel('Filter')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
	    $this->addElements(array(
        $submit
    ));
            
    
  }

}