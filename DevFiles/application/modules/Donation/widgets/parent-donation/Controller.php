<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       14.08.12
 * @time       15:38
 */
class Donation_Widget_ParentDonationController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    /**
     * @var $donation Donation_Model_Donation
     */
    if (!Engine_Api::_()->core()->hasSubject()) return $this->setNoRender();

    $this->view->fundraise = $fundraise = Engine_Api::_()->core()->getSubject();

    $this->view->donation = $donation = Engine_Api::_()->getItem('donation', $fundraise->parent_id);



  }

}
