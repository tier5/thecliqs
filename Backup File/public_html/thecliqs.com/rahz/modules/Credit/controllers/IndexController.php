<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 06.01.12 13:32 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('credit', null, 'view_credit_home')->isValid() ) return;

    // Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled()
    ;
  }

  public function manageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
    }

    // Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled()
    ;
  }

  public function sendAction()
  {
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');
    $translate = Zend_Registry::get('Zend_Translate');

    $credits = (int)$this->_getParam('credit', 0);
    $user_id = $this->_getParam('user_id', 0);
    $form_type = $this->_getParam('format', false);
    if ($form_type == 'smoothbox') {
      $this->view->form = $form = new Credit_Form_Send($form_type, $user_id);
      if (!$this->getRequest()->isPost()) {
        return false;
      }
    }

    if (!$this->_getParam('format', false)) {
      $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'credit_general', true);
    }

    if (empty($user_id)) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, this User doesn\'t exist, please, choose from suggestion list!');
      return ;
    }

    if (empty($credits) || $credits <= 0) {
      $this->view->result  = false;
      if ($form_type == 'smoothbox') {
        $form->addError($this->view->translate('Sorry, you didn\'t enter credits'));
      } else {
        $this->view->message = $translate->_('Sorry, you didn\'t enter credits');
      }
      return ;
    }

    $sender = Engine_Api::_()->user()->getViewer();

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    if ($permissionsTable->getAllowed('credit', $sender->level_id, 'transfer') === 0) {
      $this->view->result  = false;
      if ($form_type == 'smoothbox') {
        $form->addError($this->view->translate('Sorry, Admin doesn\'t allow to transfer for this level.'));
      }
      else {
        $this->view->message = $translate->_('Sorry, Admin doesn\'t allow to transfer for this level.');
      }
      return ;
    }

    if (!$this->getAllowTransfer($sender->getIdentity())) {
      $this->view->result  = false;
      if ($form_type == 'smoothbox') {
        $form->addError($this->view->translate('Sorry, Admin doesn\'t allow to transfer for you, you have reached the daily limit.'));
      }
      else {
        $this->view->message = $translate->_('Sorry, Admin doesn\'t allow to transfer for you, you have reached the daily limit.');
      }
      return ;
    }

    $recipient = Engine_Api::_()->getItem('user', $user_id);

    /**
     * @var $creditApi Credit_Api_Core
     **/

    $creditApi = Engine_Api::_()->credit();
    $value = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity())->current_credit - $credits;
    if ($value < 0) {
      $this->view->result  = false;
      $this->view->message = $translate->_('CREDIT_not-enough-credit');
      return;
    }

    $creditApi->updateTransferCredits($sender, $recipient, $credits);

    $notificationTable->addNotification($recipient, $sender, $recipient, 'send_credits', array(
      'amount' => $credits,
      'action' => $this->view->url(array('action' => 'manage'), 'credit_general', true),
      'label' => $translate->_('here'),
    ));

    $this->view->result  = true;

    if ($form_type == 'smoothbox') {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format'=> 'smoothbox',
        'messages' => array($this->view->translate('%s credits successfully sent to your friend %s', $credits, $recipient->getTitle())),
      ));
    }
    else {
      $this->view->message = $this->view->translate('%s credits successfully sent to your friend %s', $credits, $recipient->getTitle());
      return ;
    }
  }

  public function buyAction()
  {
    /**
     * @var $table Credit_Model_DbTable_Payments
     */
    $this->_helper->layout->setLayout('default-simple');
    $table = Engine_Api::_()->getDbTable('payments', 'credit');

    $this->view->prices = $prices = $table->getPrices();
    $price = empty($prices[0]->credit) ? null : $prices[0];
    $this->view->credits_for_one_unit = ($price) ? (float)($price->credit/(float)$price->price) : 0; // credits for one unit
    $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $balance = Engine_Api::_()->getItem('credit_balance', $user_id);
    $this->view->current_balance = $balance->current_credit;
  }

  public function faqAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('credit', null, 'view_credit_faq')->isValid() ) return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_main', array(), 'credit_main_faq');

    $translate = Zend_Registry::get('Zend_Translate');

    $faqs = array();
    $iter = 1;
    while('CREDIT_QUESTION_'.$iter != $translate->_('CREDIT_QUESTION_'.$iter)) {
      if ('CREDIT_ANSWER_'.$iter !=$translate->_('CREDIT_ANSWER_'.$iter)) {
        $faqs[$iter]['q'] = 'CREDIT_QUESTION_'.$iter;
        $faqs[$iter]['a'] = 'CREDIT_ANSWER_'.$iter;
      }
      $iter ++;
    }
    $this->view->faqs = $faqs;

    /**
     * @var $table Credit_Model_DbTable_ActionTypes
     */

    $table = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $this->view->actionTypes = $table->getActionTypes(array('action_module' => 'ASC', 'credit' => 1));
  }

  public function suggestAction()
  {
    $users = $this->getUsersByText($this->_getParam('text'), $this->_getParam('limit', 40));
    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' ) {
      foreach( $users as $user ) {
        $data[] = $user->displayname;
      }
    } else {
      foreach( $users as $user ) {
        if (!$this->getAllowTransfer($user->user_id)) {
          continue;
        }
        $data[] = array(
          'id' => $user->user_id,
          'label' => $user->displayname,
          'photo' => $this->view->itemPhoto($user, 'thumb.icon')
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  private function getUsersByText($text = null, $limit = 10)
  {
    /**
     * @var $table User_Model_DbTable_Users
     * @var $user User_Model_User
     **/

    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $user->membership()->getMembersOfSelect();
    $friends = $table->fetchAll($select);

    $ids = array(0);
    foreach( $friends as $friend ) {
      $ids[] = $friend->resource_id;
    }

    $select = $table->select()
      ->where("user_id IN(".join(',', $ids).")")
      ->group('user_id')
      ->limit($limit);

    if( $text ) {
      $select->where('displayname LIKE ?', $text);
    }

    $select1 = clone $select;

    if ($this->check($select1)) {
      return $table->fetchAll($select);
    }

    if( $text ) {
      $select->reset('where');
      $select
        ->where(sprintf("displayname LIKE %s OR displayname LIKE %s", "'".$text."'", "'".$text."%'"))
        ->where("user_id IN(".join(',', $ids).")")
      ;
    }

    $select2 = clone $select;

    if ($this->check($select2)) {
      return $table->fetchAll($select);
    }

    if( $text ) {
      $select->reset('where');
      $select
        ->where(sprintf("displayname LIKE %s OR displayname LIKE %s", "'".$text."'", "'%" . $text . "%'"))
        ->where("user_id IN(".join(',', $ids).")")
      ;
    }

    $select3 = clone $select;

    if ($this->check($select3)) {
      return $table->fetchAll($select);
    }

    return $table->fetchAll($select);
  }


  private function check($select)
  {
    /**
     * @var $table User_Model_DbTable_Users
     **/
    $table = Engine_Api::_()->getDbTable('users', 'user');
    if ($table->fetchRow($select)) {
      return true;
    } else {
      return false;
    }
  }

  private function getAllowTransfer($user_id)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     */
    $table = Engine_Api::_()->getDbTable('logs', 'credit');
    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($user_id == $viewer->getIdentity()) {
      $max_send = $permissionsTable->getAllowed('credit', $viewer->level_id, 'max_send');
      if ($max_send === 0) {
        return true;
      } elseif ($max_send === null) {
        $max_send = 1500;
      }

      $select = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('c' => $table->info('name')))
        ->joinLeft(array('a' => $actionTypes->info('name')), 'c.action_id = a.action_id')
        ->where('c.user_id = ?', $user_id)
        ->where('a.action_type = ?', 'transfer_to')
        ->where('c.creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL 1 DAY)"))
      ;

      $credits = $table->fetchAll($select);

      $all_credits = 0;
      foreach($credits as $credit) {
        $all_credits += (-1)*$credit->credit;
      }

      return ($all_credits < $max_send) ? true : false;
    } else {
      $recipient = Engine_Api::_()->getItem('user', $user_id);
      $max_receive = $permissionsTable->getAllowed('credit', $recipient->level_id, 'max_received');

      if ($max_receive === 0) {
        return true;
      } elseif ($max_receive === null) {
        $max_receive = 1500;
      }

      $select = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('c' => $table->info('name')))
        ->joinLeft(array('a' => $actionTypes->info('name')), 'c.action_id = a.action_id')
        ->where('c.user_id = ?', $user_id)
        ->where('a.action_type = ?', 'transfer_from')
        ->where('c.creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL 1 DAY)"))
      ;

      $credits = $table->fetchAll($select);

      $all_credits = 0;
      foreach($credits as $credit) {
        $all_credits += $credit->credit;
      }

      return ($all_credits < $max_receive) ? true : false;
    }
  }
}
