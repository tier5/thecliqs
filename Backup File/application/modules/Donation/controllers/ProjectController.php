<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Donation
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @author adik
 * @date 07.08.12
 * @time 10:37
 */
class Donation_ProjectController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('donation');
    $path = dirname($path) . '/views/scripts';

    $this->view->addScriptPath($path);

    $this->view->page_id = $this->_getParam('page_id');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    if (!$settings->getSetting('donation.enable.projects', 1)) {
      if ($this->view->page_id) {
        return $this->_helper->redirector->gotoRoute(array(), 'default');
      } elseif ($settings->getSetting('donation.enable.charities', 1)) {
        return $this->_helper->redirector->gotoRoute(array(), 'donation_charity_browse', true);
      } else {
        return $this->_helper->redirector->gotoRoute(array(), 'donation_fundraise_browse', true);
      }
    }


    if ($this->view->page_id) {
      $this->view->subject = $this->subject = Engine_Api::_()->getItem('page', $this->view->page_id);
      if ($this->subject) {
        $api = Engine_Api::_()->getApi('page', 'donation');
        $this->view->navigation = $api->getNavigation($this->subject);
      }
    }
  }

  public function indexAction()
  {
    $donation_params = array(
      'page_id' => $this->view->page_id,
      'ipp' => $this->_getParam('itemCountPerPage', 10),
      'page' => $this->_getParam('page', 1),
      'type' => 'project',
      'status' => 'active'
    );

    $this->view->paginator = $donations = $this->getTable()->getDonationsPaginator($donation_params);

    $like_count = array();
    $supporters = array();
    $params = array(
      'limit' => 7,
      'resource_type' => 'donation',
    );

//Get Supporters
    foreach ($donations as $donation) {
      $like_count[$donation->getIdentity()] = Engine_Api::_()->like()->getLikeCount($donation);
      $params['resource_id'] = $donation->getIdentity();
      $select_supporters = Engine_Api::_()->like()->getLikesSelect($params);
      $supporters[$donation->getIdentity()] = $select_supporters->query()->fetchAll();
    }
    $this->view->like_count = $like_count;
    $this->view->supporters = $supporters;

    $this->view->html = $this->view->render('project_list.tpl');
  }

  public function browseAction()
  {

    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $settings = Engine_Api::_()->getApi('settings', 'core');

// Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


// Prepare data
    $table = Engine_Api::_()->getItemTable('donation');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $browse_params = array(
      'type' => 'project',
      'status' => 'active',
      'approved' => 1,
      'ipp' => $settings->getSetting('donation_browse_count', 10),
      'page' => $this->_getParam('page', 1),
      'orderBy' => $order,
    );

    if ($this->_getParam('category_id')) {
      $browse_params['category_id'] = $this->_getParam('category_id');
    }

    if ($this->_getParam('search', false)) {
      $browse_params['search'] = $this->_getParam('search');
    }
    $paginator = $this->view->paginator = $table->getDonationsPaginator($browse_params);

    $searchForm = new Donation_Form_Search();
    $searchForm->getElement('sort')->setValue($this->_getParam('sort'));
    $searchForm->getElement('search')->setValue($this->_getParam('search'));
    $category_id = $searchForm->getElement('category_id');
    if ($category_id) {
      $category_id->setValue($this->_getParam('category_id'));
    }
    $this->view->searchParams = $searchForm->getValues();

    $like_count = array();
    $supporters = array();
    $params = array(
      'limit' => 7,
      'resource_type' => 'donation',
    );

//Get Supporters
    foreach ($paginator as $donation) {
      $like_count[$donation->getIdentity()] = Engine_Api::_()->like()->getLikeCount($donation);
      $params['resource_id'] = $donation->getIdentity();
      $select_supporters = Engine_Api::_()->like()->getLikesSelect($params);
      $supporters[$donation->getIdentity()] = $select_supporters->query()->fetchAll();
    }
    $this->view->like_count = $like_count;
    $this->view->supporters = $supporters;
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('donation', null, 'create_project');

// Render
    $this->_helper->content
//->setNoRender()
      ->setEnabled();
  }

  public function createAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }


    if (null !== ($page_id = $this->_getParam('page_id'))) {
      $page = Engine_Api::_()->getItem('page', $page_id);
      if ($page) {
        if (!($page->isDonation() || $page->isOwner($viewer)) || !$page->isAllowDonation() || !$page->getDonationPrivacy('project')) {
          return $this->_forward('requiresubject', 'error', 'core');
        }
      } else {
        return $this->_forward('requiresubject', 'error', 'core');
      }
    } elseif (!$this->_helper->requireAuth()->setAuthParams('donation', null, 'create_project')->isValid()) {
      return;
    }

