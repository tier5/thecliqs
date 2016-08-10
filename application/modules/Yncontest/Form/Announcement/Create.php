<?php
class Yncontest_Form_Announcement_Create extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Post New Announcement')
      ->setDescription('Please compose your new announcement below.')
      ->setAttrib("class", "global_form_popup")
      ->setAttrib('id', 'announcements_create');

    // Add title
    $this->addElement('Text', 'title', array(
      'label' => 'Title*',
      'required' => true,
      'allowEmpty' => false,
    ));
	
	
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    $this->addElement('TinyMce', 'body', array(    		
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
      'label' => 'Post Announcement',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}