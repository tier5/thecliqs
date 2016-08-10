<?php
class Yncontest_Form_Rule_Edit extends Engine_Form
{
  public function init()
  {
   
  	//$this
  	//->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
  	//->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element');
  	//->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	 

    $this
      ->setTitle('Edit Rule');
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $rule_id = $request->getParam('rule', null);
  	
	$rule = Engine_Api::_()->getDbTable('rules','yncontest')->find($rule_id)->current();
	
    $this->addElement('Text', 'rule_name', array(
      'label' => 'Name*',
      'allowEmpty' => false,
      'required' => true,
      'value' => $rule->rule_name,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
   
    
    $start = new Engine_Form_Element_CalendarDateTime('start_date');
    $start->setLabel("Start Date*");
    $start->setAllowEmpty(false);
    $start->setRequired(true);
    $start->setValue($rule->start_date);
    $this->addElement($start);
    
    $end = new Engine_Form_Element_CalendarDateTime('end_date');
    $end->setLabel("End Date*");
    $end->setAllowEmpty(false);
    $end->setRequired(true);
    $end->setValue($rule->end_date);
    $this->addElement($end);
   
   
    
 
    
    $this->addElement('MultiCheckbox', 'option', array(
    		'label' => 'Option*',    		
    		'multiOptions' => array(
    				//'view' 		=> 'Allow the public to view submission entries',
    				'submit' => 'Allow the public to submit entries',
    				'vote' => 'Allow the public to vote entries',    				
    		),
    		'value' => array(
    		       //'view',
    		       'submit',
    		       'vote',    		       
    		),
    	
    ));
    
    $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    $this->addElement('TinyMce', 'description', array(
    		'label' => 'Description',
    		'value' => $rule->description,
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
  			'prependText' => ' or ',
  			'href' => '',
  			'onclick' => 'parent.Smoothbox.close();',
  			'decorators' => array(
  					'ViewHelper'
  			)
  	));
  }
}

