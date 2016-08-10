<?php
class Ynsocialads_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNSOCIALADS_FORM_ADMIN_SETTINGS_LEVEL_DESCRIPTION');
        
        $levels = array();
        $table  = Engine_Api::_()->getDbtable('levels', 'authorization');
        foreach ($table->fetchAll($table->select()) as $row)
            $levels[$row['level_id']] = $row['title'];
        
        $this->addElement('Select', 'level_id', array(
            'label' => 'Member Level',
            'multiOptions' => $levels,
            'ignore' => true
        ));
        
        $this->addElement('Integer', 'min_amount', array(
            'label' => 'Minimum amount of money request',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 20,
        ));
        
        $this->addElement('Integer', 'max_amount', array(
            'label' => 'Maximum amount of money request',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 100,
        ));
        
        $this->addElement('Radio', 'create', array(
            'label' => 'Allow Create of Ad?',
            'multiOptions' => array(
                1 => 'Yes, allow this member level to create ad.',
                0 => 'No, do not allow this member level to create ad.'
            ),
            'value' => 1,
        ));
        
        $this->addElement('Radio', 'edit', array(
            'label' => 'Allow Editing of Ad?',
            'multiOptions' => array(
                2 => 'Yes, allow this member level to edit all ads.',
                1 => 'Yes, allow this member level to edit their ads.',
                0 => 'No, do not allow this member level to edit their ads.'
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if( !$this->isModerator() ) {
            unset($this->edit->options[2]);
        } 
        
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing detail of Ad?',
            'multiOptions' => array(
                1 => 'Yes, allow this member level to view detail of ad.',
                0 => 'No, do not allow this member level to view detail of ad.'
            ),
            'value' => 1,
        ));
        
        $this->addElement('Radio', 'approve', array(
            'label' => 'Approve ads of this member level before they are publicly displayed?',
            'multiOptions' => array(
                1 => 'Yes, approve ads of this member level before they are publicly displayed.',
                0 => 'No, no need to approve ads of this member level before they are publicly displayed.'
            ),
            'value' => 1,
        ));
        
        $this->addElement('Radio', 'pay_later', array(
            'label' => 'Allow this member level to pay ad by Pay Later Method?',
            'multiOptions' => array(
                1 => 'Yes, allow this member level to pay ad by Pay Later Method.',
                0 => 'No, do not allow this member level to pay ad by Pay Later Method.'
            ),
            'value' => 1,
        ));
        
        $this->addElement('Radio', 'virtual_money', array(
            'label' => 'Allow this member level to pay ad by Virtual Money?',
            'multiOptions' => array(
                1 => 'Yes, allow this member level to pay ad by Virtual Money.',
                0 => 'No, do not allow this member level to pay ad by Virtual Money.'
            ),
            'value' => 1,
        ));
        
        if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                
            $this->addElement('Radio', 'pay_credit', array(
                'label' => 'Allow this member level to pay ad by Credit?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to pay ad by Credit.',
                    0 => 'No, do not allow this member level to pay ad by Credit.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Integer', 'first_amount', array(
                'label' => 'Add credit for creating ad',
                'description' => 'No of first actions',
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 2,
            ));
            
            $this->addElement('Integer', 'first_credit', array(
                'description' => 'Credit/Action',
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 10,
            ));
            
            $this->addElement('Integer', 'credit', array(
                'description' => 'Credit for next action',
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 5,
            ));
            $this->addElement('Integer', 'max_credit', array(
                'description' => 'Max Credit/Period',
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 100,
            ));
            $this->addElement('Integer', 'period', array(
                'description' => 'Period (days)',
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 1,
            ));
        }
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));       
    }
}