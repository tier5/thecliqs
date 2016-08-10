<?php
class Ynauction_Form_Admin_Page extends Engine_Form
{
  public function init()
  {
    //Set Method
    $this->setMethod('post');
    $this ->setAttrib('class', 'global_form_popup');
    //Get User Level And HTML Allow
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'ynauction_deal', 'auth_html');

    //Instruction Page Id
    $this->addElement('Hidden','pageId');

    //Instruction Page Title
    $this->addElement('Text','title',array(
      'label' => 'Page Title',
        'description' => '(Maximum length is up to 128 characters)',
        'required' =>true,
    ));

    //Instruction Page Content
    $this->addElement('TinyMce', 'body', array(
      'label' => 'Page Content',
      'description' => '(Maximum length is unlimited)',
      'required' => true,
      'editorOptions' => array(
          'bbcode' => 1,
          'html' => 1
        ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
    ));

    //Submit Button
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Page',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'      => 'ynauction',
                                                                                  'controller'  => 'page',
                                                                                  'action'      => 'index'), 'admin_default', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}
?>