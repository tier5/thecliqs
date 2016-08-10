<?php
class Yncontest_Form_Award_Edit extends Engine_Form
{
  public function init()
  {
   
  	//$this
  	//->addPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
  	//->addPrefixPath('Yncontest_Form_Element', APPLICATION_PATH . '/application/modules/Yncontest/Form/Element', 'element');
  	//->addElementPrefixPath('Yncontest_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	 

    
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $award_id = $request->getParam('award', null);
  	
	$award = Engine_Api::_()->getDbTable('awards','yncontest')->find($award_id)->current();
	
	$this
	->setTitle('Add Award');
	
	$this->addElement('Text', 'award_name', array(
			'label' => 'Name*',
			'allowEmpty' => false,
			'required' => true,
			'value' => $award->award_name,
			'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
			),
			'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
			),
	));
	
	$this->addElement('Text', 'value', array(
			'label' => 'Value',
			'value' => ( $award->value? $award->value : NULL ) ,
			'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
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
			'multiOptions' => $currencies,
			'required' => false,
			'value' => $award->currency,
	));
	
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	$this->addElement('TinyMce', 'description', array(
			'label' => 'Description*',
			'value' => $award->description,
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
	
	
	$this->addElement('Text', 'quantities', array(
			'label' => 'Quantities*',
			'allowEmpty' => false,
			'required' => true,
			'value' => $award->quantities,
			'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
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
  			'prependText' => ' or ',
  			'href' => '',
  			'onclick' => 'parent.Smoothbox.close();',
  			'decorators' => array(
  					'ViewHelper'
  			)
  	));
  }
}

?>


