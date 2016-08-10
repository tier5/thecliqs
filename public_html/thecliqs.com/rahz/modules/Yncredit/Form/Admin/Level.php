<?php
class Yncredit_Form_Admin_Level extends Engine_Form
{
	public function init()
	{
		parent::init();
		
		// Prepare user levels
	    $levelOptions = array();
	    foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level ) 
	    {
	    	if($level -> type != "public")
	      		$levelOptions[$level->level_id] = $level->getTitle();
	    }
	
	    // Element: level_id
	    $this->addElement('Select', 'level_id', array(
	      'label' => 'Member Level',
	      'multiOptions' => $levelOptions,
	      'onchange' => 'javascript:fetchLevelSettings(this.value);',
	      'ignore' => true,
	    ));
		
		
		//Spend Credit Settings
		$this->addElement('MultiCheckbox', 'spend', array(
				'label'        => 'Spend Credits',
				'multiOptions' => array(
						'upgrade_subscription' 	=> 'Allow this member level to buy/upgrade subscription via credit',
						'buy_deal' => 'Allow this member level to buy deal via credit',
						'publish_deal' => 'Allow this member level to  publish deal via credit',
						'publish_contest' => 'Allow this member level to publish contest via credit',
				),
				'value' => array('upgrade_subscription', 'buy_subscription', 'buy_deal', 'publish_deal', 'publish_contest'),
		));
		
		// Send Credit Settings
		$this->addElement('Radio', 'send', array(
				'label'        => 'Send credit to friends',
				'multiOptions' => array(
						1 => 'Yes, allow sending.',
						0 => 'No, do not allow.',
				),
				'value' => 1,
		));
		
		// Max Send Settings
		$this->addElement('Text', 'max_send', array(
				'description'  => 'Maximum credits for sending',
				'value' => 1500,
		));
		
		// Period Send Settings
		$this->addElement('Select', 'period_send', array(
				'description'  => 'Period',
				'multiOptions' => array(
						'day' => 'Day',
						'week' => 'Week',
						'month' => 'Month',
						'year' => 'Year',
				),
		));
		
		// DisplayGroup: buttons
		$this->addDisplayGroup(array('send', 'max_send', 'period_send'), 'send_credit', array(
				'decorators' => array(
						'FormElements',
				)
		));
		
		// Receive Credit Settings
		$this->addElement('Radio', 'receive', array(
				'label'        => 'Receive credit from friends',
				'multiOptions' => array(
						1 => 'Yes, allow receiving.',
						0 => 'No, do not allow.',
				),
				'value' => 1,
		));
		
		// Max Receive Settings
		$this->addElement('Text', 'max_receive', array(
				'description'  => 'Maximum credits for receiving',
				'value' => 1500,
		));
		
		// Period Receive Settings
		$this->addElement('Select', 'period_receive', array(
				'description'  => 'Period',
				'multiOptions' => array(
						'day' => 'Day',
						'week' => 'Week',
						'month' => 'Month',
						'year' => 'Year',
				),
		));
		
		// DisplayGroup: buttons
		$this->addDisplayGroup(array('receive', 'max_receive', 'period_receive'), 'receive_credit', array(
				'decorators' => array(
						'FormElements',
				)
		));
		
		// Use Credit Settings
		$this->addElement('Radio', 'use_credit', array(
				'label'        => 'Enable/disable credits to this member level',
				'multiOptions' => array(
						1 => 'Yes, enable.',
						0 => 'No, disable.',
				),
				'value' => 1,
		));
		
		// General Info Settings
		$this->addElement('Radio', 'general_info', array(
				'label'        => 'Enable/disable page General Info',
				'multiOptions' => array(
						1 => 'Yes, enable.',
						0 => 'No, disable.',
				),
				'value' => 1,
		));
		
		// FAQ Settings
		$this->addElement('Radio', 'faq', array(
				'label'        => 'Enable/disable page FAQ',
				'multiOptions' => array(
						1 => 'Yes, enable.',
						0 => 'No, disable.',
				),
				'value' => 1,
		));
		
		// Add submit
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save Changes',
	      'type' => 'submit',
	      'ignore' => true,
	      'order' => 100000,
	    ));
		
	}
}