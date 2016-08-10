<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       04.09.12
 * @time       10:37
 */
class Donation_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {

  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if($payload instanceof User_Model_User){
      //Delete donations
      $donationTable = Engine_Api::_()->getItemTable('donation');
      $donationSelect = $donationTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach($donationTable->fetchAll($donationSelect) as $donation)
      {
        $donation->deleteDonation();
      }
    }
  }
}
