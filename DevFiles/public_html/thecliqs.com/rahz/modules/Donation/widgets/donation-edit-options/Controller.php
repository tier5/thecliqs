<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       30.08.12
 * @time       15:44
 */
class Donation_Widget_DonationEditOptionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    if (Engine_Api::_()->core()->getSubject() instanceof Donation_Model_Donation)
    {
      $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();
    } else {
      $this->view->donation = $donation = Engine_Api::_()->getItem('donation', Engine_Api::_()->core()->getSubject()->donation_id);
    }

    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();

    /**
     * @var $page Page_Model_Page
     */
    $page = $donation->getPage();
    if($page){
      if(!$page->getDonationPrivacy($donation->type)){
        return $this->setNoRender();
      }
      $this->view->page = $page;
    }
    else{
      if($viewer->getIdentity() != $donation->getOwner()->getIdentity()){
        return $this->setNoRender();
      }
    }
    $album = $donation->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    if($paginator->getTotalItemCount() > 0){
      $this->view->photo = $paginator->getItem(0);
    }
  }
}
