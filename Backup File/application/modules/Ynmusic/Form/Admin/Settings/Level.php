<?php
class Ynmusic_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {
	public function init() {
		$this -> setTitle('Member Level Settings') -> setDescription('YNMUSIC_SETTINGS_LEVEL_DESCRIPTION');
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		
		$roleOptions = array(
			'everyone' => 'Everyone', 
			'registered' => 'All Registered Members', 
			'owner_network' => 'Friends and Networks', 
			'owner_member_member' => 'Friends of Friends', 
			'owner_member' => 'Friends Only', 
			'owner' => 'Just Me'
		);
		
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
		
		$types = array ('song', 'album', 'playlist');
		$types2 = array (
			'playlist' => 'Playlists',
			'album' => 'Albums',
		);
		if( !$this->isPublic() ) {
			if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
				foreach ($types as $type) {
					$this->addElement('Integer', $type.'_first_amount', array(
	                    'label' => 'Credit for adding new '.$type,
	                    'description' => 'No of First Actions',
	                    'required' =>true,
	                    'validators' => array(
	                        new Engine_Validate_AtLeast(0),
	                    ),
	                    'value' => 0,
	                ));
	                
	                $this->addElement('Integer', $type.'_first_credit', array(
	                    'description' => 'Credit/Action',
	                    'required' =>true,
	                    'validators' => array(
	                        new Engine_Validate_AtLeast(0),
	                    ),
	                    'value' => 0,
	                ));
	                
	                $this->addElement('Integer',  $type.'_credit', array(
	                    'description' => 'Credit for next action',
	                    'required' =>true,
	                    'validators' => array(
	                        new Engine_Validate_AtLeast(0),
	                    ),
	                    'value' => 0,
	                ));
	                $this->addElement('Integer',  $type.'_max_credit', array(
	                    'description' => 'Max Credit/Period',
	                    'required' =>true,
	                    'validators' => array(
	                        new Engine_Validate_AtLeast(0),
	                    ),
	                    'value' => 0,
	                ));
	                $this->addElement('Integer',  $type.'_period', array(
	                    'description' => 'Period (days)',
	                    'required' =>true,
	                    'validators' => array(
	                        new Engine_Validate_AtLeast(1),
	                    ),
	                    'value' => 1,
	                ));
				}
			}
			
			foreach ($types2 as $key => $value) {
				$this->addElement('Radio', $key.'_create', array(
					'label' => 'Allow Creation of '.$value,
					'multiOptions' => array(
	                    1 => 'Yes, allow users to create new '.strtolower($value),
	                    0 => 'No, do not allow users to create new '.strtolower($value)
	                ),
                	'value' => 1,
				));
				
				$name = $key.'_edit';
				$this->addElement('Radio', $name, array(
	                'label' => 'Allow Editing of '.$value,
	                'multiOptions' => array(
	                    2 => 'Yes, allow users to edit all '.strtolower($value). ', even private ones',
	                    1 => 'Yes, allow users to edit their own '.strtolower($value),
	                    0 => 'No, do not allow users to edit their own '.strtolower($value)
	                ),
	                'value' => ( $this->isModerator() ? 2 : 1 ),
	            ));
	            if( !$this->isModerator() ) {
	                unset($this->$name->options[2]);
	            }
				
				$name = $key.'_delete';
				$this->addElement('Radio', $name, array(
	                'label' => 'Allow Deletion of '.$value,
	                'multiOptions' => array(
	                    2 => 'Yes, allow users to delete all '.strtolower($value). ', even private ones',
	                    1 => 'Yes, allow users to delete their own '.strtolower($value),
	                    0 => 'No, do not allow users to delete their own '.strtolower($value)
	                ),
	                'value' => ( $this->isModerator() ? 2 : 1 ),
	            ));
	            if( !$this->isModerator() ) {
	                unset($this->$name->options[2]);
	            }
				
				$this->addElement('Radio', $key.'_view', array(
					'label' => 'Allow Viewing of '.$value,
					'multiOptions' => array(
						2 => 'Yes, allow users to view all '.strtolower($value). ', even private ones',
	                    1 => 'Yes, allow users to view '.strtolower($value),
	                    0 => 'No, do not allow users to view '.strtolower($value)
	                ),
					'value' => ( $this->isModerator() ? 2 : 1 ),
				));
				
				$this->addElement('Radio', $key.'_comment', array(
					'label' => 'Allow Commenting on '.$value,
					'multiOptions' => array(
						2 => 'Yes, allow users to comment on all '.strtolower($value). ', even private ones',
	                    1 => 'Yes, allow users to comment on '.strtolower($value),
	                    0 => 'No, do not allow users to comment on '.strtolower($value)
	                ),
					'value' => ( $this->isModerator() ? 2 : 1 ),
				));
				
				$this->addElement('Integer', $key.'_max_songs', array(
	                'label' => 'Maximum Songs Per '.$value,
	                'description' => 'Set 0 is unlimited',
	                'required' =>true,
	                'validators' => array(
	                    new Engine_Validate_AtLeast(0),
	                ),
	                'value' => 20,
	            ));
			}

			$this->addElement('Radio', 'album_download', array(
				'label' => 'Allow Download Albums',
				'multiOptions' => array(
					2 => 'Yes, allow users to download all albums, even private ones.',
					1 => 'Yes, allow users to download albums',
					0 => 'No, do not allow users to download albums'
				),
				'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			if( !$this->isModerator() ) {
				unset($this->album_download->options[2]);
			}

			$this->addElement('Radio', 'song_create', array(
				'label' => 'Allow Creation of Standalone Songs',
				'multiOptions' => array(
                    1 => 'Yes, allow users to create new standalone songs',
                    0 => 'No, do not allow users to create new standalone songs'
                ),
            	'value' => 1,
			));
			
			$this->addElement('Radio', 'song_edit', array(
                'label' => 'Allow Editing of Songs',
                'multiOptions' => array(
                    2 => 'Yes, allow users to edit all songs, even private ones.',
                    1 => 'Yes, allow users to edit their own songs',
                    0 => 'No, do not allow users to edit their own songs'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->song_edit->options[2]);
            }
			
			$this->addElement('Radio', 'song_delete', array(
                'label' => 'Allow Deletion of Songs',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all songs, even private ones.',
                    1 => 'Yes, allow users to delete their own songs',
                    0 => 'No, do not allow users to delete their own songs'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->song_delete->options[2]);
            }
			
			$this->addElement('Radio', 'song_view', array(
				'label' => 'Allow Viewing of Songs',
				'multiOptions' => array(
					2 => 'Yes, allow users to view all songs, even private ones.',
                    1 => 'Yes, allow users to view songs',
                    0 => 'No, do not allow users to view songs'
                ),
				'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			if( !$this->isModerator() ) {
				unset($this->song_view->options[2]);
			}
			
			$this->addElement('Radio', 'song_comment', array(
				'label' => 'Allow Commenting of Songs',
				'multiOptions' => array(
					2 => 'Yes, allow users to comment on all songs, even private ones.',
                    1 => 'Yes, allow users to comment on songs',
                    0 => 'No, do not allow users to comment on songs'
                ),
				'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			if( !$this->isModerator() ) {
				unset($this->song_comment->options[2]);
			}

			$this->addElement('Radio', 'song_download', array(
				'label' => 'Allow Download of Songs',
				'multiOptions' => array(
					2 => 'Yes, allow users to download all songs, even private ones.',
					1 => 'Yes, allow users to download songs',
					0 => 'No, do not allow users to download songs'
				),
				'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			if( !$this->isModerator() ) {
				unset($this->song_download->options[2]);
			}
			
			$this->addElement('Integer', 'song_max_filesize', array(
                'label' => 'Maximum Filesize',
                'description' => 'Maximum filesize can be uploaded (KB)?',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => 10240,
            ));
			
			$this->addElement('Integer', 'song_max_storage', array(
                'label' => 'Maximum Storage',
                'description' => 'Maximum file storage for each user (KB)?',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => 1048576,
            ));
			
			foreach ($types2 as $key => $value) {
				$this->addElement('MultiCheckbox', $key.'_auth_view', array(
	        		'label' => ucfirst($key).' View Privacy',
	        		'description' => 'YNMUSIC_AUTH_VIEW_DESCRIPTION_'.strtoupper($key),
	        		'multiOptions' => $roleOptions,
	        		'value' => array_keys($roleOptions),
	      		));

				$this->addElement('MultiCheckbox', $key.'_auth_comment', array(
	        		'label' => ucfirst($key).' Comment Privacy',
	        		'description' => 'YNMUSIC_AUTH_COMMENT_DESCRIPTION_'.strtoupper($key),
	        		'multiOptions' => $roleOptions,
	        		'value' => array_keys($roleOptions),
	      		));

				if ($key != 'playlist') {
					$this->addElement('MultiCheckbox', $key.'_auth_download', array(
						'label' => ucfirst($key).' Download Privacy',
						'description' => 'YNMUSIC_AUTH_DOWNLOAD_DESCRIPTION_'.strtoupper($key),
						'multiOptions' => $roleOptions,
						'value' => array_keys($roleOptions),
					));
				}
			}

			$this->addElement('MultiCheckbox', 'song_auth_view', array(
        		'label' => 'Song View Privacy',
        		'description' => 'YNMUSIC_AUTH_VIEW_DESCRIPTION_SONG',
        		'multiOptions' => $roleOptions,
        		'value' => array_keys($roleOptions),
      		));
			
			$this->addElement('MultiCheckbox', 'song_auth_comment', array(
        		'label' => 'Song Comment Privacy',
        		'description' => 'YNMUSIC_AUTH_COMMENT_DESCRIPTION_SONG',
        		'multiOptions' => $roleOptions,
        		'value' => array_keys($roleOptions),
      		));

			$this->addElement('MultiCheckbox', 'song_auth_download', array(
				'label' => 'Song Download Privacy',
				'description' => 'YNMUSIC_AUTH_DOWNLOAD_DESCRIPTION_SONG',
				'multiOptions' => $roleOptions,
				'value' => array_keys($roleOptions),
			));
		}	
		else {
			$types2['song'] = 'Songs';
			foreach ($types2 as $key => $value) {
				$this->addElement('Radio', $key.'_view', array(
					'label' => 'Allow Viewing of '.$value,
					'multiOptions' => array(
	                    1 => 'Yes, allow users to view '.strtolower($value),
	                    0 => 'No, do not allow users to view '.strtolower($value)
	                ),
                	'value' => 1,
				));
			}
		}
		
		$this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true,
          'order' => 999
        ));  
	}
}
