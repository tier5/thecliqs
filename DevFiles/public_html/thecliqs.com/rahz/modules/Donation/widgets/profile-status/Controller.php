<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Controller
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       12:43
 */
class Donation_Widget_ProfileStatusController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    /**
     * @var $donation Donation_Model_Donation
     */
    if (!Engine_Api::_()->core()->hasSubject()) return $this->setNoRender();
    $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();

    $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
  }

}
