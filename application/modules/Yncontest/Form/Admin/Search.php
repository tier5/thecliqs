<?php
class Yncontest_Form_Admin_Search extends Engine_Form {
  public function init()
  {
  	$this
  	->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator')
  	->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element')
  	->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Yncontest/Form/Decorator', 'decorator');

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
	$this    
      ->addDecorator('FormElements')
      ->addDecorator('Form');

    //Search Title
    $this->addElement('Text', 'name', array(
      'label' => 'Keywords',
    ));
	
	$this->addElement('Text', 'contest_id', array(
      'label' => 'Contest_ID',
      'class' => 'yncontest_admin_contest_id',
    ));
	
    //Search Owner
    $this->addElement('Text', 'owner', array(
      'label' => 'Owner',
    ));
    
		
	//Feature Filter
    $this->addElement('Select', 'browseby', array(
      'label' => 'Featured',
      'multiOptions' => array(
        'all'=>'',
        'featured_contest'  => 'Featured Contests',
      	'premium_contest'   => 'Premium Contests',
      	'endingsoon_contest'=> 'Ending Soon',           
    ),
      'value' => '',
      //'onchange' => 'this.form.submit();',
    ));
	
	//Feature Filter
    $plugin = Engine_Api::_() -> yncontest() -> getPlugins();    
	$contest_type =array_merge(array('0' => 'All'),$plugin);	
	$this->addElement('Select', 'contest_type', array(
      'label' => "Contest's type",
      'multiOptions' => $contest_type,           
    ));
    //Feature Filter
    $this->addElement('Select', 'contest_status', array(
      'label' => 'Contest Status',
      'multiOptions' => array(
      	'all'=>'',
        'draft' => 'Draft',        
        'denied' => 'Denied',
        'published' => 'Published',
        'close' => 'Closed',  
      	'waiting' => 'Waiting',
    ),
      'value' => '',
    ));
	
	$this->addElement('Select', 'activated', array(
      'label' => 'Contest Activated',
      'multiOptions' => array(
      	'1'=> 'Activated',
       	'0'=> 'Un-Activated'
    ),
      'value' => '',
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
  }
}