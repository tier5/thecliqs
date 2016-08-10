<?php
class Ynsocialads_Form_Campaigns_Statistics extends Engine_Form {
	public function init(){
		$this
	      ->setAttrib('class', 'global_form_box')
		  ->setAttrib('id', 'statistic_form')
	      ->addDecorator('FormElements')
	      ->addDecorator('Form')
          ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
          ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
	      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
	      ;
		
		// Init mode
	    $this->addElement('Select', 'mode', array(
	      'label' => 'See',
	      'multiOptions' => array(
	        'normal' => 'All',
	        'cumulative' => 'Cumulative',
	        'delta' => 'Change in',
	      ),
	      'value' => 'normal',
	      'class' => 'filter_elem',
	    ));
		
	    // Init type
	    $this->addElement('Select', 'type', array(
	      'label' => 'Type',
	      'multiOptions' => array(
	      	'all' => 'All',
	       	'click' => 'Click',
	       	'impression' => 'Impression',
	      ),
	      'value' => 'earn',
	      'class' => 'filter_elem',
	    ));
		
	    // Init period
	    $this->addElement('Select', 'period', array(
	      'label' => 'Duration',
	      'multiOptions' => array(
	        Zend_Date::WEEK => 'This week',
	        Zend_Date::MONTH => 'This month',
	        Zend_Date::YEAR => 'This year',
	      ),
	      'value' => 'week',
	      'class' => 'filter_elem',
	    ));
	
	    // Init chunk
	    $this->addElement('Select', 'chunk', array(
	      'label' => 'Time Summary',
	      'multiOptions' => array(
	        Zend_Date::DAY => 'By Day',
	        Zend_Date::WEEK => 'By Week',
	        Zend_Date::MONTH => 'By Month',
	        Zend_Date::YEAR => 'By Year',
	      ),
	      'value' => 'day',
	      'class' => 'filter_elem',
	    ));
	
	    // Init submit
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Filter',
	      'type' => 'submit',
	      'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
	      'class' => 'filter_elem',
	    ));
	}
}
?>