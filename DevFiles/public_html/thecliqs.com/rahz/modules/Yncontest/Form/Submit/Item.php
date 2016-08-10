<?php
class Yncontest_Form_Submit_Item extends Engine_Form
{
  public function init()
  {
   
	$request = Zend_Controller_Front::getInstance() -> getRequest();		
	$contestId = $request->getParam('contestId');
	$this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'name' => 'yncontest_entry_submit'                
            ))
            ->setAction(
            	Zend_Controller_Front::getInstance()->getRouter()->assemble(
	            array(		      		
		          	'action' => 'view',
		          	'contestId' => $contestId,
					'submit' => 1
	       	 	), 'yncontest_mycontest', true)
		 	);
	
    $this->addElement('Text', 'entry_name', array(
    		'label' => 'Entry Name*',
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
    
    
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	
	$editorOptions['plugins'] = array(
        'table', 'fullscreen', 'media', 'preview', 'paste',
        'code', 'image', 'textcolor', 'link'
      );

	  $editorOptions['toolbar1'] = array(
	    'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
	    'media', 'image', 'link', 'fullscreen',
	    'preview'
	  );
	  
	//Description
    $this->addElement('TinyMce', 'summary', array(
      'label' => '*Summary',
      'editorOptions' => $editorOptions,
      'required'   => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
	
   
    // Buttons
    $this -> addElement('Button', 'submit', array(
      'label' => 'Submit',     
      'type' => 'submit',
      'ignore' => true,
      'name' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    )); 
	$this -> addElement('Cancel', 'cancel', array(
	'label' => 'cancel', 
	'link' => true, 
	'prependText' => ' or ', 
	'href' =>  Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view','contestId' => $contestId), 'yncontest_mycontest', true), 	
	'decorators' => array(
        'ViewHelper',
      ),
	));	

  }
}

