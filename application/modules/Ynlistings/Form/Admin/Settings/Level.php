<?php
class Ynlistings_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNLISTINGS_SETTINGS_LEVEL_DESCRIPTION');
        
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
                    'label' => 'Add credit for creating listing',
                    'description' => 'No of first actions',
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
            }
    
    		$this->addElement('Integer', 'max_listings', array(
                'label' => 'Maximum Listings Can Be Added',
                'description' => '0 means unlimited',
                'required' => true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 100,
            ));
    		
            $this->addElement('Integer', 'publish_fee', array(
                'label' => 'Publish Listing Fee ($)',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 20,
            ));
            
    		
    		$this->addElement('Integer', 'feature_fee', array(
                'label' => 'Feature Listing Fee ($)',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 100,
            ));
    		
    		$this->addElement('Integer', 'feature_period', array(
                'description' => 'Period (days)',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => 5,
            ));
            
            $this->addElement('Radio', 'publish_credit', array(
                'label' => 'Can use credit to public and feature listing?',
                'multiOptions' => array(
                    1 => 'Yes. allow this member level to use credit to public and feature listing.',
                    0 => 'No. do not allow this member level to use credit to public and feature listing.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'view', array(
                'label' => 'Can View Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to view listings.',
                    0 => 'No, do not allow this member level to view listings.'
                ),
                'value' => 1,
            ));
            
    		$this->addElement('Radio', 'create', array(
                'label' => 'Can Create Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to create listings.',
                    0 => 'No, do not allow this member level to create listings.'
                ),
                'value' => 1,
            ));
    		
            $this->addElement('Radio', 'edit', array(
                'label' => 'Can Edit Listings?',
                'multiOptions' => array(
                    2 => 'Yes, allow this member level to edit all listings.',
                    1 => 'Yes, allow this member level to edit their listings.',
                    0 => 'No, do not allow this member level to edit their listings.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->edit->options[2]);
            }
    		
    		$this->addElement('Radio', 'delete', array(
                'label' => 'Can Delete Listings?',
                'multiOptions' => array(
                    2 => 'Yes, allow this member level to delete all listings.',
                    1 => 'Yes, allow this member level to delete their listings.',
                    0 => 'No, do not allow this member level to delete their listings.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete->options[2]);
            }  
    		
            $this->addElement('Radio', 'comment', array(
                'label' => 'Can Post Activities on Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to post activities on listings.',
                    0 => 'No, do not allow this member level to post activities on listings.'
                ),
                'value' => 1,
            ));
            
    		$this->addElement('Radio', 'follow', array(
                'label' => 'Can Follow Listing Owner?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to follow listing owner.',
                    0 => 'No, do not allow this member level to follow listing owner.'
                ),
                'value' => 1,
            ));
    		
            $this->addElement('Radio', 'select_theme', array(
                'label' => 'Can Select Theme for Own Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to select theme for their listings.',
                    0 => 'No, do not allow this member level to select theme for their listings.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'upload_photos', array(
                'label' => 'Can Upload Photos to Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to upload photos to listings.',
                    0 => 'No, do not allow this member level to upload photos to listings.'
                ),
                'value' => 1,
            ));
            
    		$this->addElement('Radio', 'upload_videos', array(
                'label' => 'Can Upload Videos to Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to upload videos to listings.',
                    0 => 'No, do not allow this member level to upload videos to listings.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'discussion', array(
                'label' => 'Can Add Discussion to Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to add discussion to listings.',
                    0 => 'No, do not allow this member level to add discussion to listings.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'approve', array(
                'label' => 'Approve Listings Before They Are Publicly Displayed?',
                'multiOptions' => array(
                    1 => 'Yes, approve listings of this member level before displaying.',
                    0 => 'No, no need to approve listings of this member level before displaying.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'print', array(
                'label' => 'Can Print Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to print listings.',
                    0 => 'No, do not allow this member level to print listings.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'import', array(
                'label' => 'Can Import Own Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to import their listings.',
                    0 => 'No, do not allow this member level to import their listings.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'export', array(
                'label' => 'Can Export Own Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to export their listings.',
                    0 => 'No, do not allow this member level to export their listings.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'report', array(
                'label' => 'Can Report Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to report listings.',
                    0 => 'No, do not allow this member level to report listings.'
                ),
                'value' => 1,
            ));
    		
    		$this->addElement('Radio', 'rate', array(
                'label' => 'Can Rate Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to rate listings.',
                    0 => 'No, do not allow this member level to rate listings.'
                ),
                'value' => 1,
            ));
    		
    		$roles = array(
    			  'everyone'            => 'Everyone',
                  'registered'          => 'All Registered Members',
                  'owner_network'       => 'Friends and Networks',
                  'owner_member_member' => 'Friends of Friends',
                  'owner_member'        => 'Friends Only',
                  'owner'               => 'Just Me',
    		);
    		
    		$roles_values = array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner');
    		
            $this->addElement('MultiCheckbox', 'view_listings', array(
    			'label' => 'Listing Viewing Options',
    			'description' => 'YNLISTINGS_VIEW_LISTINGS_DESCRIPTION',
    			'multiOptions' => $roles,
    			'value' => $roles_values		
    		));
    		
    		$this->addElement('MultiCheckbox', 'add_photos', array(
    			'label' => 'Photos Adding Options',
    			'description' => 'YNLISTINGS_ADD_PHOTOS_DESCRIPTION',
    			'multiOptions' => $roles,
    			'value' => $roles_values
    		
    		));
    		
    		$this->addElement('MultiCheckbox', 'add_videos', array(
    			'label' => 'Videos Adding Options',
    			'description' => 'YNLISTINGS_ADD_VIDEOS_DESCRIPTION',
    			'multiOptions' => $roles,
    			'value' => $roles_values
    		));
    		
            $this->addElement('MultiCheckbox', 'add_discussions', array(
                'label' => 'Discussions Adding Options',
                'description' => 'YNLISTINGS_ADD_DISCUSSIONS_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => $roles_values
            ));
    		
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Activities Posting Options',
                'description' => 'YNLISTINGS_POST_ACTIVITIES_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => $roles_values
            ));
            
            $this->addElement('MultiCheckbox', 'sharing', array(
                'label' => 'Sharing Options',
                'description' => 'YNLISTINGS_SHARING_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => $roles_values
            ));
            
    		$this->addElement('MultiCheckbox', 'printing', array(
    			'label' => 'Printing Options',
    			'description' => 'YNLISTINGS_PRINTING_DESCRIPTION',
    			'multiOptions' => $roles,
    			'value' => $roles_values
    		));
    		
        }
        else {
            $this->addElement('Radio', 'view', array(
                'label' => 'Can View Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to view listings.',
                    0 => 'No, do not allow this member level to view listings.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'print', array(
                'label' => 'Can Print Listings?',
                'multiOptions' => array(
                    1 => 'Yes, allow this member level to print listings.',
                    0 => 'No, do not allow this member level to print listings.'
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