<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Send.php 09.01.12 14:09 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Send extends Engine_Form
{
  private $form_type;
  private $user;

  public function __construct($form_type = '', $user_id = 0)
  {
    $this->form_type = $form_type;
    $this->user = Engine_Api::_()->getItem('user', $user_id);
    parent::__construct();
  }

  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'send_credit_form',
        'class' => 'global_form_box'
      ));
    $view = Zend_Registry::get('Zend_View');

    if ($this->form_type == 'smoothbox') {
      $description = $view->translate('CREDIT_You can send credits to user %s', $this->user->displayname);
      $this->setTitle('CREDIT_Send Credits')
        ->setDescription($description)
        ->setMethod('POST');
    } else {
      $this->addElement('Text', 'username', array(
        'label' => 'Your Friend Name',
        'autocomplete' => 'off',
        'description' => 'start typing...',
        'filters' => array(
          new Engine_Filter_Censor(),
        ),
      ))->getElement('username')->getDecorator("Description")->setOption("placement", "append");
    }

    $this->addElement('Text', 'credit', array(
      'label' => 'Credit',
    ));

    $this->addElement('Hidden', 'user_id');

    $this->addElement('Button', 'send', array(
      'label' => 'Send Credit',
      'type' => 'submit',
    ));
  }
}
