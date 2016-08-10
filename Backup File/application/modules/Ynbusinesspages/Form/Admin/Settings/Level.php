<?php
class Ynbusinesspages_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNBUSINESSPAGES_SETTINGS_LEVEL_DESCRIPTION');
        
        $levels = array();
        $table  = Engine_Api::_()->getDbtable('levels', 'authorization');
        foreach ($table->fetchAll($table->select()) as $row) {
            $levels[$row['level_id']] = $row['title'];
		}
		
        $this->addElement('Select', 'level_id', array(
            'label' => 'Member Level',
            'multiOptions' => $levels,
            'ignore' => true
        ));
        if( !$this->isPublic() ) {
    		if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                
            	$this->addElement('Integer', 'first_amount', array(
                    'label' => 'Credit for creating business',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
                
                $this->addElement('Radio', 'use_credit', array(
                        'label' => 'Allow users to use Credit to purchase Package',
                        'multiOptions' => array(
                            1 => 'Yes, allow users to purchase Package by Credit.',
                            0 => 'No, do not allow users to purchase Package by Credit.'
                        ),
                        'value' => 1,
                ));
            }
    
			$this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Business',
                'multiOptions' => array(
                    1 => 'Yes, allow users to create new businesses.',
                    0 => 'No, do not allow users to create new businesses.'
                ),
                'value' => 1,
            ));
			
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Business',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all businesses.',
                    1 => 'Yes, allow users to delete their own businesses.',
                    0 => 'No, do not allow users to delete their own businesses.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete->options[2]);
            }
            
            $this->addElement('Radio', 'claim', array(
                'label' => 'Allow Claiming on Business',
                'multiOptions' => array(
                    1 => 'Yes, allow users to claim businesses.',
                    0 => 'No, do not allow users to claim businesses.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'rate', array(
                'label' => 'Allow Rating on Business',
                'multiOptions' => array(
                    1 => 'Yes, allow users to rate businesses.',
                    0 => 'No, do not allow users to rate businesses.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'autoapprove', array(
                'label' => 'Allow auto approve business created by these users?',
                'multiOptions' => array(
                    1 => 'Yes, allow auto approve.',
                    0 => 'No, do not allow auto approve.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Integer', 'max', array(
                'label' => 'Maximum Businesses the user can own',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 3,
            ));
        }
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));        
    }
}