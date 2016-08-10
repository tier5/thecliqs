<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
class Welcomepagevk_Widget_SignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->form = $form = new User_Form_Signup_Account();
	$elements = $form->getElements();
	if($elements){
		$start_tabindex = 50;
		foreach($elements as $element){
			$element->setAttrib('tabindex', $start_tabindex);
			$start_tabindex++;
		}	
	}
  }
}
