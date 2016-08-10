<?php

/**
 * class Ynrestapi_Api_Classified
 */
class Ynrestapi_Api_Classified extends Ynrestapi_Api_Base
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
    public function delete($params)
    {
        self::requireScope('classifieds');

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
        $classified = Engine_Api::_()->getItem('classified', $params['id']);
        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Classified listing doesn\'t exist or not authorized to delete'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($classified);

        if (!$this->requireAuthIsValid($classified, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $classified->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $classified->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your classified listing has been deleted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postOpen($params)
    {
        $params['closed'] = 0;
        return $this->_updateClosed($params);
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postClose($params)
    {
        $params['closed'] = 1;
        return $this->_updateClosed($params);
    }

    /**
     * @param  $params
     * @return mixed
     */
    private function _updateClosed($params)
    {
        self::requireScope('classifieds');

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
        $classified = Engine_Api::_()->getItem('classified', $params['id']);
        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Listing not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($classified);

        if (!$this->requireAuthIsValid($classified, $viewer, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $table = $classified->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $classified->closed = $params['closed'];
            $classified->save();

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
        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('classified_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        $classified = $photo->getClassified();

        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Listing not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->requireAuthIsValid($classified, $viewer, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        try {
            if ($classified->photo_id == $photo->file_id) {
                $classified->photo_id = 0;
                $classified->save();
            }
            $photo->delete();
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
        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('classified_photo', $params['id']);

        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Photo not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        $classified = $photo->getClassified();

        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Listing not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->requireAuthIsValid($classified, $viewer, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $form = new Classified_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
        $form->removeElement('title');

        $values = $photo->toArray();

        if (isset($params['description'])) {
            $values['description'] = $params['description'];
        }

        $form->populate($values);

        if (!$form->isValid($values)) {
            $messages = $form->getMessages();
            self::setFormErrors($messages);
            return false;
        }

        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setFromArray($values);
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

        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['classified_id'])) {
            self::setParamError('classified_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $classified = Engine_Api::_()->getItem('classified', $params['classified_id']);

        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Listing not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($classified);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->requireAuthIsValid($classified, $viewer, 'photo')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $photoTable = Engine_Api::_()->getDbtable('photos', 'classified');
        $db = $photoTable->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $album = $classified->getSingletonAlbum();

            $params = array(
                // We can set them now since only one album is allowed
                'collection_id' => $album->getIdentity(),
                'album_id' => $album->getIdentity(),

                'classified_id' => $classified->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            $photo_id = Engine_Api::_()->classified()->createPhoto($params, $_FILES['Filedata'])->photo_id;

            if (!$classified->photo_id) {
                $classified->photo_id = $photo_id;
                $classified->save();
            }

            $db->commit();

            $data = array(
                'id' => $photo_id,
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @param  $params
     * @param  $return
     * @return mixed
     */
    public function getCommentOptions($params, $return = false)
    {
        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $user, 'auth_comment');
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
        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('classified', $user, 'auth_view');
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
        self::requireScope('classifieds');

        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();

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
     * @return null
     */
    public function postItem($params)
    {
        self::requireScope('classifieds');

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
        $classified = Engine_Api::_()->getItem('classified', $params['id']);
        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Listing not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($classified);

        if (!$this->requireAuthIsValid($classified, $viewer, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Prepare form
        $form = new Classified_Form_Edit(array(
            'item' => $classified,
        ));

        $form->removeElement('photo');

        $album = $classified->getSingletonAlbum();
        $paginator = $album->getCollectiblesPaginator();

        $paginator->setCurrentPageNumber(1);
        $paginator->setItemCountPerPage(1000);

        foreach ($paginator as $photo) {
            $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
        }

        // prepare tags
        $classifiedTags = $classified->tags()->getTagMaps();
        //$form->getSubForm('custom')->saveValues();

        $tagString = '';
        foreach ($classifiedTags as $tagmap) {
            if ($tagString !== '') {
                $tagString .= ', ';
            }

            $tagString .= $tagmap->getTag()->getTitle();
        }

        $form->tags->setValue($tagString);

        // etc
        $form->populate($classified->toArray());
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $role) {
            if ($form->auth_view && 1 === $auth->isAllowed($classified, $role, 'view')) {
                $form->auth_view->setValue($role);
            }
            if ($form->auth_comment && 1 === $auth->isAllowed($classified, $role, 'comment')) {
                $form->auth_comment->setValue($role);
            }
        }

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps, $classified, $form))) {
            return false;
        }

        $form->populate($values);

        if (!$form->isValid($values)) {
            $messages = $form->getMessages();
            self::setFormErrors($messages, $fieldMaps);
            return false;
        }

        // Process

        // handle save for tags
        $values = $form->getValues();
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map('trim', $tags));

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {

            $classified->setFromArray($values);
            $classified->modified_date = date('Y-m-d H:i:s');

            $classified->tags()->setTagMaps($viewer, $tags);
            $classified->save();

            $cover = $values['cover'];

            // Process
            foreach ($paginator as $photo) {
                if (isset($cover) && $cover == $photo->photo_id) {
                    $classified->photo_id = $photo->file_id;
                    $classified->save();
                }
            }

            // Save custom fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($classified);
            $customfieldform->saveValues();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (!empty($values['auth_view'])) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = 'everyone';
            }
            $viewMax = array_search($auth_view, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (!empty($values['auth_comment'])) {
                $auth_comment = $values['auth_comment'];
            } else {
                $auth_comment = 'everyone';
            }
            $commentMax = array_search($auth_comment, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
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
            foreach ($actionTable->getActionsByObject($classified) as $action) {
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

        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('classified', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $form = new Classified_Form_Create();

        // set up data needed to check quota
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();
        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values);

        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
        $current_count = $paginator->getTotalItemCount();

        if (($current_count >= $quota) && !empty($quota)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You have already created the maximum number of classified listings allowed. If you would like to create a new listing, please delete an old one first.'));
            return false;
        }

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps, null, $form))) {
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

        if (!$form->isValid($values)) {
            $messages = $form->getMessages();
            self::setFormErrors($messages, $fieldMaps);
            return false;
        }

        // Process
        $table = Engine_Api::_()->getItemTable('classified');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            // Create classified
            $values = array_merge($form->getValues(), array(
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
            ));

            $classified = $table->createRow();
            $classified->setFromArray($values);
            $classified->save();

            // Set photo
            if (!empty($values['photo'])) {
                $classified->setPhoto($form->photo);
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map('trim', $tags));
            $classified->tags()->addTagMaps($viewer, $tags);

            // Add fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($classified);
            $customfieldform->saveValues();

            // Set privacy
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = array('everyone');
            }
            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = array('everyone');
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
            }

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $classified, 'classified_new');
            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $classified);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'id' => $classified->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $fieldMaps
     * @param  $classified
     * @return mixed
     */
    private function _getPostValues($params, &$fieldMaps, $classified = null, $form = null)
    {
        $values = array();

        if (isset($params['title'])) {
            $values['title'] = $params['title'];
        } elseif (!empty($classified)) {
            $values['title'] = $form->title->getValue();
        }

        if (isset($params['tags'])) {
            $values['tags'] = $params['tags'];
        } elseif (!empty($classified)) {
            $values['tags'] = $form->tags->getValue();
        }

        $categories = $this->getCategories(null, true);
        if (0 < count($categories)) {
            if (isset($params['category_id'])) {
                if (!array_key_exists($params['category_id'], $categories)) {
                    self::setParamError('category_id');
                    return false;
                }
                $values['category_id'] = $params['category_id'];
            } elseif (empty($classified)) {
                reset($categories);
                $values['category_id'] = key($categories);
            } elseif (!empty($classified)) {
                $values['category_id'] = $form->category_id->getValue();
            }
        }

        if (isset($params['body'])) {
            $values['body'] = $params['body'];
        } elseif (!empty($classified)) {
            $values['body'] = $form->body->getValue();
        }

        if (empty($classified)) {
            if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
                $values['photo'] = $_FILES['photo']['name'];
            }
        }

        $values['fields'] = array();
        $fieldMaps['fields'] = array();
        $customfieldform = $form->getSubForm('fields');
        foreach ($customfieldform->getElements() as $k => $field) {
            if (0 == strcasecmp('price', $field->getLabel())) {
                $fieldMaps['fields']['price'] = $k;
                if (isset($params['fields']) && is_array($params['fields']) && isset($params['fields']['price'])) {
                    $values['fields'][$k] = $params['fields']['price'];
                } elseif (!empty($classified)) {
                    $values['fields'][$k] = $field->getValue();
                }
            } elseif (0 == strcasecmp('location', $field->getLabel())) {
                $fieldMaps['fields']['location'] = $k;
                if (isset($params['fields']) && is_array($params['fields']) && isset($params['fields']['location'])) {
                    $values['fields'][$k] = $params['fields']['location'];
                } elseif (!empty($classified)) {
                    $values['fields'][$k] = $field->getValue();
                }
            }
        }

        if (isset($params['auth_view'])) {
            $viewOptions = $this->getViewOptions(null, true);
            if (!array_key_exists($params['auth_view'], $viewOptions)) {
                self::setParamError('auth_view');
                return false;
            }
            $values['auth_view'] = $params['auth_view'];
        } elseif (!empty($classified)) {
            $values['auth_view'] = $form->auth_view->getValue();
        }

        if (isset($params['auth_comment'])) {
            $viewOptions = $this->getCommentOptions(null, true);
            if (!array_key_exists($params['auth_comment'], $viewOptions)) {
                self::setParamError('auth_comment');
                return false;
            }
            $values['auth_comment'] = $params['auth_comment'];
        } elseif (!empty($classified)) {
            $values['auth_comment'] = $form->auth_comment->getValue();
        }

        if (!empty($classified)) {
            $fieldMaps['main_photo_id'] = 'cover';
            if (isset($params['main_photo_id'])) {
                $values['cover'] = $params['main_photo_id'];
            }
        }

        return $values;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getItem($params)
    {
        self::requireScope('classifieds');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $classified = Engine_Api::_()->getItem('classified', $params['id']);
        if (!$classified || !$classified->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('The classified you are looking for does not exist or has been deleted.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($classified);

        if (!$this->requireAuthIsValid($classified, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = Engine_Api::_()->getItem('user', $classified->owner_id);

        if (!$owner->isSelf($viewer)) {
            $classified->view_count++;
            $classified->save();
        }

        $fields = $this->_getFields($params, 'detail');
        $data = Ynrestapi_Helper_Meta::exportOne($classified, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getMy($params)
    {
        self::requireScope('classifieds');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Classified_Form_Search();
        $form->removeElement('show');

        // Populate form
        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        if (!empty($categories) && is_array($categories) && $form->getElement('category')) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        // Process form
        $fieldMaps = array();

        if (false === ($values = $this->_getSearchValues($params, $fieldMaps, $form))) {
            return false;
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

        $values = $form->getValues();

        //$customFieldValues = $form->getSubForm('custom')->getValues();
        $values['user_id'] = $viewer->getIdentity();

        // custom field search
        $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        // Process options
        $tmp = array();
        foreach ($customFieldValues as $k => $v) {
            if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                continue;
            }

            if (false !== strpos($k, '_field_')) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } else if (false !== strpos($k, '_alias_')) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $customFieldValues = $tmp;
        // Get paginator
        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values, $customFieldValues);

        $items_count = isset($params['limit']) ? (int) $params['limit'] : (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
        $paginator->setItemCountPerPage($items_count);

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
    public function get($params)
    {
        self::requireScope('classifieds');

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        // Check auth
        if (!$this->requireAuthIsValid('classified', null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Prepare form
        $form = new Classified_Form_Search();

        if (!$viewer->getIdentity()) {
            $form->removeElement('show');
        }

        // Populate form
        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        if (!empty($categories) && is_array($categories) && $form->getElement('category')) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        $fieldMaps = array();

        if (false === ($values = $this->_getSearchValues($params, $fieldMaps, $form))) {
            return false;
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

        $values = $form->getValues();

        $customFieldValues = array_intersect_key($values, $form->getFieldElements());

        // Process options
        $tmp = array();
        foreach ($customFieldValues as $k => $v) {
            if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                continue;
            } else if (false !== strpos($k, '_field_')) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } else if (false !== strpos($k, '_alias_')) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $customFieldValues = $tmp;

        // Do the show thingy
        if (@$values['show'] == 2) {
            // Get an array of friend ids to pass to getClassifiedsPaginator
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            //unset($values['show']);
            $values['users'] = $ids;
        }

        // check to see if request is for specific user's listings
        if (!empty($params['user_id'])) {
            $values['user_id'] = $params['user_id'];
        }

        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values, $customFieldValues);
        $items_count = isset($params['limit']) ? (int) $params['limit'] : (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @param  $fieldMaps
     * @param  $form
     * @return mixed
     */
    private function _getSearchValues($params, &$fieldMaps, $form)
    {
        $values = array();

        if (!empty($params['category_id'])) {
            $categories = $this->getCategories(null, true);
            if (!array_key_exists($params['category_id'], $categories)) {
                self::setParamError('category_id');
                return false;
            }
            $values['category'] = $params['category_id'];
            $fieldMaps['category_id'] = 'category';
        }

        if (!empty($params['status'])) {
            $statusOptions = array(
                'all' => '',
                'only_open' => '0',
                'only_closed' => '1',
            );
            if (!array_key_exists($params['status'], $statusOptions)) {
                self::setParamError('status');
                return false;
            }
            $values['closed'] = $statusOptions[$params['status']];
            $fieldMaps['status'] = 'closed';
        }

        if (!empty($params['sort'])) {
            $sortOptions = array(
                'most_recent' => 'creation_date',
                'most_viewed' => 'view_count',
            );
            if (!array_key_exists($params['sort'], $sortOptions)) {
                self::setParamError('sort');
                return false;
            }
            $values['orderby'] = $sortOptions[$params['sort']];
            $fieldMaps['sort'] = 'orderby';
        }

        if (!empty($params['keywords'])) {
            $values['search'] = $params['keywords'];
            $fieldMaps['keywords'] = 'search';
        }

        if (!empty($params['has_photo'])) {
            if ('0' !== strval($params['has_photo']) && '1' !== strval($params['has_photo'])) {
                self::setParamError('has_photo');
                return false;
            }
            $values['has_photo'] = $params['has_photo'];
        }

        $customFields = $form->getFieldElements();
        foreach ($customFields as $k => $v) {
            if (preg_match('/_alias_price$/', $k)) {
                if (!empty($params['price_min'])) {
                    $values[$k]['min'] = $params['price_min'];
                }
                if (!empty($params['price_max'])) {
                    $values[$k]['max'] = $params['price_max'];
                }
                $fieldMaps['price'] = $k;
            } elseif (preg_match('/_alias_location$/', $k)) {
                if (!empty($params['location'])) {
                    $values[$k] = $params['location'];
                }
                $fieldMaps['location'] = $k;
            }
        }

        return $values;
    }
}