// Create form
    $params = array(
      'id' => $page_id,
      'request' => $this->getRequest()
    );
    $this->view->form = $form = new Donation_Form_CreateProject($params);
    $form->getDecorator('description')->setOption('escape', false);
// Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'donation')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $key => $values) {
      $categoryOptions[$key] = $values;
    }
    $form->category_id->setMultiOptions($categoryOptions);

// If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
//Process
    $table = Engine_Api::_()->getItemTable('donation');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
//Create Donation
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
        'type' => 'project',
        'page_id' => $form->hasPage() ? $page_id : 0,
      ));

// Convert times
      if ($values['expiry_date'] != '0000-00-00') {
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($oldTz);
        $expiry_date = strtotime($values['expiry_date']);
        $values['expiry_date'] = date('Y-m-d H:i:s', $expiry_date);
      } else {
        $values['expiry_date'] = '2019-01-01 00:00:00';
      }
      $donation = $table->createRow();
      $donation->setFromArray($values);

// Set photo
      if (!empty($values['photo'])) {
        $donation->setPhoto($form->photo);
      }

      if ($donation->save()) {
        // Auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }

        $viewMax = array_search($values['auth_view'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($donation, $role, 'view', ($i <= $viewMax));
        }
        $auth->setAllowed($donation, 'everyone', 'comment', 1);
        $auth->setAllowed($donation, 'everyone', 'order', 1);

        $address = !empty($values['street']) ? strip_tags($values['street']) : '';
        $address .= !empty($values['city']) ? ', ' . strip_tags($values['city']) : '';
        $address .= !empty($values['state']) ? ', ' . strip_tags($values['state']) : '';
        $address .= !empty($values['country']) ? ', ' . strip_tags($values['country']) : '';
        $values['phone'] = strip_tags($values['phone']);
        $address = urlencode($address);
        $url = 'http://maps.google.com/maps/geo?&q=' . $address . '&output=csv';

        if (($result = file_get_contents($url)) != false) {
          $coordinates = Engine_Api::_()->getApi('core', 'donation')->str_getcsv($result);
          $donationMarker = $donation->getMarker(true);
          if ($coordinates[0] == 200) {
            $donationMarker->latitude = $coordinates[2];
            $donationMarker->longitude = $coordinates[3];
          } else {
            $donationMarker->latitude = 0;
            $donationMarker->longitude = 0;
          }
          $donationMarker->save();
        }
        $album = $donation->getSingletonAlbum();
        $ids = explode(' ', $this->_getParam('fancyuploadfileids'));
        $count = 0;

        foreach ($ids as $photo_id) {
          $photo = Engine_Api::_()->getItem("donation_photo", $photo_id);
          if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
            continue;

          $photo->donation_id = $donation->getIdentity();
          $photo->collection_id = $album->album_id;
          $photo->album_id = $album->album_id;
          $photo->save();
          $count++;
        }
        //Set Approved
        $approved = $settings->getSetting('donation.auto.approve', 1);
        $approvedValue = $approved ? 1 : 0;
        $donation->approvedStatus($approvedValue);
      }
//Commit
      $db->commit();

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('donation_id' => $donation->getIdentity(), 'title' => $donation->getUrlTitle()), 'donation_profile', true);
  }

  public function editAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $donation = null;

    $donation = Engine_Api::_()->getItem('donation', $this->_getParam('donation_id', 0));

    if (!$donation || $donation->type != 'project') {
      return $this->_forward('requiresubject', 'error', 'core');
    }

    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      Engine_Api::_()->core()->setSubject($donation);
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->donation = $donation;

    $page = $donation->getPage();

    if ($page) {
      if (!$page->getDonationPrivacy($donation->type)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      $this->view->subject = $page;
      $api = Engine_Api::_()->getApi('page', 'donation');
      $this->view->navigation = $api->getNavigation($page);
    }
    elseif (!$donation->isOwner($viewer)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $params = array(
      'request' => $this->getRequest()
    );

    $this->view->form = $form = new Donation_Form_CreateProject($params);
    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'donation')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $key => $values) {
      $categoryOptions[$key] = $values;
    }
    $form->category_id->setMultiOptions($categoryOptions);
    $form->populate($donation->toArray());

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //Process
    $table = Engine_Api::_()->getItemTable('donation');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {

      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
        'status' => $donation->status,
      ));

      // Convert times
      if ($values['expiry_date'] != '0000-00-00') {
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($oldTz);
        $expiry_date = strtotime($values['expiry_date']);
        $values['expiry_date'] = date('Y-m-d H:i:s', $expiry_date);
      } else {
        $values['expiry_date'] = '2019-01-01 00:00:00';
      }

