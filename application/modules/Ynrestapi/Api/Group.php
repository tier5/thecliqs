<?php

/**
 * class Ynrestapi_Api_Group
 */
class Ynrestapi_Api_Group extends Ynrestapi_Api_Base
{
    /**
     * @var array
     */
    protected $availablePrivacies = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'member' => 'All Group Members',
        'officer' => 'Officers and Owner Only',
        //'owner' => 'Owner Only',
    );

    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'group';
        $this->mainItemType = 'group';
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getEvents($params)
    {
        self::requireScope('groups');

        // Don't render if event item not available
        if (!Engine_Api::_()->hasItemType('event')) {
            self::setError(503, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Service Unavailable'));
            return false;
        }

        // Don't render this if not authorized
        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get subject and check auth
        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['group_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('group_id');
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }
        Engine_Api::_()->core()->setSubject($group);

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$group->authorization()->isAllowed($viewer, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get paginator
        $paginator = $group->getEventsPaginator();

        // Set item count per page and current page number
        $limit = isset($params['limit']) ? (int) $params['limit'] : $this->_getParam('itemCountPerPage', 5);
        $paginator->setItemCountPerPage($limit);

        $page = isset($params['page']) ? (int) $params['page'] : $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return null
     */
    public function postPhotosUpload($params)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);

        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($group);

        if (!$this->requireAuthIsValid($group, null, 'photo')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $album = $group->getSingletonAlbum();

            $values = array(
                'group_id' => $group->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            $photoTable = Engine_Api::_()->getItemTable('group_photo');
            $photo = $photoTable->createRow();
            $photo->setFromArray($values);
            $photo->save();

            $photo->setPhoto($_FILES['Filedata']);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'id' => $photo->photo_id,
            'name' => $_FILES['Filedata']['name'],
        );

        self::setSuccess(200, $data);
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function deletePhotos($params)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('group_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        $group = $photo->getParent('group');
        if (!$this->requireAuthIsValid($group, null, 'photo.edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
        $db->beginTransaction();

        try
        {
            $photo->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Photo deleted'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postPhotosItem($params)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('group_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        $group = $photo->getParent('group');
        if (!$this->requireAuthIsValid($group, null, 'photo.edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $form = new Group_Form_Photo_Edit();

        $values = $photo->toArray();

        if (isset($params['title'])) {
            $values['title'] = $params['title'];
        }

        if (isset($params['description'])) {
            $values['description'] = $params['description'];
        }

        $form->populate($values);

        if (!$form->isValid($values)) {
            $messages = $form->getMessages();
            self::setFormErrors($messages);
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
        $db->beginTransaction();

        try
        {
            $photo->setFromArray($form->getValues())->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Changes saved'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postPhotos($params)
    {
        if (isset($params['id'])) {
            // Edit photo
            return $this->postPhotosItem($params);
        }

        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['ids'])) {
            self::setParamError('ids', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);

        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($group);

        if (!$this->requireAuthIsValid($group, null, 'photo')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // validate photo ids
        $values = array(
            'file' => array_unique(explode(',', $params['ids'])),
        );

        foreach ($values['file'] as $photo_id) {
            $photo = Engine_Api::_()->getItem('group_photo', $photo_id);
            // Not a photo
            if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
                self::setParamError('ids');
                return false;
            }

            // Not owner
            if ($photo->getOwner()->getIdentity() != Engine_Api::_()->user()->getViewer()->getIdentity()) {
                self::setParamError('ids');
                return false;
            }

            // Photo is belong to another album
            if ($photo->group_id != $group->getIdentity()) {
                self::setParamError('ids');
                return false;
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $album = $group->getSingletonAlbum();

        // Process
        $table = Engine_Api::_()->getItemTable('group_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            // Add action and attachments
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $group, 'group_photo_upload', null, array(
                'count' => count($values['file']),
            ));

            // Do other stuff
            $count = 0;
            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem('group_photo', $photo_id);
                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
                    continue;
                }

                $photo->collection_id = $album->album_id;
                $photo->album_id = $album->album_id;
                $photo->group_id = $group->group_id;
                $photo->save();

                if ($action instanceof Activity_Model_Action && $count < 8) {
                    $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                }
                $count++;
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        self::setSuccess(200);
    }

    /**
     * @param  $params
     * @return null
     */
    protected function getPhotosItem($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $photo = Engine_Api::_()->getItem('group_photo', $params['id']);
        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }
        Engine_Api::_()->core()->setSubject($photo);

        $viewer = Engine_Api::_()->user()->getViewer();
        $album = $photo->getCollection();
        $group = $photo->getGroup();

        if (!$this->requireAuthIsValid($group, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity()) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        $fields = $this->_getFields($params, 'detail');
        $data = Ynrestapi_Helper_Meta::exportOne($photo, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getPhotos($params)
    {
        if (isset($params['id'])) {
            return $this->getPhotosItem($params);
        }

        self::requireScope('groups');

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['group_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('group_id');
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }
        Engine_Api::_()->core()->setSubject($group);

        if (!$this->requireAuthIsValid($group, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get paginator
        $album = $group->getSingletonAlbum();
        $paginator = $album->getCollectiblesPaginator();

        // Set item count per page and current page number
        $limit = isset($params['limit']) ? (int) $params['limit'] : 8;
        $paginator->setItemCountPerPage($limit);

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function postInvite($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        if (empty($params['user_ids'])) {
            self::setParamError('user_ids', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } else {
            $usersIds = explode(',', $params['user_ids']);
            foreach ($usersIds as $userId) {
                if (false === filter_var($userId, FILTER_VALIDATE_INT)) {
                    self::setParamError('user_ids');
                    return false;
                }
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $group = Engine_Api::_()->core()->getSubject();
        $friends = $viewer->membership()->getMembers();

        $multiOptions = array();
        foreach ($friends as $friend) {
            if ($group->membership()->isMember($friend, null)) {
                continue;
            }

            $multiOptions[$friend->getIdentity()] = $friend->getTitle();
        }

        foreach ($usersIds as $userId) {
            if (!array_key_exists($userId, $multiOptions)) {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('One of the members specified is not in your friends list or is already a member of this group.'));
                return false;
            }
        }

        // Process
        $table = $group->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            foreach ($friends as $friend) {
                if (!in_array($friend->getIdentity(), $usersIds)) {
                    continue;
                }

                $group->membership()->addMember($friend)
                    ->setResourceApproved($friend);

                $notifyApi->addNotification($friend, $viewer, $group, 'group_invite');
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Members invited'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function getInvite($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $group = Engine_Api::_()->core()->getSubject();
        $friends = $viewer->membership()->getMembers();

        $multiOptions = array();
        foreach ($friends as $friend) {
            if ($group->membership()->isMember($friend, null)) {
                continue;
            }

            $multiOptions[] = $friend;
        }

        $data = Ynrestapi_Helper_Meta::exportAll($multiOptions, array('simple'));

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function postReject($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Group_Group($subject);
        if (!$helper->isMembershipOption('reject')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'group_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s');
        $message = sprintf($message, $subject->__toString());
        $message = trim(strip_tags(Ynrestapi_Helper_Utils::prepareHtmlHref($message), '<a>'));
        $data = array(
            'message' => $message,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function postAccept($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Group_Group($subject);
        if (!$helper->isMembershipOption('accept')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->setUserApproved($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'group_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $subject, 'group_join');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group %s');
        $message = sprintf($message, $subject->__toString());
        $message = trim(strip_tags(Ynrestapi_Helper_Utils::prepareHtmlHref($message), '<a>'));
        $data = array(
            'message' => $message,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function postLeave($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Group_Group($subject);
        if (!$helper->isMembershipOption('leave')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if ($subject->isOwner($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process form
        $list = $subject->getOfficerList();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            // remove from officer list
            $list->remove($viewer);

            $subject->membership()->removeMember($viewer);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('You have successfully left this group.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function postCancel($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Group_Group($subject);
        if (!$helper->isMembershipOption('cancel')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($viewer);

            // Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $subject->getOwner(), $subject, 'group_approve');
            if ($notification) {
                $notification->delete();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Group membership request cancelled.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function postRequest($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Group_Group($subject);
        if (!$helper->isMembershipOption('request')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process form
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $owner = $subject->getOwner();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'group_approve');
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Group membership request sent'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postJoin($params)
    {
        self::requireScope('groups');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Group_Group($subject);
        if (!$helper->isMembershipOption('join')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // If member is already part of the group
        if ($subject->membership()->isMember($viewer)) {
            $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
            $db->beginTransaction();

            try
            {
                // Set the request as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                    $viewer, $subject, 'group_invite');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->save();
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $data = array(
                'message' => Zend_Registry::get('Zend_Translate')->_('You are already a member of this group.'),
            );

            self::setSuccess(200, $data);
            return true;
        }

        // Process form
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'group_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $subject, 'group_join');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('You are now a member of this group.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postRejectMember($params)
    {
        self::requireScope('groups');

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['group_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('group_id');
        }

        if (empty($params['user_id'])) {
            self::setParamError('user_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['user_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('user_id');
        }

        if (self::isError()) {
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($group);

        // Get user
        if (0 === ($user_id = (int) $params['user_id']) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found.'));
            return false;
        }

        $group = Engine_Api::_()->core()->getSubject();
        $list = $group->getOfficerList();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$group->isOwner($viewer) && !$list->has($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($user);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $user, $subject, 'group_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();
        } catch (Core_Model_Exception $e) {
            self::setError(500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group %s');
        $message = sprintf($message, $subject->__toString());
        $message = trim(strip_tags(Ynrestapi_Helper_Utils::prepareHtmlHref($message), '<a>'));

        $data = array(
            'message' => $message,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postApproveMember($params)
    {
        self::requireScope('groups');

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['group_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('group_id');
        }

        if (empty($params['user_id'])) {
            self::setParamError('user_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['user_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('user_id');
        }

        if (self::isError()) {
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($group);

        // Get user
        if (0 === ($user_id = (int) $params['user_id']) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found.'));
            return false;
        }

        $group = Engine_Api::_()->core()->getSubject();
        $list = $group->getOfficerList();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$group->isOwner($viewer) && !$list->has($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->setResourceApproved($user);

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'group_accepted');

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($user, $subject, 'group_join');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Group request approved'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getWaitingMembers($params)
    {
        $params['waiting'] = true;

        return $this->getMembers($params);
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function deleteMember($params)
    {
        self::requireScope('groups');

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['group_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('group_id');
        }

        if (empty($params['user_id'])) {
            self::setParamError('user_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['user_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('user_id');
        }

        if (self::isError()) {
            return false;
        }

        $group = Engine_Api::_()->getItem('group', $params['group_id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($group);

        // Get user
        if (0 === ($user_id = (int) $params['user_id']) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found.'));
            return false;
        }

        $group = Engine_Api::_()->core()->getSubject();
        $list = $group->getOfficerList();

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$group->isOwner($viewer) && !$list->has($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!$group->membership()->isMember($user)) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Cannot remove a non-member'));
            return false;
        }

        $db = $group->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            // Remove as officer first (if necessary)
            $list->remove($user);

            // Remove membership
            $group->membership()->removeMember($user);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Group member removed.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getMembers($params)
    {
        self::requireScope('groups');

        if (empty($params['group_id'])) {
            self::setParamError('group_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['group_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('group_id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $group = Engine_Api::_()->getItem('group', $params['group_id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        if (!$group->authorization()->isAllowed($viewer, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get params
        $search = isset($params['keywords']) ? $params['keywords'] : '';
        $waiting = (isset($params['waiting']) && false !== filter_var($params['waiting'], FILTER_VALIDATE_BOOLEAN)) ? (bool) $params['waiting'] : false;

        // Prepare data
        $list = $group->getOfficerList();

        // get viewer
        if ($viewer->getIdentity() && ($group->isOwner($viewer) || $list->has($viewer))) {
            $waitingMembers = Zend_Paginator::factory($group->membership()->getMembersSelect(false));
        }

        // if not showing waiting members, get full members
        $select = $group->membership()->getMembersObjectSelect();
        if ($search) {
            $select->where('displayname LIKE ?', '%' . $search . '%');
        }
        $fullMembers = Zend_Paginator::factory($select);

        // if showing waiting members, or no full members
        if ($waiting) {
            if ($viewer->getIdentity() && ($group->isOwner($viewer) || $list->has($viewer))) {
                $paginator = $waitingMembers;
            } else {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
                return false;
            }
        } else {
            $paginator = $fullMembers;
        }

        // Set item count per page and current page number
        $limit = (!empty($params['limit']) && false !== filter_var($params['limit'], FILTER_VALIDATE_INT)) ? $params['limit'] : 10;
        $page = (!empty($params['page']) && false !== filter_var($params['page'], FILTER_VALIDATE_INT)) ? $params['page'] : 1;
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        $data = array();
        foreach ($paginator as $key => $member) {
            if (!empty($member->resource_id)) {
                $memberInfo = $member;
                $member = Engine_Api::_()->getItem('user', $memberInfo->user_id);
            } else {
                $memberInfo = $group->membership()->getMemberInfo($member);
            }
            $listItem = $list->get($member);
            $isOfficer = (null !== $listItem);

            $tmp = Ynrestapi_Helper_Meta::exportOne($member, array('simple', 'status'));

            if ($group->isOwner($member)) {
                $tmp['membership'] = $memberInfo->title ? $memberInfo->title : Zend_Registry::get('Zend_Translate')->_('owner');
            } elseif ($isOfficer) {
                $tmp['membership'] = $memberInfo->title ? $memberInfo->title : Zend_Registry::get('Zend_Translate')->_('officer');
            } else {
                $tmp['membership'] = '';
            }

            if ($params['waiting']) {
                if ($memberInfo->active == false && $memberInfo->resource_approved == false) {
                    $tmp['waiting_type'] = 'requested';
                } elseif ($memberInfo->active == false && $memberInfo->resource_approved == true) {
                    $tmp['waiting_type'] = 'invited';
                }
            }

            $data[] = $tmp;
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function delete($params)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $group = Engine_Api::_()->getItem('group', $params['id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group doesn\'t exists or not authorized to delete'));
            return false;
        }

        if (!$this->requireAuthIsValid($group, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $group->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $group->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('The selected group has been deleted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return null
     */
    public function postItem($params)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $group = Engine_Api::_()->getItem('group', $params['id']);
        if (!$group || !$group->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($group);

        if (!$this->requireAuthIsValid(null, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $officerList = $group->getOfficerList();
        $form = new Group_Form_Edit();

        // Populate with categories
        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('0' => '');
        foreach ($categories as $k => $v) {
            $categoryOptions[$k] = $v;
        }
        $form->category_id->setMultiOptions($categoryOptions);

        if (count($form->category_id->getMultiOptions()) <= 1) {
            $form->removeElement('category_id');
        }

        // Populate auth
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('officer', 'member', 'registered', 'everyone');
        $actions = array('view', 'comment', 'invite', 'photo', 'event');
        $perms = array();
        foreach ($roles as $roleString) {
            $role = $roleString;
            if ($role === 'officer') {
                $role = $officerList;
            }
            foreach ($actions as $action) {
                if ($auth->isAllowed($group, $role, $action)) {
                    $perms['auth_' . $action] = $roleString;
                }
            }
        }

        $form->populate($group->toArray());
        $form->populate($perms);

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps, $group))) {
            return false;
        }

        $size = $form->photo->getMaxFileSize();
        if ($size > 0) {
            $form->photo->setMaxFileSize(0);
            $values['MAX_FILE_SIZE'] = $size;
        }

        if (empty($values['photo'])) {
            $form->removeElement('photo'); // photo is not required
        }

        $form->populate($values);

        if (!$form->isValid($form->getValues())) {
            $messages = $form->getMessages();
            foreach ($messages as $key => $value) {
                if (false !== ($k = array_search($key, $fieldMaps))) {
                    $field = $k;
                } else {
                    $field = $key;
                }
                self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        // Process
        $db = Engine_Api::_()->getItemTable('group')->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();

            // Set group info
            $group->setFromArray($values);
            $group->save();

            if (!empty($values['photo'])) {
                $group->setPhoto($form->photo);
            }

            // Process privacy
            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('officer', 'member', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_photo'], $roles);
            $eventMax = array_search($values['auth_event'], $roles);
            $inviteMax = array_search($values['auth_invite'], $roles);

            foreach ($roles as $i => $role) {
                if ($role === 'officer') {
                    $role = $officerList;
                }
                $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
                $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
                $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
            }

            // Create some auth stuff for all officers
            $auth->setAllowed($group, $officerList, 'photo.edit', 1);
            $auth->setAllowed($group, $officerList, 'topic.edit', 1);

            // Add auth for invited users
            $auth->setAllowed($group, 'member_requested', 'view', 1);

            // Commit
            $db->commit();
        } catch (Engine_Image_Exception $e) {
            $db->rollBack();
            $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($group) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function post($params)
    {
        if (isset($params['id'])) {
            return $this->postItem($params);
        }

        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('group', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Create form
        $form = new Group_Form_Create();

        // Populate with categories
        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('0' => '');
        foreach ($categories as $k => $v) {
            $categoryOptions[$k] = $v;
        }
        $form->category_id->setMultiOptions($categoryOptions);

        if (count($form->category_id->getMultiOptions()) <= 1) {
            $form->removeElement('category_id');
        }

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps))) {
            return false;
        }

        $size = $form->photo->getMaxFileSize();
        if ($size > 0) {
            $form->photo->setMaxFileSize(0);
            $values['MAX_FILE_SIZE'] = $size;
        }

        if (empty($values['photo'])) {
            $form->removeElement('photo'); // photo is not required
        }

        $form->populate($values);

        if (!$form->isValid($form->getValues())) {
            $messages = $form->getMessages();
            foreach ($messages as $key => $value) {
                if (false !== ($k = array_search($key, $fieldMaps))) {
                    $field = $k;
                } else {
                    $field = $key;
                }
                self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        // Process
        $values = $form->getValues();
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();

        $db = Engine_Api::_()->getDbtable('groups', 'group')->getAdapter();
        $db->beginTransaction();

        try {
            // Create group
            $table = Engine_Api::_()->getDbtable('groups', 'group');
            $group = $table->createRow();
            $group->setFromArray($values);
            $group->save();

            // Add owner as member
            $group->membership()->addMember($viewer)
                ->setUserApproved($viewer)
                ->setResourceApproved($viewer);

            // Set photo
            if (!empty($values['photo'])) {
                $group->setPhoto($form->photo);
            }

            // Process privacy
            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('officer', 'member', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_photo'], $roles);
            $eventMax = array_search($values['auth_event'], $roles);
            $inviteMax = array_search($values['auth_invite'], $roles);

            $officerList = $group->getOfficerList();

            foreach ($roles as $i => $role) {
                if ($role === 'officer') {
                    $role = $officerList;
                }
                $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
                $auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
                $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
            }

            // Create some auth stuff for all officers
            $auth->setAllowed($group, $officerList, 'photo.edit', 1);
            $auth->setAllowed($group, $officerList, 'topic.edit', 1);

            // Add auth for invited users
            $auth->setAllowed($group, 'member_requested', 'view', 1);

            // Add action
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $group, 'group_create');
            if ($action) {
                $activityApi->attachActivity($action, $group);
            }

            // Commit
            $db->commit();

            $data = array(
                'id' => $group->getIdentity(),
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Engine_Image_Exception $e) {
            $db->rollBack();
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @param $params
     * @param $fieldMaps
     */
    private function _getPostValues($params, &$fieldMaps, $group = null)
    {
        $values = array();

        if (isset($params['title'])) {
            $values['title'] = $params['title'];
        }

        if (isset($params['description'])) {
            $values['description'] = $params['description'];
        }

        if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $values['photo'] = $_FILES['photo']['name'];
        }

        if (isset($params['category_id'])) {
            $categories = $this->getCategories(null, true);
            if (!array_key_exists($params['category_id'], $categories)) {
                self::setParamError('category_id');
                return false;
            }
            $values['category_id'] = $params['category_id'];
        } elseif (empty($group)) {
            $values['category_id'] = 0;
        }

        if (isset($params['allow_search'])) {
            if ('0' !== strval($params['allow_search']) && '1' !== strval($params['allow_search'])) {
                self::setParamError('allow_search');
                return false;
            }
            $values['search'] = $params['allow_search'];
        } elseif (empty($group)) {
            $values['search'] = 1;
        }
        $fieldMaps['allow_search'] = 'search';

        if (isset($params['auth_invite'])) {
            $inviteOptions = array(
                'member' => 'Yes, members can invite other people.',
                'officer' => 'No, only officers can invite other people.',
            );
            if (!array_key_exists($params['auth_invite'], $inviteOptions)) {
                self::setParamError('auth_invite');
                return false;
            }
            $values['auth_invite'] = $params['auth_invite'];
        } elseif (empty($group)) {
            $values['auth_invite'] = 'member';
        }

        if (isset($params['approval'])) {
            if ('0' !== strval($params['approval']) && '1' !== strval($params['approval'])) {
                self::setParamError('approval');
                return false;
            }
            $values['approval'] = $params['approval'];
        } elseif (empty($group)) {
            $values['approval'] = 0;
        }

        if (isset($params['auth_view'])) {
            $viewOptions = $this->getViewOptions(null, true);
            if (!array_key_exists($params['auth_view'], $viewOptions)) {
                self::setParamError('auth_view');
                return false;
            }
            $values['auth_view'] = $params['auth_view'];
        }

        if (isset($params['auth_comment'])) {
            $viewOptions = $this->getCommentOptions(null, true);
            if (!array_key_exists($params['auth_comment'], $viewOptions)) {
                self::setParamError('auth_comment');
                return false;
            }
            $values['auth_comment'] = $params['auth_comment'];
        }

        if (isset($params['auth_photo'])) {
            $viewOptions = $this->getPhotoOptions(null, true);
            if (!array_key_exists($params['auth_photo'], $viewOptions)) {
                self::setParamError('auth_photo');
                return false;
            }
            $values['auth_photo'] = $params['auth_photo'];
        }

        if (isset($params['auth_event'])) {
            $viewOptions = $this->getEventOptions(null, true);
            if (!array_key_exists($params['auth_event'], $viewOptions)) {
                self::setParamError('auth_event');
                return false;
            }
            $values['auth_event'] = $params['auth_event'];
        }

        return $values;
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getEventOptions($params, $return = false)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $eventOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_event');
        $eventOptions = array_intersect_key($this->availablePrivacies, array_flip($eventOptions));

        if ($return) {
            return $eventOptions;
        }

        $data = array();
        foreach ($eventOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getPhotoOptions($params, $return = false)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_photo');
        $photoOptions = array_intersect_key($this->availablePrivacies, array_flip($photoOptions));

        if ($return) {
            return $photoOptions;
        }

        $data = array();
        foreach ($photoOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getCommentOptions($params, $return = false)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_comment');
        $commentOptions = array_intersect_key($this->availablePrivacies, array_flip($commentOptions));

        if ($return) {
            return $commentOptions;
        }

        $data = array();
        foreach ($commentOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getViewOptions($params, $return = false)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_view');
        $viewOptions = array_intersect_key($this->availablePrivacies, array_flip($viewOptions));

        if ($return) {
            return $viewOptions;
        }

        $data = array();
        foreach ($viewOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getCategories($params, $return = false)
    {
        self::requireScope('groups');

        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();

        if ($return) {
            return $categories;
        }

        $data = array();
        foreach ($categories as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getItem($params)
    {
        self::requireScope('groups');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $subject = Engine_Api::_()->getItem('group', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Group not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        if (!$this->requireAuthIsValid($subject, Engine_Api::_()->user()->getViewer(), 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Increment view count
        if (!$subject->getOwner()->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        $fields = $this->_getFields($params, 'detail');
        $data = Ynrestapi_Helper_Meta::exportOne($subject, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getMy($params)
    {
        self::requireScope('groups');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Form
        $formFilter = new Group_Form_Filter_Manage();
        $fieldMaps = array();

        if (false === ($values = $this->_getFilterManageValues($params, $fieldMaps))) {
            return false;
        }

        $formFilter->populate($values);

        if (!$formFilter->isValid($formFilter->getValues())) {
            $messages = $formFilter->getMessages();
            foreach ($messages as $key => $value) {
                if (false !== ($k = array_search($key, $fieldMaps))) {
                    $field = $k;
                } else {
                    $field = $key;
                }
                self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        $values = $formFilter->getValues();

        $viewer = Engine_Api::_()->user()->getViewer();
        $membership = Engine_Api::_()->getDbtable('membership', 'group');
        $select = $membership->getMembershipsOfSelect($viewer);
        $select->where('group_id IS NOT NULL');

        $table = Engine_Api::_()->getItemTable('group');
        $tName = $table->info('name');
        if ($values['view'] == 2) {
            $select->where("`{$tName}`.`user_id` = ?", $viewer->getIdentity());
        }
        if (!empty($values['text'])) {
            $select->where(
                $table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $values['text'] . '%') . ' OR ' .
                $table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $values['text'] . '%')
            );
        }

        $paginator = Zend_Paginator::factory($select);

        $limit = isset($params['limit']) ? (int) $params['limit'] : null;
        $paginator->setItemCountPerPage($limit);

        $page = isset($params['page']) ? (int) $params['page'] : null;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $fieldMaps
     * @return mixed
     */
    private function _getFilterManageValues($params, &$fieldMaps)
    {
        $values = array();

        if (!empty($params['keywords'])) {
            $values['text'] = $params['keywords'];
            $fieldMaps['keywords'] = 'text';
        }

        if (!empty($params['show'])) {
            $showOptions = array(
                'all_my_groups' => '',
                'only_groups_i_lead' => '2',
            );
            if (!array_key_exists($params['show'], $showOptions)) {
                self::setParamError('show');
                return false;
            }
            $values['view'] = $showOptions[$params['show']];
            $fieldMaps['show'] = 'view';
        }

        return $values;
    }

    /**
     * @param $params
     */
    public function get($params)
    {
        self::requireScope('groups');

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Form
        $formFilter = new Group_Form_Filter_Browse();

        if (!$viewer || !$viewer->getIdentity()) {
            $formFilter->removeElement('view');
        }

        // Populate options
        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        $formFilter->category_id->addMultiOptions($categories);

        $fieldMaps = array();

        if (false === ($values = $this->_getFilterBrowseValues($params, $fieldMaps))) {
            return false;
        }

        $formFilter->populate($values);

        if (!$formFilter->isValid($formFilter->getValues())) {
            $messages = $formFilter->getMessages();
            foreach ($messages as $key => $value) {
                if (false !== ($k = array_search($key, $fieldMaps))) {
                    $field = $k;
                } else {
                    $field = $key;
                }
                self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        $values = $formFilter->getValues();

        if ($viewer->getIdentity() && @$values['view'] == 1) {
            $values['users'] = array();
            foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
                $values['users'][] = $memberinfo->user_id;
            }
        }

        $values['search'] = 1;

        // check to see if request is for specific user's listings
        if (!empty($params['user_id'])) {
            $values['user_id'] = $params['user_id'];
        }

        // Make paginator
        $paginator = Engine_Api::_()->getItemTable('group')
            ->getGroupPaginator($values);

        $limit = isset($params['limit']) ? (int) $params['limit'] : null;
        $paginator->setItemCountPerPage($limit);

        $page = isset($params['page']) ? (int) $params['page'] : null;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $fieldMaps
     * @return mixed
     */
    private function _getFilterBrowseValues($params, &$fieldMaps)
    {
        $values = array();

        if (!empty($params['keywords'])) {
            $values['search_text'] = $params['keywords'];
            $fieldMaps['keywords'] = 'search_text';
        }

        if (!empty($params['category_id'])) {
            $categories = $this->getCategories(null, true);
            if (!array_key_exists($params['category_id'], $categories)) {
                self::setParamError('category_id');
                return false;
            }
            $values['category_id'] = $params['category_id'];
        }

        if (!empty($params['show'])) {
            $showOptions = array(
                'everyone' => '',
                'only_my_friend' => '1',
            );
            if (!array_key_exists($params['show'], $showOptions)) {
                self::setParamError('show');
                return false;
            }
            $values['view'] = $showOptions[$params['show']];
            $fieldMaps['show'] = 'view';
        }

        if (!empty($params['sort'])) {
            $sortOptions = array(
                'recently_created' => 'creation_date DESC',
                'most_popular' => 'member_count DESC',
            );
            if (!array_key_exists($params['sort'], $sortOptions)) {
                self::setParamError('sort');
                return false;
            }
            $values['order'] = $sortOptions[$params['sort']];
            $fieldMaps['sort'] = 'order';
        }

        return $values;
    }
}
