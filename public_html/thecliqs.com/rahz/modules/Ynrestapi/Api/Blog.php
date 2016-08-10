<?php

/**
 * class Ynrestapi_Api_Blog
 */
class Ynrestapi_Api_Blog extends Ynrestapi_Api_Base
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
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'blog';
        $this->mainItemType = 'blog';
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function delete($params)
    {
        self::requireScope('blogs');

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
        $blog = Engine_Api::_()->getItem('blog', $params['id']);
        if (!$blog || !$blog->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Blog entry doesn\'t exist or not authorized to delete'));
            return false;
        }

        if (!$this->requireAuthIsValid($blog, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $blog->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $blog->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your blog entry has been deleted.'),
        );

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
        self::requireScope('blogs');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $user, 'auth_comment');
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
        self::requireScope('blogs');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $user, 'auth_view');
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
        self::requireScope('blogs');

        $categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();

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
    public function postItem($params)
    {
        self::requireScope('blogs');

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
        $blog = Engine_Api::_()->getItem('blog', $params['id']);
        if (!$blog || !$blog->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Blog not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($blog);

        if (!$this->requireAuthIsValid($blog, $viewer, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Prepare form
        $form = new Blog_Form_Edit();
        $form->getElement('category_id')->setRequired(true);
        $form->removeElement('token');

        // Populate form
        $form->populate($blog->toArray());

        $tagStr = '';
        foreach ($blog->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap->getTag();
            if (!isset($tag->text)) {
                continue;
            }

            if ('' !== $tagStr) {
                $tagStr .= ', ';
            }

            $tagStr .= $tag->text;
        }
        $form->populate(array(
            'tags' => $tagStr,
        ));

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        foreach ($roles as $role) {
            if ($form->auth_view) {
                if ($auth->isAllowed($blog, $role, 'view')) {
                    $form->auth_view->setValue($role);
                }
            }

            if ($form->auth_comment) {
                if ($auth->isAllowed($blog, $role, 'comment')) {
                    $form->auth_comment->setValue($role);
                }
            }
        }

        // hide status change if it has been already published
        if ($blog->draft == '0') {
            $form->removeElement('draft');
        }

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps, $blog))) {
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

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try
        {
            $values = $form->getValues();

            $blog->setFromArray($values);
            $blog->modified_date = date('Y-m-d H:i:s');
            $blog->save();

            // Auth
            if (empty($values['auth_view'])) {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
            }

            // handle tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $blog->tags()->setTagMaps($viewer, $tags);

            // insert new activity if blog is just getting published
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($blog);
            if (count($action->toArray()) <= 0 && $values['draft'] == '0') {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new');
                // make sure action exists before attaching the blog to the activity
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                }
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($blog) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            // Send notifications for subscribers
            Engine_Api::_()->getDbtable('subscriptions', 'blog')
                ->sendNotifications($blog);

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

        self::requireScope('blogs');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('blog', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // set up data needed to check quota
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator($values);

        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'blog', 'max');
        $current_count = $paginator->getTotalItemCount();

        if (!empty($quota) && ($current_count >= $quota)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of entries allowed. If you would like to upload a new entry, please delete an old one first.'));
            return false;
        }

        // Prepare form
        $form = new Blog_Form_Create();
        $categories = $this->getCategories(null, true);
        if (count($categories) > 0) {
            $form->getElement('category_id')->setRequired(true);
        }
        $form->removeElement('token');

        $fieldMaps = array(
            // param => value
        );

        if (false === ($values = $this->_getPostValues($params, $fieldMaps))) {
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

        // Process
        $table = Engine_Api::_()->getItemTable('blog');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Create blog
            $values = array_merge($form->getValues(), array(
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
            ));

            $blog = $table->createRow();
            $blog->setFromArray($values);
            $blog->save();

            // Auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $blog->tags()->addTagMaps($viewer, $tags);

            // Add activity only if blog is published
            if ($values['draft'] == 0) {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new');

                // make sure action exists before attaching the blog to the activity
                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                }

            }

            // Send notifications for subscribers
            Engine_Api::_()->getDbtable('subscriptions', 'blog')
                ->sendNotifications($blog);

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'id' => $blog->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     * @param $fieldMaps
     */
    private function _getPostValues($params, &$fieldMaps, $blog = null)
    {
        $values = array();

        if (isset($params['title'])) {
            $values['title'] = $params['title'];
        }

        if (isset($params['tags'])) {
            $values['tags'] = $params['tags'];
        }

        if (isset($params['category_id'])) {
            $categories = $this->getCategories(null, true);
            if (!array_key_exists($params['category_id'], $categories)) {
                self::setParamError('category_id');
                return false;
            }
            $values['category_id'] = $params['category_id'];
        }

        if (empty($blog) || $blog->draft != '0') {
            if (isset($params['is_draft'])) {
                if ('0' !== strval($params['is_draft']) && '1' !== strval($params['is_draft'])) {
                    self::setParamError('is_draft');
                    return false;
                }
                $values['draft'] = $params['is_draft'];
            } elseif (empty($blog)) {
                $values['draft'] = 0;
            }
            $fieldMaps['is_draft'] = 'draft';
        }

        if (isset($params['body'])) {
            $values['body'] = $params['body'];
        }

        if (isset($params['allow_search'])) {
            if ('0' !== strval($params['allow_search']) && '1' !== strval($params['allow_search'])) {
                self::setParamError('allow_search');
                return false;
            }
            $values['search'] = $params['allow_search'];
        } elseif (empty($blog)) {
            $values['search'] = 1;
        }
        $fieldMaps['allow_search'] = 'search';

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

        return $values;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getItem($params)
    {
        self::requireScope('blogs');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $blog = Engine_Api::_()->getItem('blog', $params['id']);
        if (!$blog || !$blog->getIdentity() ||
            ($blog->draft && !$blog->isOwner($viewer))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Entry not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($blog);

        if (!$this->requireAuthIsValid($blog, $viewer, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Prepare data
        $blogTable = Engine_Api::_()->getDbtable('blogs', 'blog');

        if (!$blog->isOwner($viewer)) {
            $blogTable->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
            ), array(
                'blog_id = ?' => $blog->getIdentity(),
            ));
        }

        // Get category
        if (!empty($blog->category_id)) {
            $category = Engine_Api::_()->getDbtable('categories', 'blog')
                ->find($blog->category_id)->current();
        }

        $fields = $this->_getFields($params, 'detail');
        $data = Ynrestapi_Helper_Meta::exportOne($blog, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function getMy($params)
    {
        self::requireScope('blogs');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();

        return $this->get($params);
    }

    /**
     * @param $params
     */
    public function get($params)
    {
        self::requireScope('blogs');

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();

        // Make form
        $form = new Blog_Form_Search();

        $form->removeElement('draft');
        if (!$viewer->getIdentity()) {
            $form->removeElement('show');
        }

        // Populate form
        $categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();
        if (!empty($categories) && is_array($categories) && $form->getElement('category')) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        $orderbyOptions = array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
        );

        $showOptions = array(
            'everyone' => '1',
            'only_my_friend' => '2',
        );

        $fieldMaps = array(
            'keywords' => 'search',
            'sort' => 'orderby',
            'category_id' => 'category',
        );

        foreach ($fieldMaps as $key => $value) {
            if (isset($params[$key])) {
                $params[$value] = $params[$key];
                unset($params[$key]);
            }
        }

        if (isset($params['orderby'])) {
            if (!array_key_exists($params['orderby'], $orderbyOptions)) {
                self::setParamError('sort');
            }
        }

        if (isset($params['show'])) {
            if (!array_key_exists($params['show'], $showOptions)) {
                self::setParamError('show');
            } else {
                $params['show'] = $showOptions[$params['show']];
            }
        }

        if (isset($params['category'])) {
            if (!array_key_exists($params['category'], $categories)) {
                self::setParamError('category_id');
            }
        }

        if (self::isError()) {
            return false;
        }

        $form->populate($params);

        // Process form
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
        $values['draft'] = '0';
        $values['visible'] = '1';

        if (isset($params['user_id'])) {
            $user = Engine_Api::_()->user()->getUser($params['user_id']);
            if (!$user->getIdentity()) {
                self::setParamError('user_id', 404, 'not_found', Zend_Registry::get('Zend_Translate')->_('User not found'));
                return false;
            } else {
                $values['user_id'] = $params['user_id'];
            }
        }

        // Do the show thingy
        if (@$values['show'] == 2) {
            // Get an array of friend ids
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

        // Get blogs
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator($values);

        $items_per_page = isset($params['limit']) ? (int) $params['limit'] : Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $paginator->setItemCountPerPage($items_per_page);

        $page = isset($params['page']) ? (int) $params['page'] : null;
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }
}
