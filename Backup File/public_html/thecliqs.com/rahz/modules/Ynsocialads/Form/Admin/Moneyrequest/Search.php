<?php
class Ynsocialads_Form_Admin_MoneyRequest_Search extends Engine_Form {
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
                'method'=>'GET',
            ));

    $this->addElement('Text', 'name', array(
      'label' => 'Requester',
      'filters' => array(
                'StripTags'
            )
    ));
	
	$status_arr = array(
		'all' => 'All',
		'pending' => 'Pending',
		'approved' => 'Approved',
		'rejected' => 'Rejected'
	);
	
	 $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => $status_arr,
    ));
	
    $this->addElement('Text', 'from', array(
      'label' => 'Request From',
      'class' => 'date_picker'
    ));
	
    $this->addElement('Text', 'to', array(
      'label' => 'To',
      'class' => 'date_picker'
    ));
	
     // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 101,
      'value' => 'moneyrequest_id'
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