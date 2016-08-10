<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Form_Folder extends Engine_Form {
	protected $_parentType;
	protected $_parentId;
	protected $_folder;

	public function setFolder($value) {
		if ($value instanceof Ynfilesharing_model_Folder) {
			$this->_parentType = $value->parent_type;
			$this->_parentId = $value->getIdentity();
			$this->_folder = $value;
		}
	}
	public function setParentType($value) {
		$this->_parentType = $value;
	}

	public function setParentId($value) {
		$this->_parentId = $value;
	}

	protected $_roles;

	public function init() {
		$view = $this->getView(); 
		$view->headScript()
			->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
			->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
			->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
			->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
		
		$this->addElement('Text', 'title', array(
			'label' => 'Title',
			'maxlength' => '256',
			'allowEmpty' => false,
			'required' => true,
			'filters' => array(
               'StripTags',
				new Engine_Filter_Censor(),
				new Engine_Filter_StringLength(array('max' => '256')),
			)
		));
		
		// init tag
		$this->addElement('Text', 'tags', array(
				'label' => 'Tags (Keywords)',
				'description' => 'Separate tags with commas.',
				'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
				)
		));
		$this->tags->getDecorator("Description")->setOption("placement", "append");
		

		if ($this->_parentType == 'group') {
			$this->_roles = array(
				'everyone' => 'Everyone',
				'registered' => 'All Registered Members',
				'parent_member' => 'Group Members',
				'owner' => 'Group Owner',
			);
		} 
		else if($this->_parentType == 'event') {
			$this->_roles = array(
				'everyone' => 'Everyone',
				'registered' => 'All Registered Members',
				'parent_member' => 'Event Members',
				'owner' => 'Event Owner',
			);
		}
		else {
			$this->_roles = array(
				'everyone' => 'Everyone',
				'registered' => 'All Registered Members',
				'owner_network' => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member' => 'Friends Only',
				'owner' => 'Just Me',
			);
		}
        
        $id = Engine_Api::_()->user()->getViewer() -> level_id;
		// View
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $id, 'auth_view');
        $viewOptions = array_intersect_key($this->_roles, array_flip($viewOptions));
        
        if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
            // Make a hidden field
            if(count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
            // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy viewing',
                    'description' => 'Who may view this folder?',
                    'multiOptions' => $viewOptions,
                ));
                $this->getELement('auth_view')->getDecorator('Description')->setOption('placement', 'append');
            }
        }
		unset($this->_roles['everyone']);
		
		// Create
		$createOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $id, 'auth_create');
        $createOptions = array_intersect_key($this->_roles, array_flip($createOptions));
        
        if( !empty($createOptions) && count($createOptions) >= 1 ) {
            // Make a hidden field
            if(count($createOptions) == 1) {
                $this->addElement('hidden', 'auth_create', array('value' => key($createOptions)));
            // Make select box
            } else {
                $this->addElement('Select', 'auth_create', array(
                    'label' => 'Privacy creating',
                    'description' => 'Who may create sub folders in this folder?',
                    'multiOptions' => $createOptions,
                    'value' => 'owner'
                ));
                $this->getELement('auth_create')->getDecorator('Description')->setOption('placement', 'append');
            }
        }		
		
		// Edit
		$editOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $id, 'auth_edit');
        $editOptions = array_intersect_key($this->_roles, array_flip($editOptions));
        
        if( !empty($editOptions) && count($editOptions) >= 1 ) {
            // Make a hidden field
            if(count($editOptions) == 1) {
                $this->addElement('hidden', 'auth_edit', array('value' => key($editOptions)));
            // Make select box
            } else {
                $this->addElement('Select', 'auth_edit', array(
                    'label' => 'Privacy editing',
                    'description' => 'Who may rename or move this folder?',
                    'multiOptions' => $editOptions,
                    'value' => 'owner'
                ));
                $this->getELement('auth_edit')->getDecorator('Description')->setOption('placement', 'append');
            }
        }
		
		// Delete
		$deleteOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $id, 'auth_delete');
        $deleteOptions = array_intersect_key($this->_roles, array_flip($deleteOptions));
        
        if( !empty($deleteOptions) && count($deleteOptions) >= 1 ) {
            // Make a hidden field
            if(count($deleteOptions) == 1) {
                $this->addElement('hidden', 'auth_delete', array('value' => key($deleteOptions)));
            // Make select box
            } else {
                $this->addElement('Select', 'auth_delete', array(
                    'label' => 'Privacy deleting',
                    'description' => 'Who may delete this folder?',
                    'multiOptions' => $deleteOptions,
                    'value' => 'owner'
                ));
                $this->getELement('auth_delete')->getDecorator('Description')->setOption('placement', 'append');
            }
        }
		
		// Comment
		$commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $id, 'auth_comment');
        $commentOptions = array_intersect_key($this->_roles, array_flip($commentOptions));
        
        if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
            // Make a hidden field
            if(count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('value' => key($deleteOptions)));
            // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Privacy comment',
                    'description' => 'Who may comment in this folder?',
                    'multiOptions' => $commentOptions,
                ));
                $this->getELement('auth_comment')->getDecorator('Description')->setOption('placement', 'append');
            }
        }
		
		// Edit permissions
		$editPermission = 'owner';
		if ($this->_parentType == 'group' || $this->_parentType == 'event') {
			$editPermission == 'owner_member';
		}
		$this->addElement('Hidden', 'auth_edit_perm', array('value' => $editPermission));
		
		// Init submit
		$this->addElement('Button', 'upload', array(
			'label' => 'Save Folder',
			'type' => 'submit',
			'decorators' => array(
        'ViewHelper',
      ),
		));
		$this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('upload', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
	}
}