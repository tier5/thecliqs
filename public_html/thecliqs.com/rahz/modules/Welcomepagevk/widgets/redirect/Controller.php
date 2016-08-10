<?php

/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
class Welcomepagevk_Widget_RedirectController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

      $viewer = Engine_Api::_()->user()->getViewer();
      if( !$viewer->getIdentity() ) {
        $gotoWelcome = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'welcomepagevk_general');
        header("Location: {$gotoWelcome}");
        die();
      }

    }

}
