<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 02.02.12 11:44 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_FaqController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');

    $faqs = array();
    $iter = 1;
    while('CREDIT_ANSWER_'.$iter != $translate->_('CREDIT_ANSWER_'.$iter)) {
      $faqs[$iter] = $translate->_('CREDIT_ANSWER_'.$iter);
      $iter ++;
    }

    if ($iter == 1) {
      return $this->setNoRender();
    }

    $index = rand(1, $iter-1);

    $this->view->index = rand(0, 2);
    $this->view->faq = $faqs[$index];
  }
}
