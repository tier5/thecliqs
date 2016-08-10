<?php

class Headvmessages_IndexController extends Core_Controller_Action_Standard
{

  var $_maxRecipients = 10;

  public function indexAction()
  {
    $this->view->html = $this->getConversations();
    $this->view->status = true;
  }

  private function getConversations($active = 0)
  {
    /**
     * @var $api Headvmessages_Api_Core
     * @var $paginator Zend_Paginator
     */

    $this->view->activeConversation = $active;
    $api = Engine_Api::_()->headvmessages();
    $this->view->paginator = $paginator = $api->getConversations();
    $this->view->cCount = $paginator->getTotalItemCount();
    return $this->view->render('_conversations_list.tpl');
  }

  private function getMessages($id)
  {
    /**
     * @var $conversation Messages_Model_Conversation
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$id) {
      return false;
    }

    $this->view->conversation = $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

    if (!$conversation) {
      return false;
    }
    $this->view->recipients = $conversation->getRecipients();
    $this->view->messages = $conversation->getMessages($viewer);
    $this->view->count = count($this->view->messages);
    $conversation->setAsRead($viewer);
    return $this->view->render('_messages_list.tpl');
  }

  public function conversationAction()
  {
    $this->_helper->layout->disableLayout();
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (empty($data['composer'])) {
        continue;
      }

      foreach ($data['composer'] as $type => $config) {
        // is the current user has "create" privileges for the current plugin
        if (isset($config['auth'], $config['auth'][0], $config['auth'][1])) {
          $isAllowed = Engine_Api::_()
            ->authorization()
            ->isAllowed($config['auth'][0], null, $config['auth'][1]);

          if (!empty($config['auth']) && !$isAllowed) {
            continue;
          }
        }
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;

    $id = $this->getParam('conversation_id');

    $this->view->allowSmiles = Engine_Api::_()->headvmessages()->allowSmiles();
    $this->view->html = $this->getMessages($id);
    $this->view->status = (boolean)$this->view->html;
  }

  public function composeAction()
  {
    $this->_helper->layout->disableLayout();
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (empty($data['composer'])) {
        continue;
      }

      foreach ($data['composer'] as $type => $config) {
        // is the current user has "create" privileges for the current plugin
        if (isset($config['auth'], $config['auth'][0], $config['auth'][1])) {
          $isAllowed = Engine_Api::_()
            ->authorization()
            ->isAllowed($config['auth'][0], null, $config['auth'][1]);

          if (!empty($config['auth']) && !$isAllowed) {
            continue;
          }
        }
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;
    $this->view->status = true;


    $this->view->allowSmiles = Engine_Api::_()->headvmessages()->allowSmiles();

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->code = 1;
      return;
    }

    $form = new Messages_Form_Compose();
    if (!$form->isValid($this->getRequest()->getParams())) {
      $this->view->status = false;
      $this->view->code = 2;
      return;
    }
    $this->view->composePartials = null;
////////////////////////////////////////////////////////////////
    $this->postMessage($form->getValues());
////////////////////////////////////////////////////////////////
  }

  private function postMessage($values)
  {
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = $attachmentData['type'];
        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {
          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if ($config) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
          $parent = $attachment->getParent();
          if ($parent->getType() === 'user') {
            $attachment->search = 0;
            $attachment->save();
          } else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }

      $viewer = Engine_Api::_()->user()->getViewer();

      // Prepopulated
      /*if ($toObject instanceof User_Model_User) {
        $recipientsUsers = array($toObject);
        $recipients = $toObject;
        // Validate friends
        if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
          if (!$viewer->membership()->isMember($recipients)) {
            return $form->addError('One of the members specified is not in your friends list.');
          }
        }

      } else if ($toObject instanceof Core_Model_Item_Abstract &&
        method_exists($toObject, 'membership')
      ) {
        $recipientsUsers = $toObject->membership()->getMembers();
//        $recipients = array();
//        foreach( $recipientsUsers as $recipientsUser ) {
//          $recipients[] = $recipientsUser->getIdentity();
//        }
        $recipients = $toObject;
      } // Normal
      else {
        $recipients = preg_split('/[,. ]+/', $values['toValues']);
        // clean the recipients for repeating ids
        // this can happen if recipient is selected and then a friend list is selected
        $recipients = array_unique($recipients);
        // Slice down to 10
        $recipients = array_slice($recipients, 0, $maxRecipients);
        // Get user objects
        $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
        // Validate friends
        if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
          foreach ($recipientsUsers as &$recipientUser) {
            if (!$viewer->membership()->isMember($recipientUser)) {
              return $form->addError('One of the members specified is not in your friends list.');
            }
          }
        }
      }*/

      $recipients = preg_split('/[,. ]+/', $values['toValues']);
      // clean the recipients for repeating ids
      // this can happen if recipient is selected and then a friend list is selected
      $recipients = array_unique($recipients);
      // Slice down to 10
      $recipients = array_slice($recipients, 0, $this->_maxRecipients);
      // Get user objects
      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
      $users = array();
      // Validate friends
      if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
        foreach ($recipientsUsers as &$recipientUser) {
          if (!$viewer->membership()->isMember($recipientUser)) {
            $this->view->status = false;
            $this->view->code = 4;
            $this->view->message = $this->view->translate('One of the members specified is not in your friends list.');
            return;
          }
        }
      }

      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipients,
        $values['title'],
        $values['body'],
        $attachment
      );

      // Send notifications
      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $viewer,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $this->view->status = false;
      $this->view->code = 3;
      $this->view->message = $e->getMessage();
      return;
    }

    $this->view->id = $conversation->getIdentity();
    $this->view->status = true;
    $this->view->message = $this->view->translate('Your message has been sent successfully.');

    $this->view->conversationsHtml = $this->getConversations($conversation->getIdentity());
    $this->view->messagesHtml = $this->getMessages($conversation->getIdentity());
  }

  public function replyAction()
  {
    $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);
    if (!$conversation) {
      $this->view->status = false;
      $this->view->code = 0;
      return;
    }
    $form = new Messages_Form_Reply();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
      $db->beginTransaction();
      try {
        // Try attachment getting stuff
        $attachment = null;
        $attachmentData = $this->getRequest()->getParam('attachment');
        if (!empty($attachmentData) && !empty($attachmentData['type'])) {
          $type = $attachmentData['type'];
          $config = null;
          foreach (Zend_Registry::get('Engine_Manifest') as $data) {
            if (!empty($data['composer'][$type])) {
              $config = $data['composer'][$type];
            }
          }
          if ($config) {
            $plugin = Engine_Api::_()->loadClass($config['plugin']);
            $method = 'onAttach' . ucfirst($type);
            $attachment = $plugin->$method($attachmentData);

            $parent = $attachment->getParent();
            if ($parent->getType() === 'user') {
              $attachment->search = 0;
              $attachment->save();
            } else {
              $parent->search = 0;
              $parent->save();
            }

          }
        }

        $values = $form->getValues();
        $values['conversation'] = (int)$id;

        $conversation->reply(
          $viewer,
          $values['body'],
          $attachment
        );
        /*
        Engine_Api::_()->messages()->replyMessage(
          $viewer,
          $values['conversation'],
          $values['body'],
          $attachment
        );
         *
         */
        $recipients = $conversation->getRecipients();
        // Send notifications
        foreach ($recipients as $user) {
          if ($user->getIdentity() == $viewer->getIdentity()) {
            continue;
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
            $user,
            $viewer,
            $conversation,
            'message_new'
          );
        }

        // Increment messages counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

        $db->commit();
      } catch (Exception $e) {
        $this->view->status = false;
        $this->view->message = $e->getMessage();
        return;
      }

      $this->view->status = true;
    }
  }

  public function deleteAction()
  {
    /**
     * @var $conversation Messages_Model_Conversation
     */

    $id = $this->getParam('id');
    if (!$id) {
      $this->view->status = false;
      $this->view->code = 0;
      return;
    }

    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

    if (!$conversation) {
      $this->view->status = false;
      $this->view->code = 1;
      return;
    }

    try {
      Engine_Api::_()->headvmessages()->removeConversation($id);
    } catch (Exception $e) {
      $this->view->status = false;
      $this->view->code = 2;
      return;
    }


    $this->view->status = true;
    $api = Engine_Api::_()->headvmessages();
    $paginator = $api->getConversations();
    $this->view->cCount = $paginator->getTotalItemCount();
  }

}
