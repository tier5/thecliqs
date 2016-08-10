<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Buy.php 19.01.12 13:44 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Payments_Buy extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'buy_credit_form',
        'class' => 'global_form_box'
      ))
    ;

    $view = Zend_Registry::get('Zend_View');
    $description = $view->translate('%s You can buy credits by click this button.', $view->htmlImage($view->layout()->staticBaseUrl.'application/modules/Credit/externals/images/buy.png', '', array('class' => 'buy_credits_icon')));

    $this->setDescription($description);

    $this->addElement('Button', 'buy', array(
      'label' => 'Buy Credits',
      'type' => 'submit',
    ));
  }
}
