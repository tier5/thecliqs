<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {
	public function init() {
		parent::init ();

		// My stuff
		$this->setTitle ( 'Member Level Settings' )->setDescription ("FILESHARING_FORM_ADMIN_LEVEL_DESCRIPTION" );
		$view = Zend_Registry::get('Zend_View');
		$url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $view  -> url(array('controller' => 'level', 'action' => 'edit'), 'admin_default', true);
		$this -> addNotice($view->translate('These settings about max total size will be limited by <a href = "%s">Storage Quota</a>.',$url));
		// Element: view
		$this->addElement ( 'Radio', 'view', array (
			'label' => 'Allow Viewing of Folders?',
			'description' => 'Do you want to let members view folders? If set to no, some other settings on this page may not apply.',
			'multiOptions' => array (
				2 => 'Yes, allow viewing of all folders, even private ones.',
				1 => 'Yes, allow viewing of folders.',
				0 => 'No, do not allow folders to be viewed.'
			),
			'value' => ($this->isModerator () ? 2 : 1)
		) );
		if (! $this->isModerator ()) {
			unset ( $this->view->options [2] );
		}

		if (! $this->isPublic ()) {

			// Element: create
			$this->addElement ( 'Radio', 'create', array (
					'label' => 'Allow Creation of Folders?',
					'description' => 'Do you want to let members create folders? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view folders, but only want certain levels to be able to create folders.',
					'multiOptions' => array (
						2 => 'Yes, allow creation of folders, even private ones.',
						1 => 'Yes, allow creation of folders.',
						0 => 'No, do not allow folders to be created.'
					),
					'value' => ($this->isModerator () ? 2 : 1)
			) );
			if (! $this->isModerator ()) {
				unset ( $this->create->options [2] );
			}

			// Element: edit
			$this->addElement ( 'Radio', 'edit', array (
					'label' => 'Allow Editing of Folders?',
					'description' => 'Do you want to let members edit folders? If set to no, some other settings on this page may not apply.',
					'multiOptions' => array (
						2 => 'Yes, allow members to edit all folders.',
						1 => 'Yes, allow members to edit their own folders.',
						0 => 'No, do not allow members to edit their folders.'
					),
					'value' => ($this->isModerator () ? 2 : 1)
			) );
			if (! $this->isModerator ()) {
				unset ( $this->edit->options [2] );
			}

			// Element: delete
			$this->addElement ( 'Radio', 'delete', array (
				'label' => 'Allow Deletion of Folders?',
				'description' => 'Do you want to let members delete folders? If set to no, some other settings on this page may not apply.',
				'multiOptions' => array (
					2 => 'Yes, allow members to delete all folders.',
					1 => 'Yes, allow members to delete their own folders.',
					0 => 'No, do not allow members to delete their folders.'
				),
				'value' => ($this->isModerator () ? 2 : 1)
			) );
			if (! $this->isModerator ()) {
				unset ( $this->delete->options [2] );
			}

			// Element: comment
			$this->addElement ( 'Radio', 'comment', array (
				'label' => 'Allow Commenting on Folders?',
				'description' => 'Do you want to let members of this level comment on folders?',
				'multiOptions' => array (
					2 => 'Yes, allow members to comment on all folders, including private ones.',
					1 => 'Yes, allow members to comment on folders.',
					0 => 'No, do not allow members to comment on folders.'
				),
				'value' => ($this->isModerator () ? 2 : 1)
			) );
			if (! $this->isModerator ()) {
				unset ( $this->comment->options [2] );
			}

			// Element: auth_ext
			$this->addElement ( 'Text', 'auth_ext', array (
				'label' => 'File extensions?',
				'filters' => array(
					'StringTrim'
				),
				'description' => 'If you want to allow specific file extensions, you can enter them below (separated by commas). Example: txt, pdf, ppt, doc. Leave * for any file type',
				'value' => '*'
			) );
			// Max number of files
		$this -> addElement('Text', 'userfile', array(
			'label' => 'Max number of files',
			'description' => 'How many files will be created? (leave 0 for unlimited)',
			'value' => 0,
			'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
		));
		// Max file size
		$this -> addElement('Text', 'usersize', array(
			'label' => 'Max file size',
			'description' => 'What is the maximum file size (Kb) that can be uploaded? (leave 0 for unlimited)',
			'value' => 0,
			'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
		));
		// Max total size
		
		$this -> addElement('Text', 'usertotal', array(
			'label' => 'Max total size',
			'description' => 'What is the total size (Kb) that can be uploaded? (leave 0 for unlimited).',
			'value' => 0,
			'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
		));
        
        $roles = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me',
        );
        
        $roles_values = array_keys($roles);
        $auths = array('view', 'create', 'edit', 'delete', 'comment');
        $count = 0;
        foreach ($auths as $auth) {
            if ($count == 1) {
                unset($roles['everyone']);
                unset($roles_values[0]);
            }
            $this->addElement('MultiCheckbox', 'auth_'.$auth, array(
                'label' => ucfirst($auth).' Privacy',
                'description' => 'YNFILESHARING_AUTH_'.strtoupper($auth).'_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => $roles_values        
            ));
            $count ++;
        }
		}
	}
}