<?php

class Ynrestapi_Api_Event extends Ynrestapi_Api_Core
{
    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'event';
        $this->mainItemType = 'event';
    }

    /**
     * @param  $params
     * @return null
     */
    public function postPhotosUpload($params)
    {
        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $event = Engine_Api::_()->getItem('event', $params['event_id']);

        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($event);

        if (!$this->requireAuthIsValid($event, null, 'photo')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $album = $event->getSingletonAlbum();

            $values = array(
                // These values will be set in this step from Event plugin but wrong flow
                // 'collection_id' => $album->getIdentity(),
                // 'album_id' => $album->getIdentity(),
                'event_id' => $event->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            $photoTable = Engine_Api::_()->getItemTable('event_photo');
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
        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('event_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        if (!$this->requireAuthIsValid($photo, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
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
        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('event_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        if (!$this->requireAuthIsValid($photo, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $form = new Event_Form_Photo_Edit();

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
        $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
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

        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['ids'])) {
            self::setParamError('ids', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $event = Engine_Api::_()->getItem('event', $params['event_id']);

        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($event);

        if (!$this->requireAuthIsValid($event, null, 'photo')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // validate photo ids
        $values = array(
            'file' => array_unique(explode(',', $params['ids'])),
        );

        foreach ($values['file'] as $photo_id) {
            $photo = Engine_Api::_()->getItem('event_photo', $photo_id);
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
            if ($photo->event_id != $event->getIdentity()) {
                self::setParamError('ids');
                return false;
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $album = $event->getSingletonAlbum();

        // Process
        $table = Engine_Api::_()->getItemTable('event_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            // Add action and attachments
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $event, 'event_photo_upload', null, array(
                'count' => count($values['file']),
            ));

            // Do other stuff
            $count = 0;
            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem('event_photo', $photo_id);
                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
                    continue;
                }

                $photo->collection_id = $album->album_id;
                $photo->album_id = $album->album_id;
                $photo->event_id = $event->event_id;
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
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $photo = Engine_Api::_()->getItem('event_photo', $params['id']);
        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }
        Engine_Api::_()->core()->setSubject($photo);

        $viewer = Engine_Api::_()->user()->getViewer();
        $album = $photo->getCollection();
        $event = $photo->getEvent();

        if (!$this->requireAuthIsValid($event, null, 'view')) {
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

        self::requireScope('events');

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['event_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('event_id');
            return false;
        }

        $event = Engine_Api::_()->getItem('event', $params['event_id']);
        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }
        Engine_Api::_()->core()->setSubject($event);

        if (!$this->requireAuthIsValid($event, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get paginator
        $album = $event->getSingletonAlbum();
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

    private function getOptions($params, $option_type, $return = false)
    {
        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!empty($params['parent_type'])) {
            $parent_type = $params['parent_type'];
            $allowedParentTypes = array('user', 'group');
            if (!in_array($parent_type, $allowedParentTypes)) {
                self::setParamError('parent_type');
                return false;
            }
        } else {
            $parent_type = 'user';
        }

        $availableLabels = array();
        if( $parent_type == 'user' ) {
            $availableLabels = array(
                'everyone'            => 'Everyone',
                'registered'          => 'All Registered Members',
                'owner_network'       => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member'        => 'Friends Only',
                'member'              => 'Event Guests Only',
                'owner'               => 'Just Me'
            );
        } else if( $parent_type == 'group' ) {
            $availableLabels = array(
                'everyone'      => 'Everyone',
                'registered'    => 'All Registered Members',
                'parent_member' => 'Group Members',
                'member'        => 'Event Guests Only',
                'owner'         => 'Just Me',
            );
        }

        $user = Engine_Api::_()->user()->getViewer();

        $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, "auth_$option_type");
        $options = array_intersect_key($availableLabels, array_flip($options));

        if ($return) {
            return $options;
        }

        $data = array();
        foreach ($options as $key => $value) {
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
        return $this->getOptions($params, 'comment', $return);
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getViewOptions($params, $return = false)
    {
        return $this->getOptions($params, 'view', $return);
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getPhotoOptions($params, $return = false)
    {
        return $this->getOptions($params, 'photo', $return);
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getCategories($params, $return = false)
    {
        self::requireScope('events');

        $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();

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

    public function get($params)
    {
        self::requireScope('events');

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        // Check auth
        if (!$this->requireAuthIsValid('event', null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (false === ($values = $this->_getSearchValues($params))) {
            return false;
        }

        $paginator = Engine_Api::_()->getItemTable('event')->getEventPaginator($values);
        if (isset($params['limit'])) {
            $items_count = (int)$params['limit'];
            $paginator->setItemCountPerPage($items_count);
        }
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    public function getMy($params)
    {
        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Check auth
        if (!$this->requireAuthIsValid('event', null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbtable('events', 'event');
        $tableName = $table->info('name');

        // Only mine
        if (isset($params['view'])) {
            $view = array(
                'all_my_events',
                'only_events_i_lead'
            );
            if (!in_array($params['view'], $view)) {
                self::setParamError('view');
                return false;
            } else {
                if ($params['view'] == 'only_events_i_lead') {
                    $select = $table->select()
                        ->where('user_id = ?', $viewer->getIdentity());
                } else {
                    $membership = Engine_Api::_()->getDbtable('membership', 'event');
                    $select = $membership->getMembershipsOfSelect($viewer);
                }
            }
        }
        // All membership
        else {
            $membership = Engine_Api::_()->getDbtable('membership', 'event');
            $select = $membership->getMembershipsOfSelect($viewer);
        }

        if (isset($params['keywords'])) {
            $values['text'] = $params['keywords'];
        }
        if( !empty($values['text']) ) {
            $select->where("`{$tableName}`.title LIKE ?", '%'.$values['text'].'%');
        }

        $select->order('starttime ASC');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(20);
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    public function getItem($params)
    {
        self::requireScope('events');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        if (!$this->requireAuthIsValid($subject, Engine_Api::_()->user()->getViewer(), 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Check block
        if( $viewer->isBlockedBy($subject) )
        {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

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

    public function post($params)
    {
        if (isset($params['id'])) {
            return $this->postItem($params);
        }

        self::requireScope('events');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('event', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if( isset($params['parent_type']) && isset($params['parent_id']) && $params['parent_type'] == 'group' && Engine_Api::_()->hasItemType('group') ) {
            $group = Engine_Api::_()->getItem('group', $params['parent_id']);
            if( !$this->requireAuthIsValid($group, null, 'event') ) {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
                return false;
            }
            $parent_type = $params['parent_type'];
            $parent_id = $params['parent_id'];
        } else {
            $parent_type = 'user';
            $parent_id = $viewer->getIdentity();
        }

        // Create form
        $form = new Event_Form_Create(array(
            'parent_type' => $parent_type,
            'parent_id' => $parent_id
        ));

        // Populate with categories
        $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('0' => '');
        foreach( $categories as $k => $v ) {
            $categoryOptions[$k] = $v;
        }
        if (sizeof($categoryOptions) <= 1) {
            $form->removeElement('category_id');
        } else {
            $form->category_id->setMultiOptions($categoryOptions);
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
                if ($field == 'start_time' || $field == 'end_time') {
                    $value = array('Datetime format is not correct.');
                }
                self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        // Process
        $values = $form->getValues();

        $values['user_id'] = $viewer->getIdentity();
        $values['parent_type'] = $parent_type;
        $values['parent_id'] =  $parent_id;
        if( $parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host']) ) {
            $values['host'] = $group->getTitle();
        }

        // Convert times
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = strtotime($values['starttime']);
        $end = strtotime($values['endtime']);
        date_default_timezone_set($oldTz);
        $values['starttime'] = date('Y-m-d H:i:s', $start);
        $values['endtime'] = date('Y-m-d H:i:s', $end);

        $db = Engine_Api::_()->getDbtable('events', 'event')->getAdapter();
        $db->beginTransaction();

        try
        {
            // Create event
            $table = Engine_Api::_()->getDbtable('events', 'event');
            $event = $table->createRow();

            $event->setFromArray($values);
            $event->save();

            // Add owner as member
            $event->membership()->addMember($viewer)
                ->setUserApproved($viewer)
                ->setResourceApproved($viewer);

            // Add owner rsvp
            $event->membership()
                ->getMemberInfo($viewer)
                ->setFromArray(array('rsvp' => 2))
                ->save();

            // Add photo
            if( !empty($values['photo']) ) {
                $event->setPhoto($form->photo);
            }

            // Set auth
            $auth = Engine_Api::_()->authorization()->context;

            if( $values['parent_type'] == 'group' ) {
                $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            if( empty($values['auth_view']) ) {
                $values['auth_view'] = 'everyone';
            }

            if( empty($values['auth_comment']) ) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_photo'], $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($event, $role, 'view',    ($i <= $viewMax));
                $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($event, $role, 'photo',   ($i <= $photoMax));
            }

            $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

            // Add an entry for member_requested
            $auth->setAllowed($event, 'member_requested', 'view', 1);

            // Add action
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

            $action = $activityApi->addActivity($viewer, $event, 'event_create');

            if( $action ) {
                $activityApi->attachActivity($action, $event);
            }
            // Commit
            $db->commit();

            $data = array(
                'id' => $event->getIdentity(),
            );

            self::setSuccess(200, $data);
            return true;
        }

        catch( Engine_Image_Exception $e )
        {
            $db->rollBack();
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
            return false;
        }

        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }
    }

    public function postItem($params)
    {
        self::requireScope('events');

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
        
        $event = Engine_Api::_()->getItem('event', $params['id']);

        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if ( !($this->requireAuthIsValid(null, null, 'edit') || $event->isOwner($viewer)) ) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($event);
        
        $form = new Event_Form_Edit(array(
            'parent_type'=>$event->parent_type,
            'parent_id'=>$event->parent_id
        ));

        // Populate with categories
        $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
        asort($categories, SORT_LOCALE_STRING);
        $categoryOptions = array('0' => '');
        foreach( $categories as $k => $v ) {
            $categoryOptions[$k] = $v;
        }
        if (sizeof($categoryOptions) <= 1) {
            $form->removeElement('category_id');
        } else {
            $form->category_id->setMultiOptions($categoryOptions);
        }

        // Populate auth
        $auth = Engine_Api::_()->authorization()->context;

        if( $event->parent_type == 'group' ) {
            $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
        } else {
            $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        foreach( $roles as $role ) {
            if( isset($form->auth_view->options[$role]) && $auth->isAllowed($event, $role, 'view') ) {
                $form->auth_view->setValue($role);
            }
            if( isset($form->auth_comment->options[$role]) && $auth->isAllowed($event, $role, 'comment') ) {
                $form->auth_comment->setValue($role);
            }
            if( isset($form->auth_photo->options[$role]) && $auth->isAllowed($event, $role, 'photo') ) {
                $form->auth_photo->setValue($role);
            }
        }
        $form->auth_invite->setValue($auth->isAllowed($event, 'member', 'invite'));
        $form->populate($event->toArray());

        // Convert and re-populate times
        $start = strtotime($event->starttime);
        $end = strtotime($event->endtime);
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = date('Y-m-d H:i:s', $start);
        $end = date('Y-m-d H:i:s', $end);
        date_default_timezone_set($oldTz);

        $form->populate(array(
            'starttime' => $start,
            'endtime' => $end,
        ));

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps, $event))) {
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
        $db = Engine_Api::_()->getItemTable('event')->getAdapter();
        $db->beginTransaction();

        try
        {
            // Set event info
            $event->setFromArray($values);
            $event->save();

            if( !empty($values['photo']) ) {
                $event->setPhoto($form->photo);
            }


            // Process privacy
            $auth = Engine_Api::_()->authorization()->context;

            if( $event->parent_type == 'group' ) {
                $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_photo'], $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($event, $role, 'view',    ($i <= $viewMax));
                $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($event, $role, 'photo',   ($i <= $photoMax));
            }

            $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

            // Commit
            $db->commit();
        }

        catch( Engine_Image_Exception $e )
        {
            $db->rollBack();
            $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
        }

        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach( $actionTable->getActionsByObject($event) as $action ) {
                $actionTable->resetActivityBindings($action);
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }
    
    public function delete($params)
    {
        self::requireScope('events');

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
        $event = Engine_Api::_()->getItem('event', $params['id']);
        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event doesn\'t exists or not authorized to delete'));
            return false;
        }

        if (!$this->requireAuthIsValid($event, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $event->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $event->delete();
            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('The selected event has been deleted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }
    
    public function postJoin($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        // validate rsvp value
        $RSVP = array(
            'attending' => 2,
            'maybe_attending' => 1,
            'not_attending' => 0
        );

        if (empty($params['rsvp'])) {
            self::setParamError('rsvp', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (!array_key_exists($params['rsvp'], $RSVP)) {
            self::setParamError('rsvp');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Event_Event($subject);
        if (!$helper->isMembershipOption('join')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $subject = Engine_Api::_()->core()->getSubject();
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $membership_status = $subject->membership()->getRow($viewer)->active;

            $subject->membership()
                ->addMember($viewer)
                ->setUserApproved($viewer)
            ;

            $row = $subject->membership()
                ->getRow($viewer);

            $row->rsvp = $RSVP[$params['rsvp']];
            $row->save();

            // Add activity if membership status was not valid from before
            if (!$membership_status){
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $subject, 'event_join');
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Event joined'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function postLeave($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Event_Event($subject);
        if (!$helper->isMembershipOption('leave')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if ($subject->isOwner($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($viewer);
            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Event left'),
        );

        self::setSuccess(200, $data);
        return true;
    }
    
    public function postRequest($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Event_Event($subject);
        if (!$helper->isMembershipOption('request')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->addMember($viewer)->setUserApproved($viewer);

            // Add notification
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'event_approve');

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your invite request has been sent.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function postCancel($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Event_Event($subject);
        if (!$helper->isMembershipOption('cancel')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        try
        {
            $subject->membership()->removeMember($viewer);

            // Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $subject->getOwner(), $subject, 'event_approve');
            if( $notification ) {
                $notification->delete();
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your invite request has been cancelled.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function postAccept($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Event_Event($subject);
        if (!$helper->isMembershipOption('accept')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // validate rsvp value
        $RSVP = array(
            'attending' => 2,
            'maybe_attending' => 1,
            'not_attending' => 0
        );

        if (empty($params['rsvp'])) {
            self::setParamError('rsvp', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (!array_key_exists($params['rsvp'], $RSVP)) {
            self::setParamError('rsvp');
            return false;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $membership_status = $subject->membership()->getRow($viewer)->active;

            $subject->membership()->setUserApproved($viewer);

            $row = $subject->membership()
                ->getRow($viewer);

            $row->rsvp = $RSVP[$params['rsvp']];
            $row->save();

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'event_invite');
            if( $notification )
            {
                $notification->mitigated = true;
                $notification->save();
            }

            // Add activity
            if (!$membership_status){
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($viewer, $subject, 'event_join');
            }
            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the event %s');
        $message = sprintf($message, $subject->getTitle());

        $data = array(
            'message' => $message,
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function postReject($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        $helper = new Ynrestapi_Helper_Event_Event($subject);
        if (!$helper->isMembershipOption('reject')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($viewer);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $viewer, $subject, 'event_invite');
            if( $notification )
            {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the event %s');
        $message = sprintf($message, $subject->getTitle());

        $data = array(
            'message' => $message,
        );

        self::setSuccess(200, $data);
        return true;
    }

    private function _getPostValues($params, &$fieldMaps, $event)
    {
        $values = array();

        if (isset($params['title'])) {
            $values['title'] = $params['title'];
        }

        if (isset($params['description'])) {
            $values['description'] = $params['description'];
        }

        if (isset($params['host'])) {
            $values['host'] = $params['host'];
        } else {
            $values['host'] = '';
        }

        if (isset($params['location'])) {
            $values['location'] = $params['location'];
        } else {
            $values['location'] = '';
        }

        $fieldMaps['start_time'] = 'starttime';
        if (isset($params['start_time'])) {
            $values['starttime'] = $params['start_time'];
        }

        $fieldMaps['end_time'] = 'endtime';
        if (isset($params['end_time'])) {
            $values['endtime'] = $params['end_time'];
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
        } elseif (empty($event)) {
            $values['category_id'] = 0;
        }

        if (isset($params['allow_search'])) {
            if ('0' !== strval($params['allow_search']) && '1' !== strval($params['allow_search'])) {
                self::setParamError('allow_search');
                return false;
            }
            $values['search'] = $params['allow_search'];
        } elseif (empty($event)) {
            $values['search'] = 1;
        }
        $fieldMaps['allow_search'] = 'search';

        if (isset($params['approval'])) {
            if ('0' !== strval($params['approval']) && '1' !== strval($params['approval'])) {
                self::setParamError('approval');
                return false;
            }
            $values['approval'] = $params['approval'];
        } elseif (empty($event)) {
            $values['approval'] = 0;
        }

        if (isset($params['auth_invite'])) {
            if ('0' !== strval($params['auth_invite']) && '1' !== strval($params['auth_invite'])) {
                self::setParamError('auth_invite');
                return false;
            }
            $values['auth_invite'] = $params['auth_invite'] ? true : false;
        } elseif (empty($event)) {
            $values['auth_invite'] = true;
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

        return $values;
    }

    private function _getSearchValues($params)
    {
        $values = array();

        if (!empty($params['category_id'])) {
            $categories = $this->getCategories(null, true);
            if (!array_key_exists($params['category_id'], $categories)) {
                self::setParamError('category_id');
                return false;
            }
            $values['category_id'] = $params['category_id'];
        }

        if (!empty($params['filter'])) {
            $filter = array('future', 'past');
            if (!in_array($params['filter'], $filter)) {
                self::setParamError('filter');
                return false;
            } else {
                if( $params['filter'] == 'past' ) {
                    $values['past'] = 1;
                } else {
                    $values['future'] = 1;
                }
            }
        } else {
            $values['future'] = 1;
        }

        if (!empty($params['sort'])) {
            $order = array(
                'starttime',
                'creation_date',
                'member_count'
            );
            if (!in_array($params['sort'], $order)) {
                self::setParamError('sort');
                return false;
            } else {
                $values['order'] = $params['sort'];
            }
        }

        if (isset($params['keywords'])) {
            $values['search_text'] = $params['keywords'];
        }

        if (!empty($params['user_id'])) {
            $values['user_id'] = $params['user_id'];
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if( $viewer->getIdentity() && isset($params['view']) ) {
            $view = array(
                'everyone',
                'only_my_friend'
            );
            if (!in_array($params['view'], $view)) {
                self::setParamError('view');
                return false;
            } else {
                if ($params['view'] == 'only_my_friend') {
                    $values['users'] = array();
                    foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
                        $values['users'][] = $memberinfo->user_id;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * @param $params
     * @return bool
     */
    public function getWaitingMembers($params)
    {
        $params['waiting'] = true;

        return $this->getMembers($params);
    }

    /**
     * @param $params
     * @return bool
     * @throws Zend_Exception
     * @throws Zend_Paginator_Exception
     */
    public function getMembers($params)
    {
        self::requireScope('events');

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['event_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('event_id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $event = Engine_Api::_()->getItem('event', $params['event_id']);
        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        if (!$event->authorization()->isAllowed($viewer, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get params
        $search = isset($params['keywords']) ? $params['keywords'] : '';
        $waiting = (isset($params['waiting']) && false !== filter_var($params['waiting'], FILTER_VALIDATE_BOOLEAN)) ? (bool) $params['waiting'] : false;

        $isOwner= $event->isOwner($viewer);
        $members = null;
        // validate permission to get waiting members
        if( $waiting ) {
            if( $viewer->getIdentity() && $isOwner ) {
                $waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
                $members = $waitingMembers;
            } else {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
                return false;
            }
        }

        if( !$members ) {
            $select = $event->membership()->getMembersObjectSelect();
            if( $search ) {
                $select->where('displayname LIKE ?', '%' . $search . '%');
            }
            $members = Zend_Paginator::factory($select);
        }

        $paginator = $members;

        // Set item count per page and current page number
        $limit = (!empty($params['limit']) && false !== filter_var($params['limit'], FILTER_VALIDATE_INT)) ? $params['limit'] : 10;
        $page = (!empty($params['page']) && false !== filter_var($params['page'], FILTER_VALIDATE_INT)) ? $params['page'] : 1;
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        // prepare return data
        $data = array();
        $RSVP = array(
            0 => Zend_Registry::get('Zend_Translate')->_('Not Attending'),
            1 => Zend_Registry::get('Zend_Translate')->_('Maybe Attending'),
            2 => Zend_Registry::get('Zend_Translate')->_('Attending'),
            3 => Zend_Registry::get('Zend_Translate')->_('Awaiting Reply')
        );

        foreach ($paginator as $member) {
            if( !empty($member->resource_id) ) {
                $memberInfo = $member;
                $member = Engine_Api::_()->getItem('user', $memberInfo->user_id);
            } else {
                $memberInfo = $event->membership()->getMemberInfo($member);
            }

            $tmp = Ynrestapi_Helper_Meta::exportOne($member, array('simple', 'status'));
            $tmp['is_owner'] = $event->isOwner($member);
            $tmp['can_delete'] = ($isOwner && !$event->isOwner($member) && $memberInfo->active == true);
            $tmp['can_approve'] = $tmp['can_reject'] = ($isOwner && $memberInfo->active == false && $memberInfo->resource_approved == false);
            $tmp['can_cancel'] = ($isOwner && $memberInfo->active == false && $memberInfo->resource_approved == true );
            $tmp['rsvp'] = $RSVP[$memberInfo->rsvp];
            $data[] = $tmp;
        }

        self::setSuccess(200, $data);
        return true;
    }

    public function postApproveMember($params)
    {
        self::requireScope('events');

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['event_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('event_id');
        }

        if (empty($params['user_id'])) {
            self::setParamError('user_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['user_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('user_id');
        }

        if (self::isError()) {
            return false;
        }

        $event = Engine_Api::_()->getItem('event', $params['event_id']);
        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($event);

        // Get user
        if (0 === ($user_id = (int) $params['user_id']) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found.'));
            return false;
        }
        
        $subject = $event;

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$subject->isOwner($viewer)) {
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

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'event_accepted');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Event request approved'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function postRejectMember($params)
    {
        self::requireScope('events');

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['event_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('event_id');
        }

        if (empty($params['user_id'])) {
            self::setParamError('user_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['user_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('user_id');
        }

        if (self::isError()) {
            return false;
        }

        $event = Engine_Api::_()->getItem('event', $params['event_id']);
        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($event);

        // Get user
        if (0 === ($user_id = (int) $params['user_id']) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        
        if (!$event->isOwner($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $subject = Engine_Api::_()->core()->getSubject();

        if (!$event->membership()->isMember($user)) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Cannot remove a non-member'));
            return false;
        }

        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->membership()->removeMember($user);

            // Remove the notification?
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
                $event->getOwner(), $event, 'event_approve');
            if( $notification ) {
                $notification->delete();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $message = Zend_Registry::get('Zend_Translate')->_('Event member removed.');

        $data = array(
            'message' => $message,
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function deleteMember($params)
    {
        self::requireScope('events');

        if (empty($params['event_id'])) {
            self::setParamError('event_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['event_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('event_id');
        }

        if (empty($params['user_id'])) {
            self::setParamError('user_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['user_id'], FILTER_VALIDATE_INT)) {
            self::setParamError('user_id');
        }

        if (self::isError()) {
            return false;
        }

        $event = Engine_Api::_()->getItem('event', $params['event_id']);
        if (!$event || !$event->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($event);

        // Get user
        if (0 === ($user_id = (int) $params['user_id']) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$event->isOwner($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!$event->membership()->isMember($user)) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Cannot remove a non-member'));
            return false;
        }

        $db = $event->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {

            // Remove membership
            $event->membership()->removeMember($user);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Event member removed.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function getInvite($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $event = Engine_Api::_()->core()->getSubject();
        $friends = $viewer->membership()->getMembers();

        $multiOptions = array();
        foreach ($friends as $friend) {
            if ($event->membership()->isMember($friend, null)) {
                continue;
            }

            $multiOptions[] = $friend;
        }

        $data = Ynrestapi_Helper_Meta::exportAll($multiOptions, array('simple'));

        self::setSuccess(200, $data);
        return true;
    }

    public function postInvite($params)
    {
        self::requireScope('events');

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
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $event = Engine_Api::_()->core()->getSubject();
        $friends = $viewer->membership()->getMembers();

        $multiOptions = array();
        foreach ($friends as $friend) {
            if ($event->membership()->isMember($friend, null)) {
                continue;
            }

            $multiOptions[$friend->getIdentity()] = $friend->getTitle();
        }

        foreach ($usersIds as $userId) {
            if (!array_key_exists($userId, $multiOptions)) {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('One of the members specified is not in your friends list or is already a member of this event.'));
                return false;
            }
        }

        // Process
        $table = $event->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            foreach ($friends as $friend) {
                if (!in_array($friend->getIdentity(), $usersIds)) {
                    continue;
                }

                $event->membership()->addMember($friend)
                    ->setResourceApproved($friend);

                $notifyApi->addNotification($friend, $viewer, $event, 'event_invite');
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

    public function postRsvp($params)
    {
        self::requireScope('events');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->getItem('event', $params['id']);
        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Event not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // Validate action
        if( !$subject->membership()->isMember($viewer, true))
        {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // validate rsvp value
        $RSVP = array(
            'attending' => 2,
            'maybe_attending' => 1,
            'not_attending' => 0
        );

        if (empty($params['rsvp'])) {
            self::setParamError('rsvp', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (!array_key_exists($params['rsvp'], $RSVP)) {
            self::setParamError('rsvp');
            return false;
        }

        $row = $subject->membership()->getRow($viewer);
        if (!$row) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $row->rsvp = $params['rsvp'];
        $row->save();

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('RSVP Updated.'),
        );

        self::setSuccess(200, $data);
        return true;
    }
}