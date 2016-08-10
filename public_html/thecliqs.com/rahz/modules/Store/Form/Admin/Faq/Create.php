<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Create.php 27.04.12 18:30 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Faq_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('STORE_Create New FAQ.')
      ->setDescription('STORE_ADMIN_FAQ_CREATE_DESCRIPTION')
      ->setAttrib('enctype','multipart/form-data')
    ;

    $this->addElement('Textarea', 'question', array(
      'label' => 'Question',
    ));

    $this->addElement('TinyMce', 'answer', array(
      'label' => 'Answer',
    ));

    $params = array(
      'mode' => 'exact',
	    'elements' => 'answer',
	    'width' => '500px',
	    'height' => '225px',
      'theme_advanced_buttons1' => array(
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
        'bullist', 'numlist', '|',
        'undo', 'redo', '|',
        'sub', 'sup', '|',
        'forecolor', 'forecolorpicker', 'backcolor', 'backcolorpicker', '|'
      ),
      'theme_advanced_buttons2' => array(
        'newdocument', 'code', 'image', 'media', 'preview', 'fullscreen', '|',
        'link', 'unlink', 'anchor', 'charmap', 'cleanup', 'hr', 'removeformat', 'blockquote', 'separator', 'outdent', 'indent', '|',
        'selectall', 'advimage'),
      'theme_advanced_buttons3' => array('formatselect', 'fontselect', 'fontsizeselect', 'styleselectchar', '|', 'table', '|'),
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    $this->addElement('Button', 'submit', array(
      'label' => 'STORE_Add FAQ',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      )
    ));
  }
}
