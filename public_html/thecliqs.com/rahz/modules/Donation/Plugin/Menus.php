<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 19.07.12
 * Time: 11:06
 * To change this template use File | Settings | File Templates.
 */
class Donation_Plugin_Menus
{
  public function getDonationApi()
  {
    return Engine_Api::_()->getApi('core', 'donation');
  }

  public function getSetting($setting)
  {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting($setting, 1);
  }

  public function onMenuInitialize_DonationMainBrowseCharity($row)
  {
    if ($this->getSetting('donation.enable.charities')) {
      return true;
    }
    return false;
  }

  public function onMenuInitialize_DonationMainBrowseProject($row)
  {
    if ($this->getSetting('donation.enable.projects')) {
      return true;
    }
    return false;
  }

  public function onMenuInitialize_DonationMainBrowseFundraise($row)
  {
    if ($this->getSetting('donation.enable.fundraising')) {
      return true;
    }
    return false;
  }

  public function onMenuInitialize_DonationProfileEdit($row)
  {
    /**
     * @var $subject Donation_Model_Donation
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $page = $subject->getPage();

    $allowEdit = true;

    if ($page && $subject->type != 'fundraise') {
      if (!$page->getDonationPrivacy($subject->type)) {
        $allowEdit = false;
      }
    } elseif (!$subject->isOwner($viewer)) {
      $allowEdit = false;
    }

    if ($allowEdit) {
      return array(
        'label' => 'DONATION_Edit Donation',
        'icon' => 'application/modules/User/externals/images/edit.png',
        'route' => 'donation_extended',
        'params' => array(
          'module' => 'donation',
          'controller' => $subject->type,
          'action' => 'edit',
          'donation_id' => ($viewer->getGuid(false) == $subject->getGuid(false)
            ? null
            : $subject->getIdentity()),
        )
      );
    }

    return false;
  }

  public function onMenuInitialize_DonationProfileDelete($row)
  {
    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $page = $subject->getPage();

    $allowDelete = true;

    if ($page && $subject->type != 'fundraise') {
      if (!$page->getDonationPrivacy($subject->type)) {
        $allowDelete = false;
      }
    } elseif (!$subject->isOwner($viewer)) {
      $allowDelete = false;
    }


    if ($allowDelete) {
      return array(
        'label' => 'DONATION_Delete Donation',
        'icon' => 'application/modules/Core/externals/images/delete.png',
        'route' => 'donation_extended',
        'params' => array(
          'module' => 'donation',
          'controller' => $subject->type,
          'action' => 'delete',
          'donation_id' => $subject->getIdentity(),
          'format' => 'smoothbox',
        ),
        'class' => 'smoothbox',
      );
    }

    return false;
  }

  public function onMenuInitialize_DonationProfileShare($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject && $subject->status == 'active' && $subject->approved) {
      return array(
        'label' => 'Share',
        'icon' => 'application/modules/Donation/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
          'module' => 'activity',
          'controller' => 'index',
          'action' => 'share',
          'type' => $subject->getType(),
          'id' => $subject->getIdentity(),
          'format' => 'smoothbox'
        )
      );
    }
    return false;
  }

  public function onMenuInitialize_DonationProfileDonation($row)
  {
    return false;
    $view = Zend_Registry::get('Zend_View');
    $subject = Engine_Api::_()->core()->getSubject();
    $navigation = array();
    if ($subject->page_id != 0) {
      if (null != ($page = Engine_Api::_()->getItem('page', $subject->page_id))) {
        $navigation[] = array(
          'label' => 'Back to Donations',
          'icon' => 'application/modules/Like/externals/images/icons/donation.png',
          'uri' => $page->getHref(),
        );
      }
    }

    $url = $view->url(array('action' => 'index'), 'donation_general', true);

    $navigation[] = array(
      'label' => 'Back to Donations',
      'icon' => 'application/modules/Core/externals/images/back.png',
      'uri' => $url,
    );

    return $navigation;
  }

  public function onMenuInitialize_DonationProfileFundraise($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if (!$subject || $subject->status != 'active' || !$subject->approved) {
      return false;
    }

    if ($subject->type == 'fundraise' || !$this->getSetting('donation.enable.fundraising') || !Engine_Api::_()->authorization()->isAllowed('donation', $viewer, 'raise_money')) {
      return false;
    }

    if ($viewer->getIdentity()) {
      return array(
        'label' => 'Raise Money for Us',
        'icon' => 'application/modules/Donation/externals/images/money.png',
        'route' => 'donation_extended',
        'params' => array(
          'module' => 'donation',
          'controller' => 'fundraise',
          'action' => 'index',
          'donation_id' => $subject->getIdentity(),
          'format' => 'smoothbox',
        ),
        'class' => 'smoothbox',
      );
    }
    return false;
  }

  public function onMenuInitialize_DonationProfilePromote($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    $view = Zend_Registry::get('Zend_View');

    $url = $view->url(array('controller' => 'donation', 'action' => 'promote', 'object_id' => $subject->getIdentity(), 'object' => 'donation'), 'donation_extended', true);

    if ($subject->status == 'active' && $subject->approved) {
      return array(
        'label' => 'Promote Donation',
        'icon' => 'application/modules/Donation/externals/images/promote_donation.png',
        'uri' => $url,
        'class' => 'smoothbox'
      );
    }

    return false;
  }

  public function onMenuInitialize_DonationProfileFininfo($row)
  {
    return false;
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();


    if ($subject->getOwner()->getIdentity() == $viewer->getIdentity() && $subject->type != 'fundraise') {
      return array(
        'label' => 'DONATION_Edit Financial Information',
        'icon' => 'application/modules/Donation/externals/images/money.png',
        'route' => 'donation_extended',
        'params' => array(
          'controller' => $subject->type,
          'action' => 'fininfo',
          'donation_id' => $subject->getIdentity(),
        ),
      );
    }
    return false;
  }

  public function onMenuInitialize_DonationProfilePhotoManage($row)
  {
    return false;
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $album = $subject->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    /**
     * @var $paginator Zend_Paginator
     */
    $photo = $paginator->getItem(0);

    if ($subject->getOwner()->getIdentity() == $viewer->getIdentity()) {
      return array(
        'label' => 'Manage Photos',
        'icon' => 'application/modules/User/externals/images/edit.png',
        'route' => 'donation_extended',
        'params' => array(
          'controller' => 'photo',
          'action' => 'view',
          'donation_id' => $subject->getIdentity(),
          'photo_id' => $photo->getIdentity()
        ),
      );
    }
    return false;
  }

