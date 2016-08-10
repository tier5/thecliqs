<?php
class Yncredit_Form_SearchTransactions extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
         ->addDecorator('FormElements')
         ->addDecorator('Form')
         ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
         ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
	$this -> addAttribs(array('class' => 'global_form_box')) -> setMethod("GET");
    
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
	
	// add action element
	$types = Engine_Api::_() -> getDbTable('types', 'yncredit') -> getAllActions('user');
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
	
	// add time element
	
    $this->addElement('Select', 'time', array(
      'label' => 'Time',
      'multiOptions' => array(
        'today'  => 'Today',
        'week' => 'This week',
        'month' => 'This month',
        '3month' => '3 months',
        '' => 'All',
	    ),
	      'value' => 'today',
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