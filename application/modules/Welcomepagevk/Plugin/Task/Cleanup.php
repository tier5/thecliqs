<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

class User_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    // Garbage collect the online users table
    Engine_Api::_()->getDbtable('online', 'user')->gc();

    // Garbage collect the forgot password table
    Engine_Api::_()->getDbtable('forgot', 'user')->gc();

    // Garbage collect the verification table
    Engine_Api::_()->getDbtable('verify', 'user')->gc();

    // This task shouldn't take too long, just set was idle
    $this->_setWasIdle();
  }
}
