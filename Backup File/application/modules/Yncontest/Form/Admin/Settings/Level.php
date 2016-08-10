<?php

class Yncontest_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
	public function init()
	{
		parent::init();

		// My stuff
		$this -> setTitle('Member Level Settings') -> setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

		// Element: view
		$this -> addElement('Radio', 'view', array(
				'label' => 'Allow viewing of contests',
				'description' => 'Do you want to let members view contests? If set to no, some other settings on this page may not apply.',
				'multiOptions' => array(
						1 => 'Yes, allow members to view.',
						0 => 'No, do not allow contests to be viewed',
				),
				'value' => 1,
		));
		/*
		$this -> addElement('Radio', 'viewentries', array(
				'label' => 'Allow viewing of entries',
				'description' => 'Do you want to let members view entries? If set to no, some other settings on this page may not apply.',
				'multiOptions' => array(
						1 => 'Yes, allow members to view.',
						0 => 'No, do not allow entries to be viewed',
				),
				'value' => 1,
		));
		*/
		
		//Check public level
		if (!$this -> isPublic())
		{
			$this -> addElement('Radio', 'createcontests', array(
				'label' => 'Allow creation of contests',
				'description' => 'Do you want to let members create contest? If set to no, some other settings on this page may not apply.This is useful if you want members to be able create contests but only want certian levels to be able create contests',
				'multiOptions' => array(
						1 => 'Yes, allow creation of contest',
						0 => 'No, do not allow contest to be created',
				),
				'value' => 1,
			));

			$this -> addElement('Radio', 'createentries', array(
					'label' => 'Allow creation of entries',
					'description' => 'Do you want to let members create entries? If set to no, some other settings on this page may not apply.This is useful if you want members to be able create entries but only want certian levels to be able create entries',
					'multiOptions' => array(							
							1 => 'Yes, allow creation of entries',
							0 => 'No, do not allow entry to be created',
					),
					//'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			

			$this -> addElement('Radio', 'editcontests', array(
					'label' => 'Allow editing of contests',
					'description' => 'Do you want to let members edit contest? If set to no, some other settings on this page may not apply.',
					'multiOptions' => array(							
							1 => 'Yes, allow members to edit their own contests',
							0 => 'No, do not allow members to edit their contests',
					),
					//'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			
			
			$this -> addElement('Radio', 'deletecontests', array(
					'label' => 'Allow deletion of contests',
					'description' => 'Do you want to let members delete contest? If set to no, some other settings on this page may not apply.',
					'multiOptions' => array(
							//2 => 'Yes, allow members edit contests , even private ones.',
							1 => 'Yes, allow members to delete their own contests',
							0 => 'No, do not allow members to delete their contests',
					),
					//'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			

			$this -> addElement('Radio', 'comment', array(
					'label' => 'Allow commenting on contests',
					'description' => 'Do you want to let members comment on contests ?',
					'multiOptions' => array(
							//2 => 'Yes, allow members comment on contests , even private ones.',
							1 => 'Yes, allow members to comment on contests ',
							0 => 'No, do not allow members to comment on contests',
					),
					//'value' => ( $this->isModerator() ? 2 : 1 ),
			));

			$this -> addElement('Radio', 'commententries', array(
					'label' => 'Allow commenting on entries',
					'description' => 'Do you want to let members comment on entries ?',
					'multiOptions' => array(
							//2 => 'Yes, allow members comment on entries , even private ones.',
							1 => 'Yes, allow members to comment on entries',
							0 => 'No, do not allow members to comment on entries',
					),
					//'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			

			$this -> addElement('Radio', 'voteentries', array(
					'label' => 'Allow voting on entries',
					'description' => 'Do you want to let members vote on entries ?',
					'multiOptions' => array(
							//2 => 'Yes, allow members vote on entries , even private ones.',
							1 => 'Yes, allow members to vote on entries',
							0 => 'No, do not allow members to vote on entries',
					),
					//'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			
			$this -> addElement('Text', 'max_entries', array(
					'label' => 'Maximum entries which a member can submit',
					'description' => '(0 means no limit)',
					'validators' => array(
							array(
									'Int',
									true
							),
							new Engine_Validate_AtLeast(0),
					),
					'value' => '0'
			));
			
			//publicfee
			$this -> addElement('Text', 'publishC_fee', array(
					'label' => 'Publishing fee',
					'description' => '(Set 0 to free)',
					'validators' => array(
							array(
									'Int',
									true
							),
							new Engine_Validate_AtLeast(0),
					),
					'value' => '0'
			));
			//featurefee
			$this -> addElement('Text', 'featureC_fee', array(
					'label' => 'Feature Contest fee',
					'description' => '(Set 0 to free)',
					'validators' => array(
							array(
									'Int',
									true
							),
							new Engine_Validate_AtLeast(0),
					),
					'value' => '0'
			));
			//mediumfee
			$this -> addElement('Text', 'premiumC_fee', array(
					'label' => 'Medium Contest fee',
					'description' => '(Set 0 to free)',
					'validators' => array(
							array(
									'Int',
									true
							),
							new Engine_Validate_AtLeast(0),
					),
					'value' => '0'
			));
			//endingfee
			$this -> addElement('Text', 'endingsoonC_fee', array(
					'label' => 'Ending Soon Contest fee',
					'description' => '(Set 0 to free)',
					'validators' => array(
							array(
									'Int',
									true
							),
							new Engine_Validate_AtLeast(0),
					),
					'value' => '0'
			));

		}//end !isPublic
	}
}
