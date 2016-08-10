<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminGiveCreditsController.php 11.01.12 17:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminGiveCreditsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_giveCredits');
  }

  public function indexAction()
  {
    $this->view->form = $form = new Credit_Form_Admin_GiveCredits();
    $form->getDecorator('Description')->setOption('escape', false);
    $this->view->ok = false;
    if (!$this->getRequest()->isPost()) {
      return ;
    }

    if( !$form->isValid($values = $this->getRequest()->getPost()) ) {
      return ;
    }

    /**
     * @var $creditApi Credit_Api_Core
     **/

    $creditApi = Engine_Api::_()->credit();

    $credits = $values['credit'];

    if ($credits < 0) {
      $form->addError('Only unsigned number');
      return ;
    }

    if ($values['set_default'] != 1 && !$credits) {
      $form->addError('You cannot send 0 credits');
      return ;
    }

    $values['page'] = 1;
    $values['ipp'] = 500;
    $users = $creditApi->getUsers($values);
    $count = $users->getTotalItemCount();
    if (!$count) {
      $form->addError('There are no users to give credits');
      return ;
    }

    $this->view->ok = true;
    $this->view->values = $values;
    $this->view->count = $count;
    $this->view->total = $count;
  }

  public function sendAction()
  {
    /**
     * @var $creditApi Credit_Api_Core
     * @var $notificationTable Activity_Model_DbTable_Notifications
     * @var $convoTable Messages_Model_DbTable_Conversations
     * @var $user User_Model_User
     * @var $sender User_Model_User
     **/

    $convoTable = Engine_Api::_()->getDbTable('conversations', 'messages');
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');
    $creditApi = Engine_Api::_()->credit();
    $sender = Engine_Api::_()->user()->getViewer();

    $values = $this->_getAllParams();
    $users = $creditApi->getUsers($values);
    $limit = false;

    $pageCount = $users->getPages()->pageCount;

    // prepare template
    if (!empty($values['set_default']) && $values['set_default'] == 1) {
      // setting credits
      if ($pageCount >= $values['page']) {
        $part_of_users = $users->getCurrentItems();
        foreach ($part_of_users as $user) {
          $creditApi->setCredits($user, $values['credit']);
        }

        if ($values['send'] == 1) {
          foreach ($part_of_users as $user) {
            $notificationTable->addNotification($user, $sender, $user, 'set_credits', array(
              'amount' => $values['credit'],
              'action' => $this->view->url(array('action' => 'manage'), 'credit_general', true),
              'label' => $this->view->translate('here')
            ));
          }
        } elseif ($values['send'] == 2) {
          foreach ($part_of_users as $user) {
            if ($user->getIdentity() != $sender->getIdentity()) {
              $convoTable->send($sender, $user, $values['subject'], $values['message']);
            }
          }
        }
        $values['page'] ++;
        $values['count'] = $values['count'] - $part_of_users->count();
      } else {
        $limit = true;
      }
    } else {
      // sending credits
      if ($pageCount >= $values['page']) {
        $part_of_users = $users->getCurrentItems();
        foreach ($part_of_users as $user) {
          $creditApi->giveCredits($user, $values['credit']);
        }

        if ($values['send'] == 1) {
          foreach ($part_of_users as $user) {
            $notificationTable->addNotification($user, $sender, $user, 'send_credits', array(
              'amount' => $values['credit'],
              'action' => $this->view->url(array('action' => 'manage'), 'credit_general', true),
              'label' => $this->view->translate('here')
            ));
          }
        } elseif ($values['send'] == 2) {
          foreach ($part_of_users as $user) {
            if ($user->getIdentity() != $sender->getIdentity()) {
              $convoTable->send($sender, $user, $values['subject'], $values['message']);
            }
          }
        }
        $values['page'] ++;
        $values['count'] = $values['count'] - $part_of_users->count();
      } else {
        $limit = true;
      }
    }

    $this->view->html = $this->view->partial(
      '_send.tpl',
      'credit',
      array(
        'count' => $values['count'],
        'set_default' => $values['set_default'],
        'limit' => $limit,
        'total' => $values['total']
      )
    );
    $this->view->count = $values['count'];
    $this->view->page = $values['page'];
    $this->view->limit = $limit;
  }

  public function suggestAction()
  {
    $users = $this->getUsersByText($this->_getParam('text'));
    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' ) {
      foreach( $users as $user ) {
        $data[] = $user->displayname;
      }
    } else {
      foreach( $users as $user ) {
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
     **/
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
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
      $select->where('displayname LIKE ?', $text);
      $select->orWhere('displayname LIKE ?', $text.'%');
    }

    $select2 = clone $select;

    if ($this->check($select2)) {
      return $table->fetchAll($select);
    }

    if( $text ) {
      $select->where('displayname LIKE ?', $text);
      $select->orWhere('displayname LIKE ?', '%'.$text.'%');
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
}
