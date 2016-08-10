<?php
class Ynresume_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNRESUME_SETTINGS_LEVEL_DESCRIPTION');
        
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
                
                $this->addElement('Integer', 'resume_first_amount', array(
                    'label' => 'Credit for creating resume',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'resume_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'resume_credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'resume_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'resume_period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
                
                $this->addElement('Integer', 'recommendation_first_amount', array(
                    'label' => 'Credit for adding recommendation',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'recommendation_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'recommendation_credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'recommendation_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'recommendation_period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
                
                $this->addElement('Radio', 'use_credit', array(
                'label' => 'Allow users to use Credit',
                'multiOptions' => array(
                    1 => 'Yes, allow users to purchase "Who Viewed Me" service and feature resume by Credit.',
                    0 => 'No, do not allow users to purchase "Who Viewed Me" service and feature resume by Credit.'
                ),
                'value' => 1,
            ));
            }
    
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Resume',
                'multiOptions' => array(
                    1 => 'Yes, allow users to create new resume.',
                    0 => 'No, do not allow users to create new resume.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow Editing of Resume',
                'multiOptions' => array(
                    2 => 'Yes, allow users to edit all resumes.',
                    1 => 'Yes, allow users to edit their own resumes.',
                    0 => 'No, do not allow users to edit their own resumes.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->edit->options[2]);
            }
            
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Resume',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all resumes.',
                    1 => 'Yes, allow users to delete their own resumes.',
                    0 => 'No, do not allow users to delete their own resumes.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete->options[2]);
            }
            
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow Viewing Details of Resume',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view resumes.',
                    0 => 'No, do not allow users to view resumes.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'endorse', array(
                'label' => 'Allow Endorse a Skill',
                'multiOptions' => array(
                    1 => 'Yes, allow users to endorse a skill.',
                    0 => 'No, do not allow users to endorse a skill.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'recommend', array(
                'label' => 'Allow Giving a Recommendation',
                'multiOptions' => array(
                    1 => 'Yes, allow users to give a recommendation.',
                    0 => 'No, do not allow users to give a recommendation.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'service', array(
                'label' => 'Allow Using "Who Viewed Me" service',
                'multiOptions' => array(
                    1 => 'Yes, allow users to user "Who Viewed Me" service.',
                    0 => 'No, do not allow users to user "Who Viewed Me" service.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Integer', 'max_skill', array(
                'label' => 'Maximum Skills the user can add',
             //   'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 20,
            ));
            
            $this->addElement('Integer', 'max_friend', array(
                'label' => 'Maximum Friends the user can add the recommendation each time',
            //    'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 3,
            ));
            
            $this->addElement('Integer', 'max_photo', array(
                'label' => 'Maximum Photos the user can add for each section',
            //    'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 6,
            ));
            
            $roles = array(
                'everyone' => 'Everyone',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me'
            );
            
            $roles_values = array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner');
            $sections = Engine_Api::_()->ynresume()->getAllSections();
            if (isset($sections['photo'])) unset($sections['photo']);
            foreach ($sections as $key => $value) {
                $this->addElement('MultiCheckbox', 'auth_'.$key, array(
                    'label' => $value.' Privacy',
                    'description' => 'YNRESUME_AUTH_'.strtoupper($key).'_DESCRIPTION',
                    'multiOptions' => $roles,
                    'value' => $roles_values        
                ));
            }
        }
        else {
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow Viewing Details of Resume',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view resumes.',
                    0 => 'No, do not allow users to view resumes.'
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