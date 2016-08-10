<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

class User_Plugin_Activity_Request
{
  public function render(Zend_View_Interface $view, Activity_Model_Request $request)
  {
    return $view->action('request', 'widget', 'user', array('request' => $request));
  }

  public function handle(Zend_View_Interface $view, Activity_Model_Request $request, Zend_Controller_Request_Abstract $data)
  {
    return $view->action('request-process', 'widget', 'user', array('request' => $request, 'data' => $data));
  }
}
