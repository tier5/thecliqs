<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       12:54
 */
class Donation_Widget_ProfileSupportersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('donation.supporters.count', 9);

    $this->view->likes = $likes = Engine_Api::_()->like()->getLikes($subject);

    if (!$likes) {
      $this->setNoRender();
      return;
    }

    $likes->setItemCountPerPage($ipp);


    if (!$likes || $likes->getTotalItemCount() == 0) {
      $this->setNoRender();
      return;
    }
  }


}
