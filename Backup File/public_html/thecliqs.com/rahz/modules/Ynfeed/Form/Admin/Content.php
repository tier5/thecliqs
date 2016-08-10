<?php
class Ynfeed_Form_Admin_Content extends Engine_Form {

  public function init() {

    $this
        ->setTitle('Add New Filter')
		->setAttrib('class', 'global_form_popup')
  		->setMethod("POST")
        ->setDescription('Add new filter to allow users to find feeds associated to a module.');
    
    $notInclude = array('socialstream', 'ynmobile', 'ynmobileview');

    $addedModule = Engine_Api::_()->getDbtable('contents', 'ynfeed')->getAddedModule();
    
    if(!empty ($addedModule))   
  		$notInclude = array_merge($notInclude, $addedModule);
	$actionType_table = Engine_Api::_()->getDbTable('actionTypes', 'activity');
	$select = $actionType_table -> select() ->distinct() -> from($actionType_table -> info('name'), 'module');
	$include = $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
	
    $module_table = Engine_Api::_()->getDbTable('modules', 'core');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('name', 'title'))
            ->where($module_name . '.type = ?', 'extra')
            ->where($module_name . '.name not in(?)', $notInclude)
			->where($module_name . '.name in(?)', $include)
            ->where($module_name . '.enabled =?', 1);

    $contentModuloe = $select->query()->fetchAll();
    $contentModuloeArray = array();
    foreach ($contentModuloe as $modules) {
      $contentModuloeArray[$modules['name']] = $modules['title']. " ";
    }

    if (!empty($contentModuloeArray)) 
    {
      $this->addElement('Select', 'module_name', array(
          'label' => 'Module',
          'allowEmpty' => false,
          'onchange' => 'setModuleName(this.value)',
          'multiOptions' => $contentModuloeArray,
      ));
      $this->addElement('Hidden', 'filter_type', array(
          'value' => key($contentModuloeArray),
      ));

      $this->addElement('Text', 'resource_title', array(
          'label' => 'Filter Name',
          'description' => 'Enter the filter name for which you use this module.',
          'required' => true
      ));
	  $this -> 	resource_title -> getDecorator("Description")->setOption("placement", "append");
	 
	  // Photo
      $this->addElement('File', 'photo', array(
      		'label' => 'Icon (16x16)'
      ));
	  $this -> photo -> setAttrib("accept", "image/*");
      $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	  
      // Element: execute
      $this->addElement('Button', 'execute', array(
          'label' => 'Save',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper'),
      ));

      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'prependText' => ' or ',
          'ignore' => true,
          'link' => false,
          'onClick'=> 'javascript:parent.Smoothbox.close();',
          'decorators' => array('ViewHelper'),
      ));

      // DisplayGroup: buttons
      $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper',
          )
      ));
    } 
    else 
    {
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules that could be added for “Filter Lists”.") . "</span></div>";
      $this->addElement('Dummy', 'module', array(
          'description' => $description,
      ));
      $this->module->addDecorator('Description', array('placement' =>
          Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }
  }

}

?>
