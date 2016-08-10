<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
class Welcomepagevk_Widget_FooterUrlController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    $this->view->url = $url = 'http://socialenginemarket.com/browse_market/Themes/';
  }
    
}