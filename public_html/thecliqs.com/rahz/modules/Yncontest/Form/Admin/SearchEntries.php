<?php
class Yncontest_Form_Admin_SearchEntries extends Engine_Form {
  public function init()
  {
  	$this
  	->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator')
  	->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element')
  	->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator');
	
    $this->clearDecorators()
         ->addDecorator('FormElements')
         ->addDecorator('Form')
         ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
         ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));

    //Search Title
    $this->addElement('Text', 'entry_name', array(
      'label' => 'Entry name',     
    ));
	
	$contest_type[] = "";
	$contest_type = array_merge($contest_type,Engine_Api::_()->yncontest()->arrPlugins);//getPlugins();
	$this->addElement('Select', 'entry_type', array(
      'label' => "Entry's type",
      'multiOptions' => $contest_type,           
    ));
		
	$this->addElement('Select', 'browseby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
      	'all'=>'',
        'view_count' => 'Most view',
      	'vote_count' => 'Most voted',
      	'like_count' => 'Most liked',        
      ),     
    ));	

	
     // Element: order
    $this->addElement('Hidden', 'orderby', array(
      'order' => 101,
      'value' => '',//'start_date'
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