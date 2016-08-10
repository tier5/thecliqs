<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 05.09.12
 * Time: 10:30
 * To change this template use File | Settings | File Templates.
 */
class Donation_PageController extends Core_Controller_Action_Standard
{
  protected $_subject;

  protected $_allowCreateCharity;

  protected $_allowCreateProject;



  public function init()
  {
    $this->view->subject = $this->_subject = Engine_Api::_()->getItem('page',$this->_getParam('page_id', 0));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();


    if(!$this->_subject || !($this->_subject->isDonation() || $this->_subject->isOwner($viewer)) || !$this->_subject->isAllowDonation()){
      if(!$this->_getParam('format') == 'json'){
        return $this->_forward('requireauth', 'error', 'core');
      }

    }
    

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->_allowCreateCharity = true;
    $this->_allowCreateProject = true;

    if(!$settings->getSetting('donation.enable.charities',1) || !$this->_subject->getDonationPrivacy('charity')){
      $this->_allowCreateCharity = false;
    }

    if(!$settings->getSetting('donation.enable.projects',1) || !$this->_subject->getDonationPrivacy('project')){
      $this->_allowCreateProject = false;
    }

    $api = Engine_Api::_()->getApi('page', 'donation');
    $this->view->navigation = $api->getNavigation($this->_subject);
    $this->view->currency = $settings->getSetting('payment.currency', 'USD');
  }

  public function indexAction()
  {
    if(!$this->_allowCreateCharity && !$this->_allowCreateProject){
      return $this->_forward('requireauth', 'error', 'core');
    }

    $params = array(
      'page_id' => $this->_subject->getIdentity(),
      'ipp' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('donation_browse_count', 10),
      'page' => $this->_getParam('page', 1),
      'order' => 'DESC',
    );

    if($this->_allowCreateCharity && !$this->_allowCreateProject){
      $params['type'] = 'charity';
    }
    elseif($this->_allowCreateProject && !$this->_allowCreateCharity){
      $params['type'] = 'project';
    }

    $this->view->donations = $this->getTable()->getDonationsPaginator($params);

    $this->view->isAllowPostCharity = $this->_allowCreateCharity;
    $this->view->isAllowPostProject = $this->_allowCreateProject;

    if ($this->_getParam('format') == 'json') {
      $this->view->html = $this->view->render('donations_list_edit.tpl');
    }
  }

  public function browseAction()
  {
    $donation_params = array(
      'page_id' => $this->_subject->getIdentity(),
      'ipp' => $this->_getParam('itemCountPerPage', 10),
      'page' => $this->_getParam('page', 1),
      'status' => 'active',
      'approved' => 1
    );

    $type = $this->_getParam('type');

    if($type){
      $donation_params['type'] = $type;
    }
    $this->view->donations = $donations = $this->getTable()->getDonationsPaginator($donation_params);

    $like_count = array();
    $supporters = array();
    $params = array(
      'limit' => 7,
      'resource_type' => 'donation',
    );

    //Get Supporters
    foreach($donations as $donation)
    {
      $like_count[$donation->getIdentity()] = Engine_Api::_()->like()->getLikeCount($donation);
      $params['resource_id'] = $donation->getIdentity();
      $select_supporters = Engine_Api::_()->like()->getLikesSelect($params);
      $supporters[$donation->getIdentity()] = $select_supporters->query()->fetchAll();
    }
    $this->view->like_count = $like_count;
    $this->view->supporters = $supporters;

    if(!$this->_subject || !($this->_subject->isDonation() || $this->_subject->isOwner($viewer)) || !$this->_subject->isAllowDonation()){
      $this->view->html = '';
    }
    elseif($type){
      if($type == 'charity'){
        $this->view->html = $this->view->render('charity_list.tpl');
      }
      else{
        $this->view->html = $this->view->render('project_list.tpl');
      }
    }
    else{
      //todo render view list of all donations on this page for manage
      $this->view->html = $this->view->render('charity_list.tpl');
    }
  }

  public function getTable()
  {
    return Engine_Api::_()->getDbTable('donations','donation');
  }
}
