<?php
class Yncredit_Form_Admin_SearchTransactions extends Engine_Form
{
  public function init()
  {
   	$this->clearDecorators()->addDecorator('FormElements')
        ->addDecorator('Form')->addDecorator('HtmlTag', array(
            'tag' => 'div',
            'class' => 'search'))
        ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
        ->setAttribs(array('id' => 'filter_form',
         'method'=>'GET',        
        ));
    
	$this->addElement('Text', 'member', array(
            'label' => 'Member',
        	''
        ));
	
	$date_validate = new Zend_Validate_Date("YYYY-MM-dd");
    $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);
	$this->addElement('Text', 'start_date', array(
            'label' => 'From Date',
            'required' => false,));
    $this->getElement('start_date')->addValidator($date_validate);

    $this->addElement('Text', 'end_date', array(
            'label' => 'To Date',
            'required' => false,));
    $this->getElement('end_date')->addValidator($date_validate);

	
	$view = Zend_Registry::get('Zend_View');
	// add group element
    $this->addElement('Select', 'group', array(
      'label' => 'Group Type',
      'multiOptions' => array(
        ''  => 'All',
        'earn' => 'YNCREDIT_GROUP_TYPE_EARN',
        'buy' => 'YNCREDIT_GROUP_TYPE_BUY',
        'receive' => 'YNCREDIT_GROUP_TYPE_RECEIVE',
        'spend' => 'YNCREDIT_GROUP_TYPE_SPEND',
        'send' => 'YNCREDIT_GROUP_TYPE_SEND',
	    ),
	      'value' => '',
    ));
	
	$moduleOptions = array('' => 'All');
		
    $this->addElement('Select', 'modu', array(
      'label' => 'Module',
      'multiOptions' => $moduleOptions,
      'value' => '',
    ));
	
	// add action element
	$types = Engine_Api::_() -> getDbTable('types', 'yncredit') -> getAllActions();
	$actions = array('' => 'All');    
	foreach($types as $type)
	{
		$actions[$type -> type_id] = $view->translate('YNCREDIT_MODULE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->module), '_'))). " - ". $view->translate('YNCREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->action_type), '_')));
	}
	$this->addElement('Select', 'action_type', array(
      'label' => 'Action',
      'multiOptions' => $actions,
	      'value' => '',
    ));
	
	$this->addElement('Hidden', 'order', array('order' => 10004,));      
    $this->addElement('Hidden', 'direction', array('order' => 10005,));
	
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