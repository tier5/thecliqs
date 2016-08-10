<?php

/**
 * class Ynrestapi_Api_Notification
 */
class Ynrestapi_Api_Notification extends Ynrestapi_Api_Base
{
    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'activity';
        $this->mainItemType = 'activity_notification';
    }

    /**
     * Mark all notifications as read
     *
     * @param  $params
     * @return mixed
     */
    public function postMarkAllRead($params)
    {
        self::requireScope('activities');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer);

        self::setSuccess(200);
        return true;
    }

    /**
     * Mark notification as read
     *
     * @param  $params
     * @return mixed
     */
    public function postMarkRead($params)
    {
        self::requireScope('activities');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $action_id = $params['id'];
        $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
        if (!$notification) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $notificationsTable->getAdapter();
        $db->beginTransaction();

        try {
            $notification->read = 1;
            $notification->save();
            $db->commit();
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * Get notifications of the logged in user
     *
     * @param  $params
     * @return mixed
     */
    public function get($params)
    {
        self::requireScope('activities');

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $type = isset($params['type']) ? $params['type'] : '';
        $fields = $this->_getFields($params, 'listing');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get viewer and other user
        $viewer = Engine_Api::_()->user()->getViewer();

        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');

        $updateConds = array(
            'mitigated = ?' => 0,
            'user_id = ?' => $viewer->getIdentity(),
        );

        // if type set
        if ($type == 'update') {
            $updateConds['type != ?'] = 'friend_request';
        } elseif ($type == 'friend_request') {
            $updateConds['type = ?'] = 'friend_request';
        }

        $notificationsTable->update(array(
            'mitigated' => 1,
        ), $updateConds);

        // Get notifications
        $select = $notificationsTable
            ->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('object_type IN (?)', Engine_Api::_()->getItemTypes())
            ->order('notification_id DESC');

        // if type set
        if ($type == 'update') {
            $select->where('type != ?', 'friend_request');
        } elseif ($type == 'friend_request') {
            $select->where('type = ?', 'friend_request');
        }

        $data = Ynrestapi_Helper_Meta::exportByPage($select, $page, $limit, $fields);

        self::setSuccess(200, $data);
        return true;
    }
}
