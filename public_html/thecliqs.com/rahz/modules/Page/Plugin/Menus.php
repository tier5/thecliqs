<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Plugin_Menus
{

  public function onMenuInitialize_PageMainPages($row)
  {
    return true;
  }

  public function onMenuInitialize_PageMainReviews($row)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $isEnabledBrowseReviews = $settings->getSetting('rate.browse.reviews.enable', 0);
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate') && $isEnabledBrowseReviews) {
      return true;
    }
    return false;
  }

  public function onMenuInitialize_PageMainManage($row)
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return false;
    }
    return true;
  }

  public function onMenuInitialize_PageMainCreate($row)
  {
    if( !Engine_Api::_()->authorization()->isAllowed('page', null, 'create') ) {
      return false;
    }
    return true;
  }

  public function onMenuInitialize_PageMainClaim($row)
  {
    $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');
    $claimersTbl = Engine_Api::_()->getDbTable('settings', 'user');
    $select = $pageTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $pageTbl->info('name')))
      ->joinLeft(array('c' => $claimersTbl->info('name')), 'c.user_id = p.user_id', array())
      ->where('c.name = ?', 'claimable_page_creator')
            ->limit(1);

    if (!Engine_Api::_()->user()->getViewer()->getIdentity() || null === $pageTbl->fetchRow($select)) {
      return false;
    }
    return true;
  }

  // page_profile
  public function onMenuInitialize_PageProfileEdit($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    if($subject->isAdmin()) {
      return array(
        'label' => 'Edit Page',
        'icon' => 'application/modules/Core/externals/images/admin/editinfo.png',
        'route' => 'page_team',
        'params' => array(
          'action' => 'edit',
          'page_id' => $subject->getIdentity(),
        )
      );
    }

    return false;
  }

  // Timeline Page
  public function onMenuInitialize_PageProfileTimeline($row)
  {
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline')) {
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $usage = $settings->__get('timeline.usageonpage', false);
            if($usage == 'force')
                return false;

      $subject = Engine_Api::_()->core()->getSubject();
      $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'type', 'page' => $subject->getIdentity(), 'p'=>'switch'), 'page_editor', true);
      // icon lisence
      // http://www.iconsearch.ru/detailed/36766/1/
      // http://creativecommons.org/licenses/by/3.0/
      if ($subject->isTeamMember()) {
        return array(
          'label' => 'Switch View Mode',
          'icon' => 'application/modules/Timeline/externals/images/page_timeline/convert_1.png',
          'href' => 'javascript:void(0);',
          'onClick' => "changePageProfileType('$url');",
          'route' => 'page_team',
        );
      }
    }
    return false;
  }

// Timeline Page

  public function onMenuInitialize_PageProfileShare($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if ($viewer->getIdentity()) {
      return array(
        'label' => 'Share Page',
        'icon' => 'application/modules/Page/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
          'module' => 'activity',
          'controller' => 'index',
          'action' => 'share',
          'type' => $subject->getType(),
          'id' => $subject->getIdentity(),
          'format' => 'smoothbox',
        )
      );
    }

    return false;
  }

  public function onMenuInitialize_PageProfileFavorite($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $table = Engine_Api::_()->getDbTable('pages', 'page');

    if ($viewer->getIdentity() && $table->hasPages($viewer->getIdentity(), $subject->getIdentity())) {
      return array(
        'label' => 'Add Page To Favorites',
        'icon' => 'application/modules/Page/externals/images/favorite.png',
        'uri' => 'javascript:page.getPages()'
      );
    }

    return false;
  }

  public function onMenuInitialize_PageProfileStore($row)
  {
    /**
     * @var $subject Page_Model_Page
     * @var $viewer User_Model_User
     */
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($viewer->getIdentity() && $subject->isStore() && $subject->getStorePrivacy() && $subject->isAllowStore()) {
      return array(
        'label' => 'STORE_Manage Products',
        'icon' => 'application/modules/Store/externals/images/edit_store.png',
        'route' => 'store_products',
        'params' => array(
          'page_id' => $subject->getIdentity()
        )
      );
    }

    return false;
  }

  public function onMenuInitialize_PageProfileDonation($row)
  {
    /**
     * @var $subject Page_Model_Page
     * @var $viewer User_Model_User
     */
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if($viewer->getIdentity() && $subject->isDonation() && $subject->isAllowDonation() && ($subject->getDonationPrivacy('charity') || $subject->getDonationPrivacy('project'))){
      return array(
        'label' => 'DONATION_Manage Donations',
        'icon' => 'application/modules/Donation/externals/images/manage_donations.png',
        'route' => 'donation_extended',
        'controller' => 'page',
        'action' => 'index',
        'params' => array(
          'page_id' => $subject->getIdentity(),
        )
      );
    }

    return false;
  }

  public function onMenuInitialize_PageProfilePrint()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'page') {
      return false;
    }

    return array(
      'label' => 'Print Page',
      'icon' => 'application/modules/Page/externals/images/page_print.png',
      'target' => '_blank',
      'route' => 'page_print',
      'params' => array(
        'action' => 'print',
        'page_id' => $subject->getIdentity(),
      ),
    );
  }

  public function onMenuInitialize_PageProfileClaim($row)
  {
    $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');
    $claimersTbl = Engine_Api::_()->getDbTable('settings', 'user');
    $select = $pageTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $pageTbl->info('name')))
      ->joinLeft(array('c' => $claimersTbl->info('name')), 'c.user_id = p.user_id', array())
      ->where('c.name = ?', 'claimable_page_creator')
            ->limit(1);

    if (!Engine_Api::_()->user()->getViewer()->getIdentity() || null === $pageTbl->fetchRow($select)) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();

        if ($subject->user_id === Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return false;
    }

    if ($subject->getType() !== 'page') {
      return false;
    }

    return array(
      'label' => 'Claim this Page',
      'icon' => 'application/modules/Page/externals/images/share.png',
      'route' => 'page_claim',
      'params' => array(
        'action' => 'claim',
        'id' => $subject->getIdentity(),
      ),
    );
  }

}