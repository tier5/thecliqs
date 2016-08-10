<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation_Widget_DonationOptionsController
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       25.07.12
 * @time       18:27
 */

class Donation_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        // Don't render this if not authorized
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();

        // Get subject and check auth
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('donation_profile');

    $isOwner = false;
    $page = $donation->getPage();

    if($page){
      if($page->getDonationPrivacy($donation->type)){
        $isOwner = true;
      }
    }
    elseif($donation->isOwner($viewer)){
      $isOwner = true;
    }

    $this->view->isOwner = $isOwner;
    }
}