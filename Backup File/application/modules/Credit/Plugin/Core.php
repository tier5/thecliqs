<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-01-04 13:05 teajay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Plugin_Core
{
  public function onItemCreateAfter($event)
  {
    /**
     * @var $api Credit_Api_Core
     */
    $object = $event->getPayload();
    $api = Engine_Api::_()->credit();
    if ($object->getType() == 'activity_action') {
      $user = Engine_Api::_()->getItem('user', $object->subject_id);
      $api->updateCredits($user, $object);
    } else {
      $api->updateItemCredits(Engine_Api::_()->user()->getViewer(), $object);
    }
  }

  public function onUserLoginAfter($event)
  {
    /**
     * @var $api Credit_Api_Core
     */
    $user = $event->getPayload();
    $api = Engine_Api::_()->credit();
    $api->updateItemCredits($user, $user);
  }

  public function onInviterSendInvite($event)
  {
    /**
     * @var $api Credit_Api_Core
     */
    $invite = $event->getPayload();
    $api = Engine_Api::_()->credit();
    $api->updateInviteCredits($invite);
  }

  public function onInviterRefered($event)
  {
    /**
     * @var $api Credit_Api_Core
     */
    $invite = $event->getPayload();
    $api = Engine_Api::_()->credit();
    $api->updateInviteCredits($invite);
  }

  public function onUserDeleteBefore($event)
  {
    /**
     * @var $user User_Model_User
     * @var $balance Credit_Model_Balance
     */

    $user = $event->getPayload();
    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());
    if ($balance) {
      $balance->delete();
    }
  }

  public function onPageVisit($event)
  {
    /**
     * @var $api Credit_Api_Core
     */
    $page = $event->getPayload();
    $api = Engine_Api::_()->credit();
    $api->updatePageVisitCredits($page);
  }

  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();
    if (!($view instanceof Zend_View_Interface)) {
      return;
    }

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      return;
    }

    /**
     * @var $contentTbl Page_Model_DbTable_Content
     */
    $contentTbl = Engine_Api::_()->getDbTable('content', 'core');
    $select = $contentTbl->select()->where("name = 'credit.buy-level'");
    $count = $contentTbl->fetchAll($select)->count();
    if (!$count) {
      return;
    }

    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if (!empty($params['module']) && $params['module'] == 'payment'
      && !empty($params['controller']) && $params['controller'] == 'subscription'
      && !empty($params['action']) && $params['action'] == 'gateway'
    ) {
      $script = $view->partial('_paymentButtonJS.tpl', 'credit');
      $view->headScript()->appendScript($script);
    }
  }

  public function onUserUpdateBefore($event)
  {
    $user = $event->getPayload();

    if (!($user instanceof User_Model_User)) {
      return;
    }

    if (Zend_Controller_Front::getInstance()->getRequest()->getModuleName() == 'user' &&
      Zend_Controller_Front::getInstance()->getRequest()->getControllerName() == 'edit' &&
      Zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'profile'
    ) {
      $creditApi = Engine_Api::_()->getApi('core', 'credit');
      $creditApi->updateUserProfileCredits($user);
    }
  }


  public function onWallPostAction($event)
  {
    $object = $event->getPayload();
    $object = $object['token'];
    $user = Engine_Api::_()->getItem('user', $object->user_id);
    $creditApi = Engine_Api::_()->getApi('core', 'credit');
    $creditApi->updateWallServicesPostCredits($user, $object);
  }

  public function onWallPostStatus($event)
  {
    $object = $event->getPayload();
    $object = $object['token'];
    $user = Engine_Api::_()->getItem('user', $object->user_id);
    $creditApi = Engine_Api::_()->getApi('core', 'credit');
    $creditApi->updateWallServicesPostCredits($user, $object);
  }
}