<?php
class Ynbusinesspages_Form_Business_Theme extends Engine_Form
{
	protected $_package;
	
	public function getPackage()
	{
		return $this -> _package;
	}
	
	public function setPackage($package)
	{
		$this -> _package = $package;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $id = Engine_Api::_()->user()->getViewer() -> level_id;
	$this->setDescription('You can reselect your business theme anytime that you want');
	
	if($this -> _package){
		$this -> addElement('dummy', 'theme', array(
				'label'     => 'Select Themes',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_post_business_themes.tpl',
						'package' =>  $this -> _package,
						'class' => 'form element',
					)
				)), 
		));  
    }
	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Select Theme',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
    $this->addDisplayGroup(array('submit_button'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
