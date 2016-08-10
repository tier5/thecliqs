<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: IndexController.php 03.02.12 12:21 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $action = $this->_getParam('action', false);
    if( $action == 'approve' || $action == 'active') {
      return $this->_forward($action, null, null, $this->_getAllParams());
    }

    /**
     * @var $giftsTbl Hegift_Model_DbTable_Gifts
     */

    $giftsTbl = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $gifts = $giftsTbl->getGifts(array('enabled' => true, 'date' => true));

    foreach ($gifts as $gift) {
      $gift->starttime = date("Y-m-d H:i:s", mktime(1,0,0, date("m", strtotime($gift->starttime)), date("d", strtotime($gift->starttime)), date("Y", strtotime($gift->starttime))+1));
      $gift->endtime = date("Y-m-d H:i:s", mktime(1,0,0, date("m", strtotime($gift->endtime)), date("d", strtotime($gift->endtime)), date("Y", strtotime($gift->endtime))+1));
      $gift->save();
    }

    $user_id = $this->_getParam('user_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    $pages = Engine_Api::_()->getApi('menus', 'core')
      ->getMenuParams('hegift_main');

    if( $user_id ) {
      $user = Engine_Api::_()->getItem('user', $user_id);
      //Hide Browse Gifts for non-friends
      if( !$user->membership()->isMember($viewer) ) {
        foreach( $pages as $key=>$page ) {
          if( substr($page['class'], strpos($page['class'], ' ') + 1) == 'hegift_main_index' ) {
            unset($pages[$key]);
          }
        }
      }
    }
    // Get navigation
    $navigation = new Zend_Navigation();
    $navigation->addPages($pages);
    $this->view->navigation = $navigation;
  }

  public function indexAction()
  {
    $user_id = $this->_getParam('user_id');
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $user_id ) {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if( !$user->membership()->isMember($viewer) ) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
      }
    }

    //if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid() ) return;
    // Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled()
    ;
  }

  public function manageAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     * @var $paginator Zend_Paginator
     * @var $viewer User_Model_User
     */

    if( !$this->_helper->requireUser()->isValid() ) return;
    /*$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_main', array(), 'hegift_main_manage');*/

    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
    $this->view->action_name = $action_name = $this->_getParam('action_name', 'received');

    $page = $this->_getParam('page', 1);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->paginator = $paginator = $table->getPaginator(array('user_id' => $viewer->getIdentity(), 'action_name' => $action_name, 'page' => $page, 'ipp' => 20));

    $this->view->active_recipient_id = $this->getActiveRecipientId();
    $this->view->received_gifts_count = $table->getPaginator(array('user_id' => $viewer->getIdentity(), 'action_name' => 'received'))->getTotalItemCount();
    $this->view->sent_gifts_count = $table->getPaginator(array('user_id' => $viewer->getIdentity(), 'action_name' => 'sent'))->getTotalItemCount();
    $this->view->storage = Engine_Api::_()->storage();
  }

  public function approveAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $value = $this->_getParam('value', 1);
    $viewer = Engine_Api::_()->user()->getViewer();
    $recipient_id = $this->_getParam('recipient_id', 0);
    if (!$recipient_id) {
      return ;
    }
    $recipient = Engine_Api::_()->getItem('hegift_recipient', $recipient_id);
    if (!$this->checkGift($recipient->getGift(), $recipient)) {
      return ;
    }

    $active_recipient_id = $this->getActiveRecipientId();

    if ($active_recipient_id == $recipient_id && $value == 0) {
      $this->setSetting($viewer, 'active_gift', 0);
    }

    $recipient->approved = $value;
    $recipient->save();
    return ;
  }

  public function activeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $recipient_id = $this->_getParam('recipient_id', 0);
    $recipient = Engine_Api::_()->getItem('hegift_recipient', $recipient_id);
    if (!$recipient->getIdentity()) {
      return ;
    }

    if (!$this->checkGift($recipient->getGift(), $recipient)) {
      return ;
    }

    $active_recipient_id = $this->getActiveRecipientId();

    $value = $recipient->getIdentity();

    if ($active_recipient_id == $value) {
      $value = 0; // unset activated photo
    }

    $this->setSetting($viewer, 'active_gift', $value);
    return ;
  }

  public function sendAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     * @var $gift Hegift_Model_Gift
     * @var $notificationTable Activity_Model_DbTable_Notifications
     * @var $sender User_Model_User
     */
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    $this->view->user = $sender = Engine_Api::_()->user()->getViewer();
    if (!$sender->getIdentity()) {
      return $this->_helper->layout->disableLayout(true);
    }
    $values = array();
    $translate = Zend_Registry::get('Zend_Translate');
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');
    if ($this->_getParam('return', 0)) {
      $this->_redirectCustom($this->view->url(array('gift_id' => $gift->getIdentity()), 'hegift_general', true));
    }

    if (!$gift) {
      $this->view->message = $translate->_('HEGIFT_Gift is not found');
      return ;
    } else {
      $values['gift_id'] = $gift->getIdentity();
    }

    if ($gift->owner_id) {
      if ($sender->getIdentity() != $gift->owner_id) {
        $this->view->message = $translate->_('HEGIFT_This is not your Gift');
        return ;
      }
      if ($gift->isSent()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent');
        return ;
      }
      if ($gift->getStatus()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent from you. Please reload this page.');
        return ;
      }
    }

    $recipients = $this->_getParam('recipients', array());
    if (!count($recipients)) {
      $this->view->message = $translate->_('HEGIFT_You didn\'t select the recipient.');
      return ;
    }

    $balance = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity());
    if (!$balance) {
      $this->view->message = $translate->_('HEGIFT_Not enough credits to send a gift.');
      return ;
    }
    $credits = count($recipients)*$gift->credits;
    if ($credits > $balance->current_credit) {
      $this->view->message = $translate->_('HEGIFT_Not enough credits to send a gift.');
      return ;
    }

    $values['message'] = trim(strip_tags($this->_getParam('message', '')));
    $values['privacy'] = (int)$this->_getParam('privacy', 1);
    if ($values['privacy']) {
      $values['privacy'] = 1;
    }
    $values['subject_id'] = $sender->getIdentity();
    $values['send_date'] = new Zend_Db_Expr("NOW()");

    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');

    $counter = 0;
    $db = $table->getAdapter();
    $db->beginTransaction();

    if ($gift->owner_id == $sender->getIdentity() && $gift->type == 3) {
      $values['approved'] = 0;

      // Sending gift
      try {
        foreach($recipients as $recipient) {
          if ($table->checkGiftForUser($recipient, $values['gift_id'])) {
            continue;
          }
          $values['object_id'] = $recipient;
          $row = $table->createRow();
          $row->setFromArray($values);
          $row->save();
          $counter++;
        }

        $gift->temporaryPay($counter);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      Engine_Api::_()->getDbtable('jobs', 'core')->addJob('gift_video_encode', array(
        'gift_id' => $gift->getIdentity(),
      ));

      $this->view->result = 1;
      $this->view->message = $translate->_('HEGIFT_Gift will be sent only when the video will be ready. If the video is an error, we will refund all the credits');

      $task = Engine_Api::_()->hegift()->getTask('core');
      $class = $task->plugin;
      $manualHook = new $class($task);
      $manualHook->execute();
      return ;
    }

    // Sending gift
    try {
      foreach($recipients as $recipient) {
        if ($table->checkGiftForUser($recipient, $values['gift_id'])) {
          continue;
        }
        $values['object_id'] = $recipient;
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $counter++;
        //activity feed
        if ($values['privacy']) {
          $action = Engine_Api::_()->getDbTable('actions', 'activity')->addActivity($sender, Engine_Api::_()->getItem('user', $recipient), 'sent_gift');
          if( $action ) {
            Engine_Api::_()->getDbTable('actions', 'activity')->attachActivity($action, $gift);
          }
        }
        
        //send notification
        $notificationTable->addNotification(Engine_Api::_()->getItem('user', $recipient), $sender, Engine_Api::_()->getItem('user', $recipient), 'send_gift', array(
          'action' => $this->view->url(array('action' => 'manage'), 'hegift_general', true),
          'label' => $translate->_('HEGIFT_here'),
          'object_link' => $this->view->url(array('action' => 'manage'), 'hegift_general')
        ));
      }

      $gift->payOff($counter);
      $gift->updateGift($counter);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->result = 1;
    $this->view->message = $translate->_('HEGIFT_Gift successfully sent.');
    return ;
  }

  public function videoAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    $this->view->recipient = $recipient = Engine_Api::_()->getItem('hegift_recipient', $this->_getParam('recipient_id', 0));

    if (!$this->checkGift($gift, $recipient)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return ;
    }

    $file_id = $gift->file_id;

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $this->view->video_location = $storage_file->map();
      }
    }
  }

  public function videoPreviewAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    $file_id = $gift->file_id;

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $this->view->video_location = $storage_file->map();
      }
    }
  }

  public function audioAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    $this->view->recipient = $recipient = Engine_Api::_()->getItem('hegift_recipient', $this->_getParam('recipient_id', 0));
    $file_id = $gift->file_id;

    if (!$this->checkGift($gift, $recipient)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return ;
    }

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $this->view->audio_location = $storage_file->map();
      }
    }
  }

  public function photoAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    $this->view->recipient = $recipient = Engine_Api::_()->getItem('hegift_recipient', $this->_getParam('recipient_id', 0));

    if (!$this->checkGift($gift, $recipient)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return ;
    }
  }

  protected function setSetting(User_Model_User $user, $key, $value)
  {
    /**
     * @var $settings User_Model_DbTable_Settings
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'user');
    if( null === $value ) {
      $settings->delete(array(
        'user_id = ?' => $user->getIdentity(),
        'name = ?' => $key,
      ));
    } else if( false === ($prev = $settings->getSetting($user, $key)) ) {
      $settings->insert(array(
        'user_id' => $user->getIdentity(),
        'name' => $key,
        'value' => $value,
      ));
    } else {
      $settings->update(array(
        'value' => $value,
      ), array(
        'user_id = ?' => $user->getIdentity(),
        'name = ?' => $key,
      ));
    }

    return $settings;
  }

  protected function checkGift($gift, $recipient)
  {
    if (!$gift || !$recipient) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($gift->getIdentity() != $recipient->gift_id || ($viewer->getIdentity() != $recipient->object_id && $viewer->getIdentity() != $recipient->subject_id)) {
      return false;
    }
    return true;
  }

  protected function getActiveRecipientId()
  {
    /**
     * @var $settings User_Model_DbTable_Settings
     */

    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getDbTable('settings', 'user');
    return $settings->getSetting($viewer, 'active_gift');
  }
}