<?php
class Yncontest_Form_Member_Edit extends Engine_Form
{
  public function init()
  {
  	
  	$this
  	//->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
  	->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element');
  	//->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	
  	
    $this->setTitle('Edit This Contest')
      ->setDescription('Would you like to edit this contest?');

    //$this->addElement('Hash', 'token');

        
//    $this->addElement('radio','member_type',array(
//     		'label'=>'Member Type',
//     		'multiOptions'=>array(
//     				'participant'	=>'As a Participant',
//     				'yncontest_list'		=>'As a Organizer',
//     		),
//     		'value'=>'participant',
//     ));
    
    $this->addElement('Text', 'full_name', array(
    		'label' => 'Full Name*',
    		'allowEmpty' => false,
    		'required' => true,
    		'validators' => array(
    				array('NotEmpty', true),
    				array('StringLength', false, array(1, 64)),
    		),
    		'filters' => array(
    				'StripTags',
    				new Engine_Filter_Censor(),
    		),
    ));
    $this->addElement('Text', 'email', array(
    		'label' => 'Email Address*',
    		//'description' => 'We will send email alert to you if there is a submitted entry.',
    		'validators' => array(
    				array('NotEmpty', true),
    				array('EmailAddress', true),
    		),
    ));
    $this->addElement('Text', 'phone', array(
    		'label' => 'Phone',
    		'allowEmpty' => true,
    		'required' => false, 
    ));
    
//     $start = new Engine_Form_Element_Birthdate('birth');
//     $start->setLabel("Day of Birth");
//     $start->setAllowEmpty(true);
//     $start->setRequired(false);
//     $this->addElement($start);
    
//     $this->addElement('ContestMultiLevel', 'location_id', array(
//     		'label' => 'Location*',
//     		'required'=>true,
//     		'allowEmpty' => false,
//     		'model'=>'Yncontest_Model_DbTable_Locations',
//     		'onchange'=>"en4.yncontest.changeCategory($(this),'location_id','Yncontest_Model_DbTable_Locations','contest/my-contest')",
//     		'title' => '',
//     		'value' => ''
//     ));
//     $this->addElement('radio','sex',array(
//     		'label'=>'Member Type',
//     		'multiOptions'=>array(
//     				'male'	=>'Male',
//     				'female'	=>'FeMale',
//     		),
//     		'value'=>'male',
//     ));
   
    
//     $this->addElement('File', 'photo_id', array(
//     		'label' => 'Upload Photo*',
//     		'required' => true,
//     ));
    
    $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    $this->addElement('TinyMce', 'summary', array(
    		'label' => 'Summary',
    		'editorOptions' => array(
    				'bbcode' => 1,
    				'html'   => 1,
    				'theme_advanced_buttons1' => array(
    						'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
    						'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
    				),
    				'theme_advanced_buttons2' => array(
    						'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
    						'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
    						'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
    				),
    		),
    		'required'   => true,
    		'allowEmpty' => false,
    		'filters' => array(
    				new Engine_Filter_Censor(),
    				new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
  }
}