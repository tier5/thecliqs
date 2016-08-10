<?php
class Ynjobposting_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNJOBPOSTING_SETTINGS_LEVEL_DESCRIPTION');
        
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
                
            	$this->addElement('Integer', 'company_first_amount', array(
                    'label' => 'Credit for creating companies',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'company_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'company_credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'company_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'company_period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
                
                $this->addElement('Integer', 'job_first_amount', array(
                    'label' => 'Credit for creating jobs',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'job_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'job_credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'job_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'job_period', array(
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
    
			$this->addElement('Radio', 'create_company', array(
                'label' => 'Allow Creation of Company',
                'multiOptions' => array(
                    1 => 'Yes, allow users to create new companies.',
                    0 => 'No, do not allow users to create new companies.'
                ),
                'value' => 1,
            ));
			
			$this->addElement('Radio', 'edit_company', array(
                'label' => 'Allow Editing of Company',
                'multiOptions' => array(
                    2 => 'Yes, allow users to edit all companies.',
                    1 => 'Yes, allow users to edit their own companies.',
                    0 => 'No, do not allow users to edit their own companies.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->edit_company->options[2]);
            }
			
			$this->addElement('Radio', 'view_company', array(
                'label' => 'Allow Viewing Details of Company',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view companies.',
                    0 => 'No, do not allow users to view companies.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'comment_company', array(
                'label' => 'Allow Commenting on Company',
                'multiOptions' => array(
                    1 => 'Yes, allow users to comment on companies.',
                    0 => 'No, do not allow users to comment on companies.'
                ),
                'value' => 1,
            ));
			
            $this->addElement('Radio', 'delete_company', array(
                'label' => 'Allow Deletion of Company',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all companies.',
                    1 => 'Yes, allow users to delete their own companies.',
                    0 => 'No, do not allow users to delete their own companies.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete_company->options[2]);
            }
            
            $this->addElement('Radio', 'close_company', array(
                'label' => 'Allow Closing of Company',
                'multiOptions' => array(
                    2 => 'Yes, allow users to close all companies.',
                    1 => 'Yes, allow users to close their own companies.',
                    0 => 'No, do not allow users to close their own companies.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->close_company->options[2]);
            }
            
            $this->addElement('Radio', 'sponsor_company', array(
                'label' => 'Allow Sponsoring on Company',
                'multiOptions' => array(
                    2 => 'Yes, allow users to sponsor all companies.',
                    1 => 'Yes, allow users to sponsor their own companies.',
                    0 => 'No, do not allow users to sponsor their own companies.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->sponsor_company->options[2]);
            }
            
            $this->addElement('Integer', 'max_company', array(
                'label' => 'Maximum Companies the user can create',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 3,
            ));
            
			$roles = array(
                'everyone' => 'Everyone', 
	            'registered' => 'All Registered', 
	            'network' => 'My Network', 
	            'owner_member' => 'My Friends', 
	            'owner' => 'Only Me'
            );
            
			$this->addElement('MultiCheckbox', 'auth_view_company', array(
                'label' => 'View Privacy of Company',
                'description' => 'YNJOBPOSTING_COMPANY_AUTH_VIEW_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => array_keys($roles)        
            ));
			
			$this->addElement('Radio', 'create_job', array(
                'label' => 'Allow Creation of Job',
                'multiOptions' => array(
                    1 => 'Yes, allow users to create new jobs.',
                    0 => 'No, do not allow users to create new jobs.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'edit_job', array(
                'label' => 'Allow Editing of Job',
                'multiOptions' => array(
                    2 => 'Yes, allow users to edit all jobs.',
                    1 => 'Yes, allow users to edit their own jobs.',
                    0 => 'No, do not allow users to edit their own jobs.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->edit_job->options[2]);
            }
            
            $this->addElement('Radio', 'view_job', array(
                'label' => 'Allow Viewing Details of Job',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view jobs.',
                    0 => 'No, do not allow users to view jobs.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'comment_job', array(
                'label' => 'Allow Commenting on Job',
                'multiOptions' => array(
                    1 => 'Yes, allow users to comment on jobs.',
                    0 => 'No, do not allow users to comment on jobs.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'delete_job', array(
                'label' => 'Allow Deletion of Job',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all jobs.',
                    1 => 'Yes, allow users to delete their own jobs.',
                    0 => 'No, do not allow users to delete their own jobs.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete_job->options[2]);
            }
            
            $this->addElement('Radio', 'end_job', array(
                'label' => 'Allow Ending of Job',
                'multiOptions' => array(
                    2 => 'Yes, allow users to end all jobs.',
                    1 => 'Yes, allow users to end their own jobs.',
                    0 => 'No, do not allow users to end their own jobs.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->end_job->options[2]);
            }
            
            $this->addElement('Radio', 'apply_job', array(
                'label' => 'Allow Application for Job',
                'multiOptions' => array(
                    1 => 'Yes, allow users to apply for jobs.',
                    0 => 'No, do not allow users to apply for jobs.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'autoapprove_job', array(
                'label' => 'Allow auto approve jobs created by these users?',
                'multiOptions' => array(
                    1 => 'Yes, allow auto approve.',
                    0 => 'No, do not allow auto approve.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Integer', 'max_job', array(
                'label' => 'Maximum Jobs the user can create',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 3,
            ));
			
			$this->addElement('MultiCheckbox', 'auth_view_job', array(
                'label' => 'View Privacy of Job',
                'description' => 'YNJOBPOSTING_JOB_AUTH_VIEW_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => array_keys($roles)        
            ));
			
			$this->addElement('MultiCheckbox', 'auth_comment_job', array(
                'label' => 'Comment Privacy of Job',
                'description' => 'YNJOBPOSTING_JOB_AUTH_COMMENT_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => array_keys($roles)        
            ));
        }
        else {
            $this->addElement('Radio', 'view_company', array(
                'label' => 'Allow Viewing Details of Company',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view companies.',
                    0 => 'No, do not allow users to view companies.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'view_job', array(
                'label' => 'Allow Viewing Details of Job',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view jobs.',
                    0 => 'No, do not allow users to view jobs.'
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