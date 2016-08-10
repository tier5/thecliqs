<?php
class Ynfeed_Form_Admin_CustomType extends Engine_Form 
{

  public function init() {

    $this ->setTitle('Add New Content Type') ->setAttrib('class', 'global_form_popup')
			->setDescription('Use the form below to add a content type for enabling users to create their custom lists for filtering activity feeds over them.');
	
    $module_name = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_name', null);
	$this->addElement('Hidden', 'module_name', array(
          'value' => $module_name,
      ));
    $contentItem = array();
    if (!empty($module_name)) {
      $contentItem = $this->getContentItem($module_name);
      if (empty($contentItem))
        $this->addElement('Dummy', 'dummy_title', array(
            'description' => 'For this module not difine any item in manifest file.',
        ));
    }
    if (!empty($contentItem)) 
    {
      $this->addElement('Select', 'resource_type', array(
          'label' => 'Content Type',
          'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
          'multiOptions' => $contentItem,
      ));

      $this->addElement('Text', 'resource_title', array(
          'label' => 'Content Title',
          'description' => 'Enter the content title for which you use this module. Ex: You may use the Documents module for ‘Tutorials’ on your community.',
          'required' => true
      ));

      $this->addElement('Checkbox', 'enabled', array(
          'description' => 'Enable for Custom Lists',
          'label' => 'Enable this content type to be part of users’ custom lists for filtering of activity feeds.',
          'value' => 1
      ));
      
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
    } else {
      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'ignore' => true,
          'link' => false,
          'onClick'=> 'javascript:parent.Smoothbox.close();',
      ));
    }
  }

  public function getContentItem($moduleName) {
    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
    $contentItem = array();
    if (@file_exists($file_path)) {
      $ret = include $file_path;
      if (isset($ret['items'])) {

        foreach ($ret['items'] as $item)
          $contentItem[$item] = $item . " ";
      }
    }
    return $contentItem;
  }

}

?>