// Set photo
      if (!empty($values['photo'])) {
        $donation->setPhoto($form->photo);
      }
      $donation->setFromArray($values);
      if ($donation->save()) {
        $values['street'] = strip_tags($values['street']);
        $values['city'] = strip_tags($values['city']);
        $values['state'] = strip_tags($values['state']);
        $values['country'] = strip_tags($values['country']);

        $address = array($values['country'], $values['state'], $values['city'], $values['street']);

        if ($address[0] == '' && $address[1] == '' && $address[2] == '' && $address[3] == '') {
          $donation->deleteMarker();
        } elseif ($donation->isAddressChanged($address)) {
          $donation->addMarkerByAddress($address);
        }

        $address = !empty($values['street']) ? $values['street'] : '';
        $address .= !empty($values['city']) ? ', ' . $values['city'] : '';
        $address .= !empty($values['state']) ? ', ' . $values['state'] : '';
        $address .= !empty($values['country']) ? ', ' . $values['country'] : '';
        $values['phone'] = strip_tags($values['phone']);
        $address = urlencode($address);
        $url = 'http://maps.google.com/maps/geo?&q=' . $address . '&output=csv';
        if (($result = file_get_contents($url)) != false) {
          $coordinates = Engine_Api::_()->getApi('core', 'donation')->str_getcsv($result);
          $donationMarker = $donation->getMarker(true);
          if ($coordinates[0] == 200) {
            $donationMarker->latitude = $coordinates[2];
            $donationMarker->longitude = $coordinates[3];
          } else {
            $donationMarker->latitude = 0;
            $donationMarker->longitude = 0;
          }
          $donationMarker->save();
        }
        $album = $donation->getSingletonAlbum();
        $ids = explode(' ', $this->_getParam('fancyuploadfileids'));
        $count = 0;

        foreach ($ids as $photo_id) {
          $photo = Engine_Api::_()->getItem("donation_photo", $photo_id);
          if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
            continue;

          $photo->donation_id = $donation->getIdentity();
          $photo->collection_id = $album->album_id;
          $photo->album_id = $album->album_id;
          $photo->save();
          $count++;
        }
      }
//Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('donation_id' => $donation->getIdentity(), 'title' => $donation->getUrlTitle()), 'donation_profile', true);

  }

  public function deleteAction()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->getRequest()->getParam('donation_id'));

    if (!$donation || $donation->type != 'project') {
      return $this->_forward('requiresubject', 'error', 'core');
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $page = $donation->getPage();


    if ($page) {
      if (!$page->getDonationPrivacy('project')) {
        return $this->_forward('requireauth', 'error', 'core');
      }
    } elseif (!$donation->isOwner($viewer)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

// In smoothbox
    $this->_helper->layout->setLayout('default-simple');

// Make form
    $this->view->form = $form = new Donation_Form_Delete();

    if (!$donation) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Donation doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $donation->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $donation->deleteDonation();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected donation has been deleted.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(''), 'donation_project_browse', true),
      'messages' => Array($this->view->message)
    ));
  }

  public function getTable()
  {
    return Engine_Api::_()->getDbTable('donations', 'donation');
  }

  public function fininfoAction()
  {
    $this->view->form = $form = new Donation_Form_FinancialInfo();
    $donation_id = $this->_getParam('donation_id');

    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      if (null !== $this->_getParam('donation_id')) {
        $subject = Engine_Api::_()->getItem('donation', $donation_id);
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $table = Engine_Api::_()->getItemTable('donation_fin_info');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = array_merge($form->getValues(), array(
        'donation_id' => $donation_id
      ));
      $fininfo = $table->createRow();
      $fininfo->setFromArray($values);
      $fininfo->save();
      $subject->status = 'active';
      if ($subject->save()) {
        $subject->addAction();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('donation_id' => $donation_id, 'title' => $subject->getUrlTitle()), 'donation_profile', true);
  }
}