  public function onMenuInitialize_DonationPageBrowseCharity($row)
  {
    if (!$this->getSetting('donation.enable.charities')) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();

    return array(
      'label' => 'Charity',
      'href' => $subject->getHref() . '/content/charity_donations',
      'onClick' => 'javascript:donation.charity_list(); return false;',
      'route' => 'donation_extended',
    );
  }

  public function onMenuInitialize_DonationPageBrowseProject($row)
  {
    if (!$this->getSetting('donation.enable.projects')) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();

    return array(
      'label' => 'Projects',
      'href' => $subject->getHref() . '/content/project_donations',
      'onClick' => 'javascript:donation.project_list(); return false;',
      'route' => 'donation_extended',
    );
  }

  public function onMenuInitialize_DonationPageManageDonations($row)
  {
    /**
     * @var $subject Page_Model_Page
     * @var $viewer User_Model_User
     */
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject && $viewer->getIdentity() && $subject->isDonation() && $subject->isAllowDonation()) {
      return array(
        'label' => 'Manage Donations',
        'href' => 'javascript:void(0);',
        'onClick' => 'javascript:donation.manage_list();',
        'route' => 'donation_extended',
      );
    }

    return false;
  }

  public function onMenuInitialize_DonationQuickCreateCharity($row)
  {
    return $this->getDonationApi()->canCreateCharity();
  }

  public function onMenuInitialize_DonationQuickCreateProject($row)
  {
    return $this->getDonationApi()->canCreateProject();
  }

  public function onMenuInitialize_DonationProfileSuggest($row)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest')) {
      return false;
    }
    $showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.donation');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_DonationMainManageDonations($row)
  {
    if ($this->getDonationApi()->canCreateCharity() || $this->getDonationApi()->canCreateProject()) {
      return true;
    }

    return false;
  }

  public function onMenuInitialize_DonationProfileStatistics($row)
  {
    /**
     * @var $subject Donation_Model_Donation
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if (!$subject) {
      return false;
    }

    $page = $subject->getPage();
    $allowView = true;


    if ($page) {
      if (!$page->getDonationPrivacy($subject->type)) {
        $allowView = false;
      }
    }
    elseif (!$subject->isOwner($viewer)) {
      $allowView = false;
    }

    if ($subject->status != 'active' || !$subject->approved) {
      $allowView = false;
    }

    if ($allowView) {
      return array(
        'label' => 'DONATION_Profile_statistic',
        'icon' => 'application/modules/Donation/externals/images/statistics.png',
        'route' => 'donation_extended',
        'params' => array(
          'module' => 'donation',
          'controller' => 'statistics',
          'action' => 'index',
          'donation_id' => $subject->getIdentity()),
      );
    }

    return false;
  }
}