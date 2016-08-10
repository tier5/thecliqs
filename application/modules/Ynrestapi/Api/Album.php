<?php

/**
 * class Ynrestapi_Api_Album
 */
class Ynrestapi_Api_Album extends Ynrestapi_Api_Base
{
    /**
     * @var array
     */
    protected $availablePrivacies = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me',
    );

    /**
     * @param  $params
     * @return mixed
     */
    public function postCover($params)
    {
        self::requireScope('albums');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        if (!isset($params['photo_id'])) {
            self::setParamError('photo_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $album = Engine_Api::_()->getItem('album', $params['id']);
        if (!($album instanceof Core_Model_Item_Abstract) || !$album->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Album not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($album);

        $photo = Engine_Api::_()->getItem('album_photo', $params['photo_id']);
        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        $parent = $photo->getParent();
        if (!($parent instanceof Core_Model_Item_Abstract) || !$parent->getIdentity()
            || $parent->getIdentity() != $album->getIdentity()) {
            self::setParamError('photo_id');
            return false;
        }

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid(null, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $table = $album->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $album->photo_id = $params['photo_id'];
            $album->save();

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
    public function deletePhotos($params)
    {
        self::requireScope('albums');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('album_photo', $params['id']);
        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Not Found.'));
            return false;
        }

        $album = $photo->getParent();
        if (!$album || !$album->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Not Found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        if (!$this->requireAuthIsValid(null, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        try {
            // delete photo
            Engine_Api::_()->getDbtable('photos', 'album')->delete(array('photo_id = ?' => $photo->photo_id));

            // delete files from server
            $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

            $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $photo->file_id))->storage_path;
            unlink($filePath);

            $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $photo->file_id))->storage_path;
            unlink($thumbPath);

            // Delete image and thumbnail
            $filesDB->delete(array('file_id = ?' => $photo->file_id));
            $filesDB->delete(array('parent_file_id = ?' => $photo->file_id));

            // Check activity actions
            $attachDB = Engine_Api::_()->getDbtable('attachments', 'activity');
            $actions = $attachDB->fetchAll($attachDB->select()->where('type = ?', 'album_photo')->where('id = ?', $photo->photo_id));
            $actionsDB = Engine_Api::_()->getDbtable('actions', 'activity');

            foreach ($actions as $action) {
                $action_id = $action->action_id;
                $attachDB->delete(array('type = ?' => 'album_photo', 'id = ?' => $photo->photo_id));

                $action = $actionsDB->fetchRow($actionsDB->select()->where('action_id = ?', $action_id));
                $count = $action->params['count'];
                if (!is_null($count) && ($count > 1)) {
                    $action->params = array('count' => (integer) $count - 1);
                    $action->save();
                } else {
                    $action->delete();
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postPhotosItem($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('album_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!empty($params['album_id'])) {
            $album = Engine_Api::_()->getItem('album', $params['album_id']);
            if (!($album instanceof Core_Model_Item_Abstract) || !$album->getIdentity()) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Album not found.'));
                return false;
            }

            if (!$album->isOwner($viewer)) {
                self::setParamError('album_id');
                return false;
            }
        }

        if (!$this->requireAuthIsValid(null, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $form = new Album_Form_Photo_Edit();

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
            foreach ($messages as $key => $value) {
                self::setParamError($key, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setFromArray($values);

            if (!empty($params['album_id'])) {
                $nextPhoto = $photo->getNextPhoto();

                $oldAlbum = $photo->getParent();
                $photo->album_id = $params['album_id'];

                // Change album cover if necessary
                if (($nextPhoto instanceof Album_Model_Photo) &&
                    (int) $oldAlbum->photo_id == (int) $photo->getIdentity()) {
                    $oldAlbum->photo_id = $nextPhoto->getIdentity();
                    $oldAlbum->save();
                }

                // Remove activity attachments for this photo
                Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
            }

            $photo->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'),
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

        // Force edit
        self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        return false;
    }

    /**
     * Delete Album
     *
     * @param  $params
     * @return mixed
     */
    public function delete($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $album = Engine_Api::_()->getItem('album', $params['id']);

        if (!($album instanceof Core_Model_Item_Abstract) || !$album->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Album doesn\'t exists or not authorized to delete'));
            return false;
        }

        if (!$this->requireAuthIsValid($album, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $album->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Album has been deleted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Edit Album
     *
     * @param  $params
     * @return mixed
     */
    public function post($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        // Prepare data
        $album = Engine_Api::_()->getItem('album', $params['id']);

        if (!($album instanceof Core_Model_Item_Abstract) || !$album->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Album not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($album, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $values = $params;

        // Filter HTML
        $titleFilter = new Zend_Filter();
        $titleFilter->addFilter(new Zend_Filter_StripTags());
        $titleFilter->addFilter(new Engine_Filter_Censor());
        $titleFilter->addFilter(new Engine_Filter_StringLength(array('max' => '63')));
        $values['title'] = $titleFilter->filter($values['title']);

        $descriptionFilter = new Zend_Filter();
        $descriptionFilter->addFilter(new Zend_Filter_StripTags());
        $descriptionFilter->addFilter(new Engine_Filter_Censor());
        $descriptionFilter->addFilter(new Engine_Filter_EnableLinks());
        $values['description'] = $descriptionFilter->filter($values['description']);

        if (isset($values['allow_search'])) {
            if ('0' !== strval($values['allow_search']) && '1' !== strval($values['allow_search'])) {
                self::setParamError('allow_search');
                return false;
            }
        } else {
            $values['allow_search'] = 1;
        }

        $values['title'] = $values['title'];
        if (empty($values['title'])) {
            $values['title'] = 'Untitled Album';
        }
        $values['category_id'] = (int) @$values['category_id'];
        $values['description'] = $values['description'];
        $values['search'] = $values['allow_search'];

        // Process
        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $album->setFromArray($values);
            $album->save();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = key($form->auth_view->options);
                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }
            }
            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = key($form->auth_comment->options);
                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'owner_member';
                }
            }
            if (empty($values['auth_tag'])) {
                $values['auth_tag'] = key($form->auth_tag->options);
                if (empty($values['auth_tag'])) {
                    $values['auth_tag'] = 'owner_member';
                }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $tagMax = array_search($values['auth_tag'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($album) as $action) {
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
     * Get Tag Options
     *
     * @param $params
     */
    public function getTagOptions($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_tag');
        $tagOptions = array_intersect_key($this->availablePrivacies, array_flip($tagOptions));

        $data = array();
        foreach ($tagOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get Comment Options
     *
     * @param $params
     */
    public function getCommentOptions($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_comment');
        $commentOptions = array_intersect_key($this->availablePrivacies, array_flip($commentOptions));

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
     * Get View Options
     *
     * @param $params
     */
    public function getViewOptions($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_view');
        $viewOptions = array_intersect_key($this->availablePrivacies, array_flip($viewOptions));

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
     * Get Categories
     *
     * @param $params
     */
    public function getCategories($params)
    {
        self::requireScope('albums');

        $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();

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
     * Add New Photos
     *
     * @param  $params
     * @return null
     */
    public function postUpload($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('album', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $values = $params;
        if (isset($values['album']) && is_array($values['album'])) {
            $values = array_merge($values, $values['album']);
            unset($values['album']);
        }

        $db = Engine_Api::_()->getItemTable('album')->getAdapter();
        $db->beginTransaction();

        try
        {
            if (!($album = $this->_saveValues($values))) {
                return false;
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'album_id' => $album->album_id,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @return mixed
     */
    private function _saveValues($values)
    {
        $set_cover = false;
        $params = array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
            $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
            $params['owner_type'] = 'user';
        } else {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
            throw new Zend_Exception('Non-user album owners not yet implemented');
        }

        // validate photo_ids
        $values['photo_ids'] = !empty($values['photo_ids']) ? explode(',', $values['photo_ids']) : array();
        $values['photo_ids'] = array_unique($values['photo_ids']);
        foreach ($values['photo_ids'] as $photo_id) {
            $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
            // Not a photo
            if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
                self::setParamError('photo_ids');
                return false;
            }

            // Not owner
            if ($photo->getOwner()->getIdentity() != Engine_Api::_()->user()->getViewer()->getIdentity()) {
                self::setParamError('photo_ids');
                return false;
            }

            // Photo is belong to another album
            if ($photo->album_id) {
                self::setParamError('photo_ids');
                return false;
            }
        }

        if (!isset($values['album_id'])) {
            // Filter HTML
            $titleFilter = new Zend_Filter();
            $titleFilter->addFilter(new Zend_Filter_StripTags());
            $titleFilter->addFilter(new Engine_Filter_Censor());
            $titleFilter->addFilter(new Engine_Filter_StringLength(array('max' => '63')));
            $values['title'] = $titleFilter->filter($values['title']);

            $descriptionFilter = new Zend_Filter();
            $descriptionFilter->addFilter(new Zend_Filter_StripTags());
            $descriptionFilter->addFilter(new Engine_Filter_Censor());
            $descriptionFilter->addFilter(new Engine_Filter_EnableLinks());
            $values['description'] = $descriptionFilter->filter($values['description']);

            if (isset($values['allow_search'])) {
                if ('0' !== strval($values['allow_search']) && '1' !== strval($values['allow_search'])) {
                    self::setParamError('allow_search');
                    return false;
                }
            } else {
                $values['allow_search'] = 1;
            }

            $params['title'] = $values['title'];
            if (empty($params['title'])) {
                $params['title'] = 'Untitled Album';
            }
            $params['category_id'] = (int) @$values['category_id'];
            $params['description'] = $values['description'];
            $params['search'] = $values['allow_search'];

            $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
            $album->setFromArray($params);
            $album->save();

            $set_cover = true;

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = key($form->auth_view->options);
                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }
            }
            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = key($form->auth_comment->options);
                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'owner_member';
                }
            }
            if (empty($values['auth_tag'])) {
                $values['auth_tag'] = key($form->auth_tag->options);
                if (empty($values['auth_tag'])) {
                    $values['auth_tag'] = 'owner_member';
                }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $tagMax = array_search($values['auth_tag'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
            }
        } else {
            $album = Engine_Api::_()->getItem('album', $values['album_id']);
            if (!($album instanceof Core_Model_Item_Abstract) || !$album->getIdentity()) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Album not found.'));
                return false;
            }
        }

        // Add action and attachments
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['photo_ids'])));

        // Do other stuff
        $count = 0;
        foreach ($values['photo_ids'] as $photo_id) {
            $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
            if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
                continue;
            }

            if ($set_cover) {
                $album->photo_id = $photo_id;
                $album->save();
                $set_cover = false;
            }

            $photo->album_id = $album->album_id;
            $photo->order = $photo_id;
            $photo->save();

            if ($action instanceof Activity_Model_Action && $count < 8) {
                $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
            }
            $count++;
        }

        return $album;
    }

    /**
     * Upload photo
     *
     * @param  $params
     * @return null
     */
    public function postPhotoUpload($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('album', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try
        {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity(),
            ));
            $photo->save();

            $photo->order = $photo->photo_id;
            $photo->setPhoto($_FILES['Filedata']);
            $photo->save();

            $this->view->status = true;
            $this->view->name = $_FILES['Filedata']['name'];
            $this->view->photo_id = $photo->photo_id;

            $db->commit();
            $data = array(
                'photo_id' => $photo->photo_id,
            );

            self::setSuccess(200, $data);
            return true;

        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @param $params
     */
    public function getPhotosItem($params)
    {
        self::requireScope('albums');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $photo = Engine_Api::_()->getItem('album_photo', $params['id']);
        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Not Found.'));
            return false;
        }

        $album = $photo->getAlbum();
        if (!$album || !$album->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Not Found.'));
            return false;
        }

        if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        // if this is sending a message id, the user is being directed from a coversation
        // check if member is part of the conversation
        $message_id = $params['conversation_id'];
        $message_view = false;
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
                $message_view = true;
            }

        }

        if (!$message_view && !$this->requireAuthIsValid($photo, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
        $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
        $canTag = $album->authorization()->isAllowed($viewer, 'tag');
        $canUntag = $album->isOwner($viewer);

        $nextPhoto = $photo->getNextPhoto();
        $previousPhoto = $photo->getPreviousPhoto();

        // Get tags
        $tags = array();
        foreach ($photo->tags()->getTagMaps() as $tagmap) {
            $tags[] = array_merge($tagmap->toArray(), array(
                'id' => $tagmap->getIdentity(),
                'text' => $tagmap->getTitle(),
                'href' => $tagmap->getHref(),
                'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id,
            ));
        }

        $fields = $this->_getFields($params, 'detail');

        $exportParams = array(
            'album' => $album,
            'message_view' => $message_view,
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
            'can_tag' => $canTag,
            'can_untag' => $canUntag,
            'next_photo' => $nextPhoto,
            'previous_photo' => $previousPhoto,
            'tags' => $tags,
        );

        $data = Ynrestapi_Helper_Meta::exportOne($photo, $fields, $exportParams);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get photos
     *
     * @param  $params
     * @return null
     */
    public function getPhotos($params)
    {
        if (isset($params['id'])) {
            // Get photo item
            return $this->getPhotosItem($params);
        }

        // Force get photo item
        self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        return false;
    }

    /**
     * Get album detail
     *
     * @param  $params
     * @return null
     */
    public function getItem($params)
    {
        self::requireScope('albums');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $album = Engine_Api::_()->getItem('album', $params['id']);
        if (!$this->requireAuthIsValid($album, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Prepare params
        $page = $params['photo_page'];
        $limit = isset($params['photo_limit']) ? $params['photo_limit'] : $settings->getSetting('album_page', 25);

        // Prepare data
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $paginator = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        // Do other stuff
        $mine = true;
        $canEdit = $this->requireAuthIsValid($album, null, 'edit');
        if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
            $album->getTable()->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
            ), array(
                'album_id = ?' => $album->getIdentity(),
            ));
            $mine = false;
        }

        $fields = $this->_getFields($params, 'detail');
        $photoFields = $this->_getFields($params, 'listing', 'photo_fields');
        $exportParams = array(
            'mine' => $mine,
            'can_edit' => $canEdit,
            'photo_paginator' => $paginator,
            'photo_fields' => $photoFields,
        );

        $data = Ynrestapi_Helper_Meta::exportOne($album, $fields, $exportParams);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get Authorized User Albums
     *
     * @param  $params
     * @return mixed
     */
    public function getMy($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();

        return $this->get($params);
    }

    /**
     * Get albums
     *
     * @param  $params
     * @return null
     */
    public function get($params)
    {
        self::requireScope('albums');

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        if (!$this->requireAuthIsValid('album', null, 'view')) {
            return;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Get params
        $sort = isset($params['sort']) ? $params['sort'] : 'recent';
        switch ($sort) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'recent':
            default:
                $order = 'modified_date';
                break;
        }

        // Prepare data
        $table = Engine_Api::_()->getItemTable('album');
        if (!in_array($order, $table->info('cols'))) {
            $order = 'modified_date';
        }

        $select = $table->select()
            ->where('search = 1')
            ->order($order . ' DESC');

        $user_id = $params['user_id'];
        if ($user_id) {
            $select->where('owner_id = ?', $user_id);
        }

        if ($params['category_id']) {
            $select->where('category_id = ?', $params['category_id']);
        }

        $search = isset($params['keywords']) ? $params['keywords'] : false;
        if ($search) {
            $select->where('title LIKE ? OR description LIKE ?', '%' . $search . '%');
        }

        $fields = $this->_getFields($params, 'listing');
        $limit = isset($params['limit']) ? (int) $params['limit'] : $settings->getSetting('album_page', 28);
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator = Zend_Paginator::factory($select);

        $data = Ynrestapi_Helper_Meta::exportByPage($paginator, $page, $limit, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postComposeUpload($params)
    {
        self::requireScope('albums');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($_FILES['Filedata'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid data'));
            return false;
        }

        // Get album
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbtable('albums', 'album');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $type = !empty($params['type']) ? $params['type'] : 'wall';

            $album = $table->getSpecialAlbum($viewer, $type);

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            ));
            $photo->save();
            $photo->setPhoto($_FILES['Filedata']);

            if ($type == 'message') {
                $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
            }

            $photo->order = $photo->photo_id;
            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            if ($type != 'message') {
                // Authorizations
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($photo, 'everyone', 'view', true);
                $auth->setAllowed($photo, 'everyone', 'comment', true);
            }

            $db->commit();

            $data = array(
                'message' => Zend_Registry::get('Zend_Translate')->_('Photo saved successfully'),
                'photo_id' => $photo->photo_id,
                'album_id' => $album->album_id,
                'src' => Ynrestapi_Helper_Utils::prepareUrl($photo->getPhotoUrl()),
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
