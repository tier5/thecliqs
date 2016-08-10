<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 05.09.12
 * Time: 15:37
 * To change this template use File | Settings | File Templates.
 */
class Donation_Api_Page extends Core_Api_Abstract
{
  public function getNavigation(Page_Model_Page $page)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $navigation = new Zend_Navigation();

    if($page && $page->isDonation() && $page->isAllowDonation()){
      if($page->getDonationPrivacy('charity') || $page->getDonationPrivacy('project')){
        $navigation->addPage(
          array(
            'label' => 'DONATION_Manage Donations',
            'route' => 'donation_extended',
            'controller' => 'page',
            'action' => 'index',
            'params' => array(
              'page_id' => $page->getIdentity(),
            ),
          )
        );
      }
      if($settings->getSetting('donation.enable.charities',1) && $page->getDonationPrivacy('charity')){
        $navigation->addPage(
          array(
            'label' => 'DONATION_Create New Charity',
            'route' => 'donation_extended',
            'controller' => 'charity',
            'action' => 'create',
            'params' => array(
              'page_id' => $page->getIdentity(),
            ),
          ));
      }
      if($settings->getSetting('donation.enable.projects',1) && $page->getDonationPrivacy('project')){
        $navigation->addPage(
          array(
            'label' => 'DONATION_Create New Project',
            'route' => 'donation_extended',
            'controller' => 'project',
            'action' => 'create',
            'params' => array(
              'page_id' => $page->getIdentity(),
            ),
          ));
      }
    }


    return $navigation;
  }
}
