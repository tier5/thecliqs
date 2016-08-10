<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdvSearch.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Page_Form_MyLocation extends Engine_Form
{
  public function init()
  {
    $this->setMethod('post')
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ;

    $myAddress = new Engine_Form_Element_Text('my_location');
    $myAddress->clearDecorators();
    $this->addElement($myAddress)
      ->addDecorator('ViewHelper');

    $button = new Engine_Form_Element_Button('my_location_submit');
    $button->clearDecorators()
      ->addDecorator('ViewHelper');
    $this->addElement($button);
  }
}