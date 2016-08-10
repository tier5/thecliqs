<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
class Welcomepagevk_Widget_LoginController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->loginAction = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login');
    $this->view->form = $form = new User_Form_Login();
  }
    
}
