<?php

class Ynrestapi_Helper_Message_Conversation extends Ynrestapi_Helper_Base
{
    /**
     * @var mixed
     */
    private $_cachedMessage = null;

    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('message', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->conversation_id;
    }

    public function field_date()
    {
        $message = $this->_getMessage();

        $this->data['date'] = $message->date;
    }

    public function field_title()
    {
        $message = $this->_getMessage();

        ((isset($message) && '' != ($title = trim($message->getTitle()))) ||
            (isset($this->entry) && '' != ($title = trim($this->entry->getTitle()))) ||
            $title = '<em>' . $this->view->translate('(No Subject)') . '</em>');

        $this->data['title'] = $title;
    }

    public function field_body()
    {
        $message = $this->_getMessage();

        $this->data['body'] = html_entity_decode($message->body);
    }

    public function field_read()
    {
        $recipient = $this->entry->getRecipientInfo($this->viewer());

        $this->data['read'] = $recipient->inbox_read ? true : false;
    }

    public function field_participant()
    {
        $conversation = $this->entry;
        $resource = '';
        $sender = '';
        if ($conversation->hasResource() &&
            ($resource = $conversation->getResource())) {
            $sender = $resource;
        } else if ($conversation->recipients > 1) {
            $sender = $this->viewer();
        } else {
            foreach ($conversation->getRecipients() as $tmpUser) {
                if ($tmpUser->getIdentity() != $this->viewer()->getIdentity()) {
                    $sender = $tmpUser;
                }
            }
        }

        if ((!isset($sender) || !$sender)) {
            if ($this->viewer()->getIdentity() !== $conversation->user_id) {
                $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
            } else {
                $sender = $this->viewer();
            }
        }

        if (!isset($sender) || !$sender) {
            //continue;
            $sender = new User_Model_User(array());
        }

        $from = array(
            'img' => $this->itemPhoto($sender, 'thumb.icon'),
        );

        if (!empty($resource)) {
            $from['title'] = $resource->getTitle();
            $from['type'] = $resource->getType();
        } elseif ($conversation->recipients == 1) {
            $from['title'] = $sender->getTitle();
            $from['type'] = $sender->getType();
        } else {
            $from['title'] = $this->view->translate(array('%s person', '%s people', $conversation->recipients),
                $this->view->locale()->toNumber($conversation->recipients));
            $from['type'] = 'users';
        }

        $this->data['participant'] = $from;
    }

    /**
     * @return mixed
     */
    public function field_recipients()
    {
        $rs = array();
        $conversation = $this->entry;

        // Check for resource
        if (!empty($conversation->resource_type) &&
            !empty($conversation->resource_id)) {
            $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
            if (!($resource instanceof Core_Model_Item_Abstract)) {
                return $this->data['recipients'] = $rs;
            }
        }
        // Otherwise get recipients
        else {
            $recipients = $conversation->getRecipients();
        }

        // Resource
        if ($resource) {
            $rs[] = array(
                'id' => $resource->getIdentity(),
                'title' => $resource->getTitle(),
                'type' => $resource->getType(),
            );
        }
        // Recipients
        else {
            foreach ($recipients as $r) {
                $rs[] = array(
                    'id' => $r->getIdentity(),
                    'title' => $r->getTitle(),
                    'type' => $r->getType(),
                );
            }

            if (count($rs) == 1) {
                // only you
                $rs[] = array(
                    'id' => null,
                    'title' => Zend_Registry::get('Zend_Translate')->_('Deleted member'),
                    'type' => 'user',
                );
            }
        }

        $this->data['recipients'] = $rs;
    }

    /**
     * @return mixed
     */
    public function field_can_reply()
    {
        $canReply = false;
        $conversation = $this->entry;

        // Check for resource
        if (!empty($conversation->resource_type) &&
            !empty($conversation->resource_id)) {
            $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
            if (!($resource instanceof Core_Model_Item_Abstract)) {
                return $this->data['can_reply'] = $canReply;
            }
        }
        // Otherwise get recipients
        else {
            $recipients = $conversation->getRecipients();

            $blocked = false;
            $blocker = '';

            // This is to check if the viewered blocked a member
            $viewer_blocked = false;
            $viewer_blocker = '';

            foreach ($recipients as $recipient) {
                if ($this->viewer()->isBlockedBy($recipient)) {
                    $blocked = true;
                    $blocker = $recipient;
                } elseif ($recipient->isBlockedBy($this->viewer())) {
                    $viewer_blocked = true;
                    $viewer_blocker = $recipient;
                }
            }
        }

        // Can we reply?
        if (!$conversation->locked) {
            if ((!$blocked && !$viewer_blocked) || (count($recipients) > 1)) {
                $canReply = true;
            } elseif ($viewer_blocked) {
                $canReply = false;
            } else {
                $canReply = false;
            }
        }

        $this->data['can_reply'] = $canReply;
    }

    public function field_messages()
    {
        $messages = array();

        foreach ($this->entry->getMessages($this->viewer()) as $message) {
            $messages[] = Ynrestapi_Helper_Meta::exportOne($message, array('listing'));
        }

        $this->data['messages'] = $messages;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_body();
        $this->field_participant();
        $this->field_date();
        $this->field_read();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_title();
        $this->field_body();
        $this->field_recipients();
        $this->field_date();
        $this->field_read();
        $this->field_messages();
        $this->field_can_reply();
    }

    /**
     * @return mixed
     */
    private function _getMessage()
    {
        if (empty($this->_cachedMessage)) {
            if ($this->_isInbox()) {
                $message = $this->entry->getInboxMessage($this->viewer());
            } else {
                $message = $this->entry->getOutboxMessage($this->viewer());
            }
            $this->_cachedMessage = $message;
        }

        return $this->_cachedMessage;
    }

    /**
     * @return mixed
     */
    private function _isInbox()
    {
        $recipient = $this->entry->getRecipientInfo($this->viewer());

        return $recipient->inbox_updated ? true : false;
    }
}
