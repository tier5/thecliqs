<?php
class Yncredit_Form_Admin_SearchStatistics extends Engine_Form {
	public function init()
	{
		$this
	      ->setAttrib('class', 'global_form_box')
	      ->addDecorator('FormElements')
	      ->addDecorator('Form')
	      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
	      ;
		
		// Init mode
	    $this->addElement('Select', 'mode', array(
	      'multiOptions' => array(
	        'normal' => 'All',
	        'cumulative' => 'Cumulative',
	        'delta' => 'Change in',
	      ),
	      'value' => 'normal',
	      'decorators' => array(
	        'ViewHelper',
	        array('HtmlTag', array('tag' => 'div')),
	      ),
	    ));
		
	    // Init mode
	    $this->addElement('Select', 'group', array(
	      'multiOptions' => array(
	       	'earn' => 'YNCREDIT_GROUP_TYPE_EARN',
	       	'buy' => 'YNCREDIT_GROUP_TYPE_BUY',
	       	'receive' => 'YNCREDIT_GROUP_TYPE_RECEIVE',
	       	'spend' => 'YNCREDIT_GROUP_TYPE_SPEND',
	       	'send' => 'YNCREDIT_GROUP_TYPE_SEND',
	      ),
	      'value' => 'earn',
	      'decorators' => array(
	        'ViewHelper',
	        array('HtmlTag', array('tag' => 'div')),
	      ),
	    ));
		
		$moduleOptions = array('' => 'All Modules');
		
	    $this->addElement('Select', 'modu', array(
	      'multiOptions' => $moduleOptions,
	      'value' => '',
	      'decorators' => array(
	        'ViewHelper',
	        array('HtmlTag', array('tag' => 'div')),
	      ),
	    ));
	    // Init period
	    $this->addElement('Select', 'period', array(
	      'multiOptions' => array(
	        Zend_Date::WEEK => 'This week',
	        Zend_Date::MONTH => 'This month',
	        Zend_Date::YEAR => 'This year',
	      ),
	      'value' => 'month',
	      'decorators' => array(
	        'ViewHelper',
	        array('HtmlTag', array('tag' => 'div')),
	      ),
	    ));
	
	    // Init chunk
	    $this->addElement('Select', 'chunk', array(
	      'multiOptions' => array(
	        Zend_Date::DAY => 'By Day',
	        Zend_Date::WEEK => 'By Week',
	        Zend_Date::MONTH => 'By Month',
	        Zend_Date::YEAR => 'By Year',
	      ),
	      'value' => 'day',
	      'decorators' => array(
	        'ViewHelper',
	        array('HtmlTag', array('tag' => 'div')),
	      ),
	    ));
	
	    // Init submit
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Search',
	      'type' => 'submit',
	      'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
	      'decorators' => array(
	        'ViewHelper',
	        array('HtmlTag', array('tag' => 'div')),
	      ),
	    ));
	}
}
?>