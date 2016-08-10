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
 * @time       13:17
 */
class Donation_Widget_ProfileDescriptionController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();
  }

}
