<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 04.01.12 13:44 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Api_Core extends Core_Api_Abstract
{
  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;

  public function updateCredits(User_Model_User $user, $object)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $balance Credit_Model_Balance
     * @var $object Activity_Model_Action
     **/

    if (!$user->getIdentity()) {
      return 0;
    }

    $view = Zend_Registry::get('Zend_View');
    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $type = $object->type;
    if (($action = $actionTypes->getActionType($type)) === null) {
      return 0;
    }

    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());
    if (!$balance) {
      $balance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $balance->balance_id = $user->getIdentity();
      $balance->save();
    }

    if (!$this->getAllowedCredits($user)) {
      return 0;
    }

    if (!$table->checkCredit($action, $user->getIdentity())) {
      return 0;
    }

    $object_type = $object->object_type;
    $object_id = $object->object_id;

    $credits = (isset($object->params['count']) && !empty($object->params['count']) && is_numeric($object->params['count'])) ?
      $table->getAvailableCredits($action, $user->getIdentity(), $object->params['count']) :
      $table->getAvailableCredits($action, $user->getIdentity());

    if ($action->action_type == 'album_photo_new' || $action->action_type == 'music_playlist_new' ||
      $action->action_type == 'group_photo_upload' || $action->action_type == 'advgroup_photo_upload' || $action->action_type == 'event_photo_upload' ||
      $action->action_type == 'pagealbum_photo_new' || $action->action_type == 'pagemusic_playlist_new' ||
      $action->action_type == 'list_photo_upload' || $action->action_type == 'mp3music_album_new'
    ) {
      if (!$object->params['count']) {
        return 0;
      }
      $body = $object->params['count'];
    } elseif ($action->action_type == 'status') {
      $body = '"<i>' . $view->string()->truncate($object->body, 10, '...') . '</i>"';
      $object_type = 'activity_action';
      $object_id = $object->action_id;
    } elseif ($action->action_type == 'hequestion_ask_self') {
      $body = $object->params['question'];
    } elseif ($action->action_type == 'hequestion_ask') {
      $body = $object->params['question'];
    } elseif ($action->action_type == 'hequestion_answer') {
      $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');
      $votes = $voteTable->fetchRow($voteTable->getVoteSelect($object->object_id, $user));
      $question = Engine_Api::_()->getItem('hequestion', $votes->question_id);
      $option = $question->getOption($votes->option_id);
      $body = '<a href="/credits/question-view/' . $votes->question_id . '/question">' . $option->title . '</a>';
    } else {
      $body = new Zend_Db_Expr('NULL');
    }

    if ($action->action_type == 'group_join' || $action->action_type == 'advgroup_join' || $action->action_type == 'event_join') {
      if ($table->checkJoin($action->action_id, $object)) {
        return 0;
      }
    }

    $row = $table->createRow();
    $row->user_id = $user->getIdentity();
    $row->action_id = $action->action_id;
    $row->credit = $credits;
    $row->object_type = $object_type;
    $row->object_id = $object_id;
    $row->body = $body;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $balance->setCredits($credits);

    $row->save();
  }

  public function updateItemCredits(User_Model_User $user, $object)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $balance Credit_Model_Balance
     **/

    if (!$user->getIdentity()) {
      return 0;
    }

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    if ($object->getType() == 'user') {
      $type = 'user_login';
    } else {
      $type = $object->getType();
    }

    if (($action = $actionTypes->getActionType($type)) === null) {
      return 0;
    }

    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());
    if (!$balance) {
      $balance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $balance->balance_id = $user->getIdentity();
      $balance->save();
    }

    if (!$this->getAllowedCredits($user)) {
      return 0;
    }

    if (!$table->checkCredit($action, $user->getIdentity())) {
      return 0;
    }

    if ($action->action_type == 'music_playlist_song') {
      $object_type = 'music_playlist';
      $object_id = $object->playlist_id;
    } elseif ($action->action_type == 'mp3music_album_song') {
      $object_type = 'mp3music_album';
      $object_id = $object->album_id;
    } elseif ($action->action_type == 'core_link' || $action->action_type == 'user_login') {
      $object_type = $object->getType();
      $object_id = $object->getIdentity();
    } elseif ($action->action_type == 'core_like' || $action->action_type == 'core_comment') {
      if ($table->checkLike($action->action_id, $object)) {
        return 0;
      }
      $object_type = $object->resource_type;
      $object_id = $object->resource_id;
    } elseif ($action->action_type == 'activity_like' || $action->action_type == 'activity_comment') {
      if ($table->checkLike($action->action_id, $object)) {
        return 0;
      }
      $object_type = 'activity_action';
      $object_id = $object->resource_id;
    } elseif ($action->action_type == 'rate' || $action->action_type == 'suggest') {
      $object_type = $object->object_type;
      $object_id = $object->object_id;
    } elseif ($action->action_type == 'checkin_check') {
      $object_type = 'activity_action';
      $object_id = $object->action_id;
    } elseif ($action->action_type == 'video') {
      $object_type = 'video';
      $object_id = $object->video_id;
    } else {
      return 0;
    }

    $row = $table->createRow();
    $row->user_id = $user->getIdentity();
    $row->action_id = $action->action_id;
    $row->credit = $action->credit;
    $row->object_type = $object_type;
    $row->object_id = $object_id;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $balance->setCredits($action->credit);

    $row->save();
  }

  public function updateInviteCredits($invite)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $balance Credit_Model_Balance
     **/
    $sender = Engine_Api::_()->getItem('user', $invite->user_id);

    if (!$sender->getIdentity()) {
      return 0;
    }

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    if (!$invite->new_user_id) {
      $type = 'invite';
    } else {
      $type = 'refer';
    }

    if (($action = $actionTypes->getActionType($type)) === null) {
      return 0;
    }

    $balance = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity());
    if (!$balance) {
      $balance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $balance->balance_id = $sender->getIdentity();
      $balance->save();
    }

    if (!$this->getAllowedCredits($sender)) {
      return 0;
    }

    if (!$table->checkCredit($action, $sender->getIdentity())) {
      return 0;
    }

    if (!$invite->new_user_id) {
      $body = $invite->recipient;
      $object_type = '';
      $object_id = 0;
    } else {
      $body = new Zend_Db_Expr('NULL');
      $object_type = 'user';
      $object_id = $invite->new_user_id;
    }

    $row = $table->createRow();
    $row->user_id = $sender->getIdentity();
    $row->action_id = $action->action_id;
    $row->credit = $action->credit;
    $row->object_type = $object_type;
    $row->object_id = $object_id;
    $row->body = $body;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $balance->setCredits($action->credit);

    $row->save();
  }

  public function updateTransferCredits($sender, $recipient, $credits)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $senderBalance Credit_Model_Balance
     * @var $recipientBalance Credit_Model_Balance
     **/

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $transfer_to = $actionTypes->getActionType('transfer_to');
    $transfer_from = $actionTypes->getActionType('transfer_from');

    $tt = $table->createRow();
    $tt->user_id = $sender->getIdentity();
    $tt->action_id = $transfer_to->action_id;
    $tt->credit = (-1) * $credits;
    $tt->object_type = 'user';
    $tt->object_id = $recipient->getIdentity();
    $tt->creation_date = new Zend_Db_Expr('NOW()');

    $senderBalance = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity());
    $senderBalance->setCredits((-1) * $credits);
    $tt->save();

    $tf = $table->createRow();
    $tf->user_id = $recipient->getIdentity();
    $tf->action_id = $transfer_from->action_id;
    $tf->credit = $credits;
    $tf->object_type = 'user';
    $tf->object_id = $sender->getIdentity();
    $tf->creation_date = new Zend_Db_Expr('NOW()');

    $recipientBalance = Engine_Api::_()->getItem('credit_balance', $recipient->getIdentity());
    if (!$recipientBalance) {
      $recipientBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $recipientBalance->balance_id = $recipient->getIdentity();
      $recipientBalance->save();
    }
    $recipientBalance->setCredits($credits);
    $tf->save();
  }

  public function setCredits($recipient, $credits)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $recipientBalance Credit_Model_Balance
     * @var $recipient User_Model_User
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $recipientBalance = Engine_Api::_()->getItem('credit_balance', $recipient->getIdentity());
    if (!$recipientBalance) {
      $recipientBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $recipientBalance->balance_id = $recipient->getIdentity();
      $recipientBalance->save();
    }

    $set_credits = $actionTypes->getActionType('set_credits');

    $row = $table->createRow();
    $row->user_id = $recipient->getIdentity();
    $row->action_id = $set_credits->action_id;
    $row->credit = $credits - $recipientBalance->current_credit;
    $row->object_type = '';
    $row->object_id = 0;
    $row->body = $credits;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $recipientBalance->settingCredits($credits);
    $row->save();
  }

  public function giveCredits(User_Model_User $recipient, $credits)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $recipientBalance Credit_Model_Balance
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $recipientBalance = Engine_Api::_()->getItem('credit_balance', $recipient->getIdentity());
    if (!$recipientBalance) {
      $recipientBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $recipientBalance->balance_id = $recipient->getIdentity();
      $recipientBalance->save();
    }

    $give_credits = $actionTypes->getActionType('give_credits');

    $row = $table->createRow();
    $row->user_id = $recipient->getIdentity();
    $row->action_id = $give_credits->action_id;
    $row->credit = $credits;
    $row->object_type = '';
    $row->object_id = 0;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $recipientBalance->setCredits($credits);
    $row->save();
  }

  public function buyCredits($buyer, $credits, $service)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $buyerBalance Credit_Model_Balance
     * @var $buyer User_Model_User
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());
    if (!$buyerBalance) {
      $buyerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $buyerBalance->balance_id = $buyer->getIdentity();
      $buyerBalance->save();
    }

    $buy_credits = $actionTypes->getActionType('buy_credits');

    $row = $table->createRow();
    $row->user_id = $buyer->getIdentity();
    $row->action_id = $buy_credits->action_id;
    $row->credit = $credits;
    $row->object_type = '';
    $row->object_id = 0;
    $row->body = $service;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $buyerBalance->setCredits($credits);
    $row->save();
  }

  public function sendGift($sender, $credits, $count)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $senderBalance Credit_Model_Balance
     * @var $sender User_Model_User
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $senderBalance = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity());
    if (!$senderBalance) {
      $senderBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $senderBalance->balance_id = $sender->getIdentity();
      $senderBalance->save();
    }

    $send_gift = $actionTypes->getActionType('send_gift');

    $row = $table->createRow();
    $row->user_id = $sender->getIdentity();
    $row->action_id = $send_gift->action_id;
    $row->credit = $credits;
    $row->object_type = '';
    $row->object_id = 0;
    $row->body = $count;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $senderBalance->setCredits($credits);
    $row->save();
  }

  public function buyProducts(User_Model_User $buyer, $order_ukey, $credits)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $buyerBalance Credit_Model_Balance
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());
    if (!$buyerBalance) {
      $buyerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $buyerBalance->balance_id = $buyer->getIdentity();
      $buyerBalance->save();
    }

    $buy_products = $actionTypes->getActionType('buy_products');

    $row = $table->createRow();
    $row->user_id = $buyer->getIdentity();
    $row->action_id = $buy_products->action_id;
    $row->credit = $credits;
    $row->object_type = '';
    $row->object_id = 0;
    $row->body = $order_ukey;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $buyerBalance->setCredits($credits);
    $row->save();
    return $row->log_id;
  }

  public function cancelOrder(User_Model_User $buyer, $credits)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $buyerBalance Credit_Model_Balance
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());
    if (!$buyerBalance) {
      $buyerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $buyerBalance->balance_id = $buyer->getIdentity();
      $buyerBalance->save();
    }

    $cancel_order = $actionTypes->getActionType('cancel_order');

    $row = $table->createRow();
    $row->user_id = $buyer->getIdentity();
    $row->action_id = $cancel_order->action_id;
    $row->credit = $credits;
    $row->object_type = '';
    $row->object_id = 0;
    $row->body = '';
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $buyerBalance->current_credit += $credits;
    $buyerBalance->spent_credit -= $credits;
    $buyerBalance->save();

    $row->save();
  }

  public function updatePageVisitCredits($page)
  {
    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $logsTable = Engine_Api::_()->getDbTable('logs', 'credit');

    $visitor = Engine_Api::_()->user()->getViewer();

    if (!$visitor->getIdentity()) {
      return 0;
    }

    $visitorBalance = Engine_Api::_()->getItem('credit_balance', $visitor->getIdentity());
    if (!$visitorBalance) {
      $visitorBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $visitorBalance->balance_id = $visitor->getIdentity();
      $visitorBalance->save();
    }

    if (($actionType = $actionTypes->getActionType('page_view')) === null) {
      return 0;
    }

    $newLogRow = $logsTable->createRow();
    $newLogRow->user_id = $visitor->getIdentity();
    $newLogRow->action_id = $actionType->action_id;
    $newLogRow->credit = $actionType->credit;
    $newLogRow->object_type = 'page';
    $newLogRow->object_id = $page->page_id;
    $newLogRow->creation_date = new Zend_Db_Expr('NOW()');

    $visitorBalance->setCredits($actionType->credit);
    $newLogRow->save();
  }

  public function buyLevel(User_Model_User $buyer, $credits, $body)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $buyerBalance Credit_Model_Balance
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());
    if (!$buyerBalance) {
      $buyerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $buyerBalance->balance_id = $buyer->getIdentity();
      $buyerBalance->save();
    }

    $buy_level = $actionTypes->getActionType('buy_level');

    $row = $table->createRow();
    $row->user_id = $buyer->getIdentity();
    $row->action_id = $buy_level->action_id;
    $row->credit = $credits;
    $row->object_type = '';
    $row->object_id = 0;
    $row->body = $body;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $buyerBalance->setCredits($credits);
    $row->save();
  }

  public function buyOffer(User_Model_User $buyer, $credits, $offer_id)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     * @var $buyerBalance Credit_Model_Balance
     */

    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');

    $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());
    if (!$buyerBalance) {
      $buyerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $buyerBalance->balance_id = $buyer->getIdentity();
      $buyerBalance->save();
    }

    $buy_offer = $actionTypes->getActionType('buy_offer');

    $row = $table->createRow();
    $row->user_id = $buyer->getIdentity();
    $row->action_id = $buy_offer->action_id;
    $row->credit = $credits;
    $row->object_type = 'offer';
    $row->object_id = $offer_id;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $buyerBalance->setCredits($credits);
    $row->save();
  }

  public function getUsers($values)
  {
    /**
     * @var $usersTbl User_Model_DbTable_Users
     **/
    $usersTbl = Engine_Api::_()->getDbTable('users', 'user');
    if ($values['users'] == 'all_users') {
      $select = $usersTbl->select();
    } elseif ($values['users'] == 'levels') {
      $select = $usersTbl->select();
      if (!empty($values['levels'])) {
        $select->where('level_id = ?', $values['levels']);
      }
    } elseif ($values['users'] == 'networks') {
      $networkTbl = Engine_Api::_()->getDbTable('networks', 'network');
      $membershipTbl = Engine_Api::_()->getDbTable('membership', 'network');
      $select = $usersTbl->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $usersTbl->info('name')))
        ->joinLeft(array('m' => $membershipTbl->info('name')), 'u.user_id = m.user_id', array())
        ->joinLeft(array('n' => $networkTbl->info('name')), 'm.resource_id = n.network_id', array())
        ->group('u.user_id');
      if (!empty($values['networks'])) {
        $select->where('network_id = ?', $values['networks']);
      }
    } elseif ($values['users'] == 'spec_users') {
      $raw_users = preg_split('/[,]+/', $values['user_ids']);
      $users = array();
      foreach ($raw_users as $user) {
        $user = trim(strip_tags($user));
        if ($user == "") {
          continue;
        }
        $users[] = $user;
      }

      $select = $usersTbl->select()
        ->where('user_id IN(' . join(',', $users) . ')');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($values['page']);
    $paginator->setItemCountPerPage($values['ipp']);

    return $paginator;
  }

  public function getPlugin($gateway_id)
  {
    if (null === $this->_plugin) {

      /**
       * @var $gatewayTb Payment_Model_Gateway
       */
      if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id))) {
        return null;
      }

      Engine_Loader::loadClass($gateway->plugin);
      if (!class_exists($gateway->plugin)) {
        return null;
      }

      $class = str_replace('Payment', 'Credit', $gateway->plugin);

      Engine_Loader::loadClass($class);
      if (!class_exists($class)) {
        return null;
      }

      $plugin = new $class($gateway);

      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
          'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }

    return $this->_plugin;
  }

  public function getGateway($gateway_id)
  {
    return $this->getPlugin($gateway_id)->getGateway();
  }

  public function getService($gateway_id)
  {
    return $this->getPlugin($gateway_id)->getService();
  }

  protected function getAllowedCredits($viewer)
  {
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $allowed = $permissionsTable->getAllowed('credit', $viewer->level_id, 'credits');
    if ($allowed === null) {
      return true;
    } elseif ($allowed == 0) {
      return false;
    } elseif ($allowed == 1) {
      return true;
    }
  }

  public function isModuleEnabled($needed_module)
  {
    if ($needed_module == null) {
      return true;
    } elseif ($needed_module == 'page') {
      $module = 'hecore';
    } else {
      $module = 'core';
    }
    return Engine_Api::_()->getDbTable('modules', $module)->isModuleEnabled($needed_module);
  }

  public function getPackageDescription($package)
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('credit.default.price', 100);
    $priceStr = '<span class="payment_credit_icon">';
    $priceStr .= '<span class="payment-credit-price">' . ceil($package->price * $defaultPrice) . '</span></span>';

    // Plan is free
    if ($package->price == 0) {
      $str = $translate->translate('Free');
    } // Plan is recurring
    else if ($package->recurrence > 0 && $package->recurrence_type != 'forever') {

      // Make full string
      if ($package->recurrence == 1) { // (Week|Month|Year)ly
        if ($package->recurrence_type == 'day') {
          $typeStr = $translate->translate('daily');
        } else {
          $typeStr = $translate->translate($package->recurrence_type . 'ly');
        }
        $str = sprintf($translate->translate('%1$s %2$s'), $priceStr, $typeStr);
      } else { // per x (Week|Month|Year)s
        $typeStr = $translate->translate(array($this->recurrence_type, $package->recurrence_type . 's', $package->recurrence));
        $str = sprintf($translate->translate('%1$s per %2$s %3$s'), $priceStr,
          $package->recurrence, $typeStr); // @todo currency
      }
    } // Plan is one-time
    else {
      $str = sprintf($translate->translate('One-time fee of %1$s'), $priceStr);
    }

    // Add duration, if not forever
    if ($package->duration > 0 && $package->duration_type != 'forever') {
      $typeStr = $translate->translate(array($package->duration_type, $package->duration_type . 's', $package->duration));
      $str = sprintf($translate->translate('%1$s for %2$s %3$s'), $str, $package->duration, $typeStr);
    }

    return $str;
  }

  public function updateDonationCredits(User_Model_User $user, $object_id)
  {
    /**
     * @var $logsTbl Credit_Model_DbTable_Logs
     * @var $actionTypesTbl Credit_Model_DbTable_ActionTypes
     * @var $balance Credit_Model_Balance
     **/

    if (!$user->getIdentity()) {
      return 0;
    }

    $view = Zend_Registry::get('Zend_View');
    $actionTypesTbl = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $logsTbl = Engine_Api::_()->getDbTable('logs', 'credit');

    $action = $actionTypesTbl->getActionType('donation_donating_new');

    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());

    if (!$balance) {
      $balance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $balance->balance_id = $user->getIdentity();
      $balance->save();
    }

    if (!$this->getAllowedCredits($user)) {
      return 0;
    }

    if (!$logsTbl->checkCredit($action, $user->getIdentity())) {
      return 0;
    }

    $object_type = 'donation';
    $credits = $action->credit;

    $row = $logsTbl->createRow();
    $row->user_id = $user->getIdentity();
    $row->action_id = $action->action_id;
    $row->credit = $credits;
    $row->object_type = $object_type;
    $row->object_id = $object_id;
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $balance->setCredits($credits);

    $row->save();
  }

  public function updateUserProfileCredits(User_Model_User $user)
  {
    /**
     * @var $logsTbl Credit_Model_DbTable_Logs
     * @var $actionTypesTbl Credit_Model_DbTable_ActionTypes
     * @var $balance Credit_Model_Balance
     **/

    if (!$user->getIdentity()) {
      return 0;
    }

    $view = Zend_Registry::get('Zend_View');
    $actionTypesTbl = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $logsTbl = Engine_Api::_()->getDbTable('logs', 'credit');

    $action = $actionTypesTbl->getActionType('user_profile_edit');

    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());

    if (!$balance) {
      $balance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $balance->balance_id = $user->getIdentity();
      $balance->save();
    }

    if (!$this->getAllowedCredits($user)) {
      return 0;
    }

    if (!$logsTbl->checkCredit($action, $user->getIdentity())) {
      return 0;
    }

    $object_type = 'user';
    $credits = $action->credit;

    $row = $logsTbl->createRow();
    $row->user_id = $user->getIdentity();
    $row->action_id = $action->action_id;
    $row->credit = $credits;
    $row->object_type = $object_type;
    $row->object_id = $user->getIdentity();
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $balance->setCredits($credits);

    $row->save();
  }

  public function updateWallServicesPostCredits(User_Model_User $user, $object)
  {
    /**
     * @var $logsTbl Credit_Model_DbTable_Logs
     * @var $actionTypesTbl Credit_Model_DbTable_ActionTypes
     * @var $balance Credit_Model_Balance
     **/

    if (!$user->getIdentity()) {
      return 0;
    }

    $view = Zend_Registry::get('Zend_View');
    $actionTypesTbl = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $logsTbl = Engine_Api::_()->getDbTable('logs', 'credit');

    $action = '';
    if ($object->provider == 'facebook') {
      $action = $actionTypesTbl->getActionType('share_post_facebook');
    } elseif ($object->provider == 'twitter') {
      $action = $actionTypesTbl->getActionType('share_post_twitter');
    } elseif ($object->provider == 'linkedin') {
      $action = $actionTypesTbl->getActionType('share_post_linkedin');
    } else {
      return 0;
    }

    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());

    if (!$balance) {
      $balance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
      $balance->balance_id = $user->getIdentity();
      $balance->save();
    }

    if (!$this->getAllowedCredits($user)) {
      return 0;
    }

    if (!$logsTbl->checkCredit($action, $user->getIdentity())) {
      return 0;
    }

    $object_type = 'user';
    $credits = $action->credit;

    $row = $logsTbl->createRow();
    $row->user_id = $user->getIdentity();
    $row->action_id = $action->action_id;
    $row->credit = $credits;
    $row->object_type = $object_type;
    $row->object_id = $user->getIdentity();
    $row->creation_date = new Zend_Db_Expr('NOW()');

    $balance->setCredits($credits);

    $row->save();
  }
}
