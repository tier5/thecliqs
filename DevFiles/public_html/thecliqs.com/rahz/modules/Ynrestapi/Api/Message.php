<?php

/**
 * class Ynrestapi_Api_Message
 */
class Ynrestapi_Api_Message extends Ynrestapi_Api_Base
{
    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'messages';
        $this->mainItemType = 'messages';
    }

    /**
     * Delete Conversations
     *
     * @param  $params
     * @return mixed
     */
    public function delete($params)
    {
        self::requireScope('messages');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['ids'])) {
            self::setParamError('ids', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $message_ids = $params['ids'];
        $messages = explode(',', $message_ids);

        foreach ($messages as $message_id) {
            if (!is_numeric($message_id)) {
                self::setParamError('ids');
                return false;
            }
        }

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($messages as $message_id) {
                $message = Engine_Api::_()->getItem('messages_conversation', $message_id);
                if (!$message || !$message->getIdentity()) {
                    self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('One of the selected messages is not found.'));
                    return false;
                }

                $recipients = $message->getRecipientsInfo();
                //$recipients = Engine_Api::_()->getApi('core', 'messages')->getConversationRecipientsInfo($message_id);
                foreach ($recipients as $r) {
                    if ($viewer_id == $r->user_id) {
                        $r->inbox_deleted = true;
                        $r->outbox_deleted = true;
                        $r->save();
                    }
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('The selected messages have been deleted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Send Reply
     *
     * @param  $params
     * @return mixed
     */
    public function postReply($params)
    {
        self::requireScope('messages');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Validate params
        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['body'])) {
            self::setParamError('body', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $id = $params['id'];
        $viewer = Engine_Api::_()->user()->getViewer();

        // Get conversation info
        $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

        // Make sure the user is part of the conversation
        if (!$conversation || !$conversation->hasRecipient($viewer)) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        // Check for resource
        if (!empty($conversation->resource_type) &&
            !empty($conversation->resource_id)) {
            $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
            if (!($resource instanceof Core_Model_Item_Abstract)) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
                return false;
            }
        }
        // Otherwise get recipients
        else {
            $recipients = $conversation->getRecipients();
        }

        if (!$conversation->locked) {

            // Process form
            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();
            try
            {
                // Try attachment getting stuff
                $attachment = null;
                $attachmentData = $params['attachment'];
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

                $conversation->reply(
                    $viewer,
                    $params['body'],
                    $attachment
                );

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
                $db->rollBack();
                throw $e;
            }

            self::setSuccess(200);
            return true;
        } else {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }
    }

    /**
     * Compose new message
     *
     * @param  $params
     * @return mixed
     */
    public function post($params)
    {
        self::requireScope('messages');

        // Get params
        $toValues = isset($params['to']) ? $params['to'] : '';
        $title = isset($params['title']) ? $params['title'] : '';
        $body = isset($params['body']) ? $params['body'] : '';

        // Validate params
        if (empty($toValues)) {
            self::setParamError('to', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($title)) {
            self::setParamError('title', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($body)) {
            self::setParamError('body', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        // Not logged in
        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $multi = $params['multi'];
        $viewer = Engine_Api::_()->user()->getViewer();
        $toObject = null;

        // Get setting?
        $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
        if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
            self::setError(401, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
        if ($messageAuth == 'none') {
            self::setError(401, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Build
        $isPopulated = false;
        if (!empty($to) && (empty($multi) || $multi == 'user')) {
            $multi = null;
            // Prepopulate user
            $toUser = Engine_Api::_()->getItem('user', $to);
            $isMsgable = ('friends' != Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ||
                $viewer->membership()->isMember($toUser));
            if ($toUser instanceof User_Model_User &&
                (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
                isset($toUser->user_id) &&
                $isMsgable) {
                $toObject = $toUser;
                $isPopulated = true;
            } else {
                $multi = null;
                $to = null;
            }
        } else if (!empty($to) && !empty($multi)) {
            // Prepopulate group/event/etc
            $item = Engine_Api::_()->getItem($multi, $to);
            // Potential point of failure if primary key column is something other
            // than $multi . '_id'
            $item_id = $multi . '_id';
            if ($item instanceof Core_Model_Item_Abstract &&
                isset($item->$item_id) && (
                    $item->isOwner($viewer) ||
                    $item->authorization()->isAllowed($viewer, 'edit')
                )) {
                $toObject = $item;
                $isPopulated = true;
            } else {
                $multi = null;
                $to = null;
            }
        }

        // Assign the composing stuff
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

        // Get config
        $maxRecipients = 10;

        // Process
        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = $params['attachment'];
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
                    if ($parent = $attachment->getParent()) {
                        if ($parent->getType() === 'user') {
                            $attachment->search = 0;
                            $attachment->save();
                        } else {
                            $parent->search = 0;
                            $parent->save();
                        }
                    }
                }
            }

            $viewer = Engine_Api::_()->user()->getViewer();

            // Prepopulated
            if ($toObject instanceof User_Model_User) {
                $recipientsUsers = array($toObject);
                $recipients = $toObject;
                // Validate friends
                if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
                    // Get data
                    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
                    if (!$direction) {
                        //one way
                        $friendship_status = $viewer->membership()->getRow($recipients);
                    } else {
                        $friendship_status = $recipients->membership()->getRow($viewer);
                    }

                    if (!$friendship_status || $friendship_status->active == 0) {
                        self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('One of the members specified is not in your friends list.'));
                        return false;
                    }
                }

            } else if ($toObject instanceof Core_Model_Item_Abstract &&
                method_exists($toObject, 'membership')) {
                $recipientsUsers = $toObject->membership()->getMembers();
                $recipients = $toObject;
            }
            // Normal
            else {
                $recipients = preg_split('/[,. ]+/', $toValues);
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
                        // Get data
                        $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
                        if (!$direction) {
                            //one way
                            $friendship_status = $viewer->membership()->getRow($recipientUser);
                        } else {
                            $friendship_status = $recipientUser->membership()->getRow($viewer);
                        }

                        if (!$friendship_status || $friendship_status->active == 0) {
                            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('One of the members specified is not in your friends list.'));
                            return false;
                        }
                    }
                }
            }

            // Create conversation
            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer,
                $recipients,
                $title,
                $body,
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
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'),
            'id' => $conversation->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get info and all messages in a conversation
     *
     * @param  $params
     * @return mixed
     */
    public function get($params)
    {
        self::requireScope('messages');

        $id = isset($params['id']) ? (int) $params['id'] : null;
        $fields = $this->_getFields($params, 'detail');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Get conversation info
        $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

        // Make sure the user is part of the conversation
        if (!$conversation || !$conversation->hasRecipient($viewer)) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        // Check for resource
        if (!empty($conversation->resource_type) &&
            !empty($conversation->resource_id)) {
            $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
            if (!($resource instanceof Core_Model_Item_Abstract)) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
                return false;
            }
        }

        $conversation->setAsRead($viewer);

        $data = Ynrestapi_Helper_Meta::exportOne($conversation, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Search messages or conversations
     *
     * @param  $params
     * @return mixed
     */
    public function getSearch($params)
    {
        self::requireScope('messages');

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $queryStr = isset($params['keywords']) ? $params['keywords'] : '';
        $fields = $this->_getFields($params, 'search');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $table = Engine_Api::_()->getDbtable('messages', 'messages');
        $query = $table->select()
            ->from('engine4_messages_messages')
            ->joinRight('engine4_messages_recipients', 'engine4_messages_recipients.conversation_id = engine4_messages_messages.conversation_id', null)
            ->where('engine4_messages_recipients.user_id = ?', $viewer->user_id)
            ->where('(engine4_messages_messages.title LIKE ? || engine4_messages_messages.body LIKE ?)', '%' . $queryStr . '%')
            ->order('engine4_messages_messages.message_id DESC')
        ;

        $paginatorAdapter = new Zend_Paginator_Adapter_DbTableSelect($query);
        $paginator = new Zend_Paginator($paginatorAdapter);

        $data = Ynrestapi_Helper_Meta::exportByPage($paginator, $page, $limit, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get outbox conversations of the logged in user
     *
     * @param  $params
     * @return mixed
     */
    public function getOutbox($params)
    {
        self::requireScope('messages');

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $fields = $this->_getFields($params, 'listing');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $paginator = Engine_Api::_()->getItemTable('messages_conversation')
            ->getOutboxPaginator($viewer);

        $data = Ynrestapi_Helper_Meta::exportByPage($paginator, $page, $limit, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get inbox conversations of the logged in user
     *
     * @param  $params
     * @return mixed
     */
    public function getInbox($params)
    {
        self::requireScope('messages');

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $fields = $this->_getFields($params, 'listing');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $paginator = Engine_Api::_()->getItemTable('messages_conversation')
            ->getInboxPaginator($viewer);

        $data = Ynrestapi_Helper_Meta::exportByPage($paginator, $page, $limit, $fields);

        self::setSuccess(200, $data);
        return true;
    }
}
