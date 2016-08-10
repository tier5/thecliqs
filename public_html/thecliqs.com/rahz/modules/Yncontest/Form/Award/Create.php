<?php
class Yncontest_Form_Award_Create extends Engine_Form
{
  public function init()
  {
   
  	//$this
  	//->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
  	//->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element');
  	//->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	 

    $this
      ->setTitle('Add Award');

    $this->addElement('Text', 'award_name', array(
      'label' => 'Name*',
      'allowEmpty' => false,
       'description' => 'Please enter no more than 10 characters',
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 10)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    $this->award_name->getDecorator("Description")->setOption("placement", "append");
	
    $this->addElement('Text', 'value', array(
    		'label' => 'Value',   
    		'validators' => array(
    				array('NotEmpty', true),
    				array('StringLength', false, array(1, 10)),
    				array('Int',true),
    		),
    		'filters' => array(
    				'StripTags',
    				new Engine_Filter_Censor(),
    				
    		),
    ));
    $table = Engine_Api::_()->getDbTable('currencies','yncontest');
    $currencies  = $table->getCurrencies();
    $this->addElement('Select', 'currency', array(
    		'label' => 'Currency',
    		'multiOptions' => Yncontest_Model_DbTable_Currencies::getMultiOptions(),    	
    		'required' => false,
    		'value' => 0
    ));    

    // ));
	$this->addElement('Text', 'description', array(
      'label' => 'Description*',
      'allowEmpty' => false,
      'required' => true,
      'description' => 'Please enter no more than 20 characters',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 20)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));    
  	$this->description->getDecorator("Description")->setOption("placement", "append");
  
    $this->addElement('Text', 'quantities', array(
    		'label' => 'Quantities*',
    		'allowEmpty' => false,
    		'required' => true,
    		'validators' => array(    				
    				array('NotEmpty',true),
    				array('Int',true),
    				array('GreaterThan',true,array(0)),
    		),
    		'filters' => array(
    				'StripTags',
    				new Engine_Filter_Censor(),
    		),
    ));
       
   
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',     
      'type' => 'submit',      
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'yncontest_general', true),
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));  
  }
}
?>
<style type="text/css">
#description {
    width: 200px;
    
}
</style>


