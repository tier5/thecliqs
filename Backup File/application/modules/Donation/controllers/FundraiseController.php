<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       09.08.12
 * @time       11:18
 */
class Donation_FundraiseController extends Core_Controller_Action_Standard
{

  protected $_settings;

  public function init()
  {

    $this->_settings = Engine_Api::_()->getApi('settings', 'core');

    if(!$this->_settings->getSetting('donation.enable.fundraising',1)){
      if($this->_settings->getSetting('donation.enable.charities',1)){
        return $this->_helper->redirector->gotoRoute(array(),'donation_charity_browse',true);
      }
      else{
        return $this->_helper->redirector->gotoRoute(array(),'donation_project_browse',true);
      }
    }
  }

  public function indexAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    if( !$this->_helper->requireAuth()->setAuthParams('donation', null, 'raise_money')->isValid()) return;

    $donation_id = $this->_getParam('donation_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $params = array(
      'id' => $donation_id,
    );
    $this->view->form =  $form = new Donation_Form_CreateFundraiseConfirm($params);
    $form->getDecorator('description')->setOption('escape', false);

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRedirectTime' => 10,
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'controller' => 'fundraise',
        'action' => 'create',
        'donation_id' => $donation_id),
          'donation_extended', true),
      'messages' => array(''),
    ));
  }

  public function browseAction()
  {
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
      'type' => 'fundraise',
      'status' => 'active',
      'approved' => 1,
      'ipp' => $this->_settings->getSetting('donation_browse_count', 10),
      'page' => $this->_getParam('page', 1),
      'orderBy' => $order,
    );

    if ($this->_getParam('category_id')){
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
      'limit' => 6,
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
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('donation', null, 'raise_money');

    // Render
    $this->_helper->content
    //->setNoRender()
      ->setEnabled();
  }

  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    if( !$this->_helper->requireAuth()->setAuthParams('donation', null, 'raise_money')->isValid()) return;

    $donation_id = $this->_getParam('donation_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->donation = $donation = Engine_Api::_()->getItem('donation', $donation_id);

    if(!$donation || $donation->type == 'fundraise' || $donation->status != 'active'){
      return $this->_forward('requiresubject', 'error', 'core');
    }
    $params = array(
      'id' => $donation_id,
      'request' => $this->getRequest()
    );

    $this->view->form =  $form = new Donation_Form_CreateFundraise($params);
    $form->getDecorator('description')->setOption('escape', false);
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
        'type' => 'fundraise',
        'parent_id' => $donation_id,
        'status' => 'active'
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

      $fundraise = $table->createRow();
      $fundraise->setFromArray($values);
      if ($fundraise->save()) {
        // Auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if( empty($values['auth_view']) ) {
          $values['auth_view'] = 'everyone';
        }

        $viewMax = array_search($values['auth_view'], $roles);

        foreach( $roles as $i => $role ) {
          $auth->setAllowed($fundraise, $role, 'view', ($i <= $viewMax));
        }
        $auth->setAllowed($fundraise, 'everyone', 'comment', 1);
        $auth->setAllowed($fundraise, 'everyone', 'order', 1);

        $album = $fundraise->getSingletonAlbum();
        $ids = explode(' ', $this->_getParam('fancyuploadfileids'));
        $count = 0;

        foreach ($ids as $photo_id) {
          $photo = Engine_Api::_()->getItem("donation_photo", $photo_id);
          if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
            continue;

          $photo->donation_id = $fundraise->getIdentity();
          $photo->collection_id = $album->album_id;
          $photo->album_id = $album->album_id;
          $photo->save();
          $count++;
        }
        //Set Approved
        $approved =  $settings->getSetting('donation.auto.approve',1);
        $approvedValue = $approved? 1:0;
        $fundraise->approvedStatus($approvedValue);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('fundraise_id' => $fundraise->getIdentity(), 'title' => $fundraise->getUrlTitle()), 'fundraise_profile', true);
  }

  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    $donation = null;

    $donation = Engine_Api::_()->getItem('donation', $this->_getParam('donation_id',0));

    if(!$donation || !$donation->getParent()->getIdentity()){
      return $this->_forward('requiresubject', 'error', 'core');
    }

    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      Engine_Api::_()->core()->setSubject($donation);
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if(!$donation->isOwner($viewer)){
      return $this->_forward('requireauth', 'error', 'core');
    }
    $params = array(
      'id' => $donation->getIdentity(),
      'request' => $this->getRequest()
    );
    $this->view->form =  $form = new Donation_Form_CreateFundraise($params);
    $form->getDecorator('description')->setOption('escape', false);
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
        'type' => 'fundraise',
        'status' => $donation->status
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

      $donation->setFromArray($values);

      if ($donation->save()) {
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
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('fundraise_id' => $donation->getIdentity(), 'title' => $donation->getUrlTitle()), 'fundraise_profile', true);
  }

  public function viewAction()
  {
    $subject = null;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      $id = $this->_getParam('fundraise_id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('donation', $id);
        if(!$subject){
          return $this->_forward('requiresubject', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if($subject->status == 'cancelled' || (!$subject->approved && !$subject->isOwner($viewer))){
      return $this->_forward('requiresubject', 'error', 'core');
    }
    // Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function deleteAction()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->getRequest()->getParam('donation_id'));


    $viewer = Engine_Api::_()->user()->getViewer();

    if(!$donation || $donation->type != 'fundraise'){
      return $this->_forward('requiresubject', 'error', 'core');
    }

    if(!$donation->isOwner($viewer)){
      return $this->_forward('requireauth', 'error', 'core');
    }
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    // Make form
    $this->view->form = $form = new Donation_Form_Delete();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $donation->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $donation->deleteDonation();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected donation has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(''), 'donation_fundraise_browse', true),
      'messages' => Array($this->view->message)
    ));
  }
}
