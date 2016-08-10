<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Form_EditFolder extends Engine_Form {
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