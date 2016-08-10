<?php
class Yncontest_Form_Entries_Edit extends Yncontest_Form_Submit_Item
{
  public function init()
  {
   

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
    $this->addElement('TinyMce', 'summary', array(
    		'label' => 'Summary*',
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
      'label' => 'Submit',     
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
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
  }
}

