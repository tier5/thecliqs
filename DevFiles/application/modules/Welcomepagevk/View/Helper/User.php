<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
class User_View_Helper_User extends Zend_View_Helper_Abstract
{
  public function user($identity)
  {
    $user = Engine_Api::_()->user()->getUser($identity);
    if( !$user )
    {
      throw new Zend_View_Exception('User does not exist');
    }
    return $user;
  }
}
