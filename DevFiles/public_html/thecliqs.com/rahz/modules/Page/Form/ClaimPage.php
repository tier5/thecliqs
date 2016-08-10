<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ClaimPage.php 19.12.11 17:59 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_ClaimPage extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Claim a Page')
      ->setDescription('ClAIM_PAGE_DESC')
      ->setAttrib('enctype','multipart/form-data')
      ->setAttrib('id', 'claim')
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Page Name',
      'description' => 'Start typing the name of the page, choose only one page',
      'allowEmpty' => false,
      'required' => true,
      'autocomplete' => 'off'
    ));

    $this->title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    $this->addElement('Hidden', 'page_id');

    $this->addElement('Text', 'claimer_name', array(
      'label' => 'Your Name',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '64')),
      )
    ));

    $this->addElement('Text', 'claimer_email', array(
      'label' => 'Your Email',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true)
      )
    ));
    $this->claimer_email->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');

    $this->addElement('Text', 'claimer_phone', array(
      'label' => 'Your Telephone',
      'allowEmpty' => false,
      'required' => true
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'About You and the Page',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
          'StripTags',
          new Engine_Filter_HtmlSpecialChars(),
          new Engine_Filter_EnableLinks(),
          new Engine_Filter_Censor(),
      ),
    ));

    $url = Zend_Registry::get('Zend_View')->url(array('action' => 'terms'), 'page_claim', true);

    $this->addElement('Checkbox', 'terms', array(
      'label' => 'Terms of Service',
      'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("I have read and agree to the <a href='javascript:void(0);' onclick=window.open('%s','mywindow','width=500,height=300')>terms of service</a>."), $url),
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        'notEmpty'
      ),
      'value' => 0
    ));
    $this->terms->getValidator('NotEmpty')->setMessage('You must agree to the terms of service to continue.', 'isEmpty');
    $this->terms->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms'))
      ->addDecorator('DivDivDivWrapper');

    $this->addElement('Button', 'execute', array(
      'label' => 'Submit Notice',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'page_browse', true),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}
