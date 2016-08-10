<?php


class Yncontest_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
	$currency = Yncontest_Api_Core::getDefaultCurrency();
	
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
     $this->addElement('Radio', 'yncontest_mode', array(
        'label' => '*Enable Test Mode?',
        'description' => 'Allow admin to test Contest by using development mode?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('yncontest.mode', 1),
      )); 
	/*
	$this->addElement('Text', 'contest_maxfeature',array(
	      'label'=>'Contests are shown on the site',
	      'title' => '',  
	      'description' => 'Maximum featured contests are shown on the site at one time',
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('yncontest.maxfeature', 1),
    ));  
	*/
	$this->addElement('Text', 'yncontest_endingsoonbefore',array(
	      'label'=>'Ending soon contests before',
	      'title' => '',  
	      'description' => 'days',
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('yncontest.endingsoonbefore', 1),
    ));    
	
	$this->addElement('Text', 'contest_page',array(
	      'label'=>'Items per page',
	      'title' => '',  
	      'description' => 'How many items will show per page? (Enter a number between 1 and 100)',
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 1),
    ));    
	
	$this->addElement('Radio', 'contest_approval', array(
        'label' => 'Approval setting',
        'description' => '',
        'multiOptions' => array(
          1 => 'Auto-Approved',
          0 => 'Pending for Approved'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.approval', 0),
      ));
	  
	/*
	$this->addElement('select', 'yncontest_currency', array(
        'label' => 'Default Currency',
        'required'=>true,
        'multiOptions' => Yncontest_Model_DbTable_Currencies::getMultiOptions(),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('yncontest.currency', 'USD'),
               	        		    
     ));  
     */
	/************************************************/
	 
			
			
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}