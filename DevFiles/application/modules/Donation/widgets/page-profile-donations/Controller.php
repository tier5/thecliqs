<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       15.08.12
 * @time       16:46
 */
class Donation_Widget_PageProfileDonationsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    /**
     * @var $subject Page_Model_Page
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->currency = $settings->getSetting('payment.currency', 'USD');
    $this->view->itemCountPerPage = $itemCountPerPage = $this->_getParam('itemCountPerPage', 10);

    if(!$subject->approved){
      return $this->setNoRender();
    }

    if ( !($subject instanceof Page_Model_Page) ){
      return $this->setNoRender();
    }

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    if(!($subject->isDonation() || $subject->isOwner($viewer)) || !$subject->isAllowDonation()){
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('donation');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $p = 1;
    $this->view->content_info = $content_info = $subject->getContentInfo();
    $type = 'charity';

    $count_params = array(
      'page_id' => $subject->getIdentity(),
      'status' => 'active',
      'approved' => 1,
    );

    if($content_info['content'] == 'charity_donations' || $content_info['content'] == 'project_donations'){
      if(!empty($content_info['content_id']))
        $p = $content_info['content_id'];
    }
    if (!empty($content_info['content'])){
      $this->view->init_js_str = $this->getApi()->getInitJs($content_info);
    }else{
      $this->view->init_js_str = "";
    }

    if($content_info['content'] == 'project_donations'){
      $type = 'project';
    }

    if(!$settings->getSetting('donation.enable.charities',1)){
      $type = 'project';
      $count_params['type'] = $type;
    }
    elseif(!$settings->getSetting('donation.enable.projects',1)){
      $type = 'charity';
      $count_params['type'] = $type;
    }
    $this->view->type = $type;
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('donation_page');
    $this->view->donations = $donations = $this->getTable()->getDonationsPaginator(array(
      'page_id' => $subject->getIdentity(), 'ipp' => $itemCountPerPage, 'page' => $p,
      'type' => $type, 'status' => 'active', 'approved' => 1,
      ));
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

    $titleCount = $this->getTable()->getDonationsCount($count_params);
    if ($this->_getParam('titleCount', false) && $titleCount > 0){
      $this->_childCount = $titleCount;
    }
  }


  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getApi()
  {
    return Engine_Api::_()->getApi('core', 'donation');
  }

  public function getTable()
  {
    return Engine_Api::_()->getDbTable('donations','donation');
  }
}