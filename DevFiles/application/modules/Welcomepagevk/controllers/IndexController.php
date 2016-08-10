<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

class Welcomepagevk_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'home', true);
    }
    $this ->_helper->content
          ->setNoRender()
          ->setEnabled()
    ;
  }
}
