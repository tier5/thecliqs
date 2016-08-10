<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

class User_View_Helper_Viewer extends Zend_View_Helper_Abstract
{
  public function viewer()
  {
    return Engine_Api::_()->user()->getViewer();
  }
}
