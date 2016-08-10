<?php

class Donation_IndexController extends Core_Controller_Action_Standard
{
  protected $_settings;

  public function init()
  {
    $this->_settings = Engine_Api::_()->getApi('settings', 'core');

    /**
     * @var $subject Donation_Model_Donation
     */
    $subject = null;

    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      $id = $this->_getParam('donation_id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('donation', $id);
        Engine_Api::_()->core()->setSubject($subject);
      }
    }
    if($subject){
      if($subject->type == 'charity'){
        if(!$this->_settings->getSetting('donation.enable.charities',1)){
          if($this->_settings->getSetting('donation.enable.projects',1)){
            return $this->_helper->redirector->gotoRoute(array(),'donation_project_browse',true);
          }
          else{
            return $this->_helper->redirector->gotoRoute(array(),'donation_fundraise_browse',true);
          }
        }
      }
      else{
        if(!$this->_settings->getSetting('donation.enable.projects',1)){
          if($this->_settings->getSetting('donation.enable.fundraising',1)){
            return $this->_helper->redirector->gotoRoute(array(),'donation_fundraise_browse',true);
          }
          else{
            return $this->_helper->redirector->gotoRoute(array(),'donation_charity_browse',true);
          }
        }
      }
    }
  }

  public function browseAction()
  {
    //Check enabled the charity type
    if(!$this->_settings->getSetting('donation.enable.charities',1)){
      if($this->_settings->getSetting('donation.enable.projects',1)){
        return $this->_helper->redirector->gotoRoute(array(),'donation_project_browse',true);
      }
      else{
        return $this->_helper->redirector->gotoRoute(array(),'donation_fundraise_browse',true);
      }
    }

    $this->view->currency = $this->_settings->getSetting('payment.currency', 'USD');


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
      'type' => 'charity',
      'status' => 'active',
      'approved' => 1,
      'ipp' => $this->_settings->getSetting('donation_browse_count', 10),
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
    foreach($paginator as $donation)
    {
      $like_count[$donation->getIdentity()] = Engine_Api::_()->like()->getLikeCount($donation);
      $params['resource_id'] = $donation->getIdentity();
      $select_supporters = Engine_Api::_()->like()->getLikesSelect($params);
      $supporters[$donation->getIdentity()] = $select_supporters->query()->fetchAll();
    }
    $this->view->like_count = $like_count;
    $this->view->supporters = $supporters;
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('donation', null, 'create_charity');

    // Render
    $this->_helper->content
    //->setNoRender()
      ->setEnabled();
  }

  public function createAction()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('donation', null, 'create_charity')->isValid()) return;

    //Prepare form
    $this->view->form = $form = new Donation_Form_Admin_Donations_Create();

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
      $viewer = Engine_Api::_()->user()->getViewer();
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
      ));

      $donation = $table->createRow();
      $donation->setFromArray($values);
      $donation->save();

      //Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  public function viewAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $subject = Engine_Api::_()->core()->getSubject();


    if(!$subject || $subject->status == 'cancelled'){
      return $this->_forward('requiresubject', 'error', 'core');
    }

    if(!$subject->approved){
      $page = $subject->getPage();
      if($page){
        if(!$page->getDonationPrivacy($subject->type)){
          return $this->_forward('requiresubject', 'error', 'core');
        }
      }
      elseif(!$subject->isOwner($viewer)){
        return $this->_forward('requiresubject', 'error', 'core');
      }
    }

    if (!$subject->getOwner()->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }

//       Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function deleteAction()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->_getParam('donation_id', 0));

    if ($donation != null && !_ENGINE_ADMIN_NEUTER) {
      $donation->delete();
    }

    $this->_redirect('');
  }

  public function mapAction()
  {
    $donation_id = (int)$this->_getParam('donation_id');
    if ($donation_id === null) {
      $this->view->result = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Undefined donation.");
      return;
    }

    $table = Engine_Api::_()->getDbTable('donations', 'donation');

    $select = $table
      ->select()->setIntegrityCheck(false)
      ->from(array('donation' => 'engine4_donation_donations'))
      ->joinLeft(array('marker' => 'engine4_donation_markers'), 'marker.donation_id = donation.donation_id',
      array('marker_id', 'latitude', 'longitude'))
      ->where('donation.donation_id = ?', $donation_id);

    $this->view->donation = $donation = $table->fetchRow($select);
    $markers = array();

    if ($donation->marker_id > 0) {
      $markers[0] = array(
        'marker_id' => $donation->marker_id,
        'lat' => $donation->latitude,
        'lng' => $donation->longitude,
        'donation_id' => $donation->donation_id,
        'donation_photo' => $donation->getPhotoUrl('thumb.normal'),
        'title' => $donation->getTitle(),
        'desc' => substr($donation->getDescription(false,false), 0, 200),
        'url' => $donation->getHref()
      );

      $this->view->markers = Zend_Json_Encoder::encode($markers);
      $this->view->bounds = Zend_Json_Encoder::encode(Engine_Api::_()->getApi('gmap', 'donation')->getMapBounds($markers));
    }
  }

  public function manageAction()
  {
    $this->view->canCreateCharity = $canCreateCharity = $this->getDonationApi()->canCreateCharity();

    $this->view->canCreateProject = $canCreateProject = $this->getDonationApi()->canCreateProject();

    if(!$this->view->canCreateCharity && !$this->view->canCreateProject){
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_main', array(), 'donation_main_manage_donations');

    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();

    if(!$viewer->getIdentity()){
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->currency = $this->_settings->getSetting('payment.currency', 'USD');

    $params = array(
      'user_id' => $viewer->getIdentity(),
      'order' => 'DESC',
      'page' => $this->_getParam('page',1),
      'ipp' => $this->_settings->getSetting('donation_browse_count', 10),
    );

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

    $params['orderBy'] = $order;

    if ($this->_getParam('category_id')){
      $params['category_id'] = $this->_getParam('category_id');
    }

    if ($this->_getParam('search', false)) {
      $params['search'] = $this->_getParam('search');
    }

    $this->view->donations = $donations = Engine_Api::_()->getItemTable('donation')->getDonationsPaginator($params);

    $searchForm = new Donation_Form_Search();
    $searchForm->getElement('sort')->setValue($this->_getParam('sort'));
    $searchForm->getElement('search')->setValue($this->_getParam('search'));
    $category_id = $searchForm->getElement('category_id');
    if ($category_id) {
      $category_id->setValue($this->_getParam('category_id'));
    }
    $this->view->searchParams = $searchForm->getValues();
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_quick');

  }

  public function getDonationApi()
  {
    return Engine_Api::_()->getApi('core', 'donation');
  }
}