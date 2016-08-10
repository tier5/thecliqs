<?php

/**
 * class Ynrestapi_Api_Core
 */
class Ynrestapi_Api_Core extends Ynrestapi_Api_Base
{
    /**
     * @param  $params
     * @return null
     */
    public function deleteTags($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['subject_type'])) {
            self::setParamError('subject_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['subject_id'])) {
            self::setParamError('subject_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (!is_numeric($params['subject_id'])) {
            self::setParamError('subject_id');
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (!is_numeric($params['id'])) {
            self::setParamError('id');
        }

        if (self::isError()) {
            return false;
        }

        try {
            $subject = Engine_Api::_()->getItem($params['subject_type'], $params['subject_id']);
        } catch (Engine_Api_Exception $e) {
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        }

        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Subject not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        // if (!$this->requireAuthIsValid(null, null, 'tag')) {
        //     self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
        //     return false;
        // }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Subject doesn't have tagging
        if (!method_exists($subject, 'tags')) {
            // throw new Engine_Exception('Subject doesn\'t support tagging');
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Subject doesn\'t support tagging.'));
            return false;
        }

        // Get tagmao
        $tagmap_id = $params['id'];
        $tagmap = $subject->tags()->getTagMapById($tagmap_id);
        if (!($tagmap instanceof Core_Model_TagMap)) {
            // throw new Engine_Exception('Tagmap missing');
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Tag not found.'));
            return false;
        }

        // Can remove if: is tagger, is tagged, is owner of resource, has tag permission
        if ($viewer->getGuid() != $tagmap->tagger_type . '_' . $tagmap->tagger_id &&
            $viewer->getGuid() != $tagmap->tag_type . '_' . $tagmap->tag_id &&
            !$subject->isOwner($viewer) /* &&
        !$subject->authorization()->isAllowed($viewer, 'tag') */) {
            // throw new Engine_Exception('Not authorized');
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $tagmap->delete();

        self::setSuccess(200);
        return true;
    }

    /**
     * @param  $params
     * @return null
     */
    public function postTags($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['subject_type'])) {
            self::setParamError('subject_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['subject_id'])) {
            self::setParamError('subject_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (!is_numeric($params['subject_id'])) {
            self::setParamError('subject_id');
        }

        if (!isset($params['extra'])) {
            self::setParamError('extra', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (!is_array($params['extra'])) {
            self::setParamError('extra');
        } else {
            $extraParams = array('x', 'y', 'w', 'h');
            foreach ($extraParams as $extra) {
                if (!isset($params['extra'][$extra])) {
                    self::setParamError('extra[' . $extra . ']', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
                } elseif (!is_numeric($params['extra'][$extra])) {
                    self::setParamError('extra[' . $extra . ']');
                }
            }
        }

        if (self::isError()) {
            return false;
        }

        try {
            $subject = Engine_Api::_()->getItem($params['subject_type'], $params['subject_id']);
        } catch (Engine_Api_Exception $e) {
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        }

        if (!$subject || !$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Subject not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        if (!$this->requireAuthIsValid(null, null, 'tag')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!method_exists($subject, 'tags')) {
            // throw new Engine_Exception('whoops! doesn\'t support tagging');
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Subject doesn\'t support tagging.'));
            return false;
        }

        // GUID tagging
        // if (!empty($params['type']) && !empty($params['id'])) {
        //     try {
        //         $tag = Engine_Api::_()->getItem($params['type'], $params['id']);
        //     } catch (Engine_Api_Exception $e) {
        //         self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
        //         return false;
        //     }

        //     if (!$tag || !$tag->getIdentity()) {
        //         self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
        //         return false;
        //     }
        // }

        // USER tagging
        if (!empty($params['user_id'])) {
            $tag = Engine_Api::_()->user()->getUser($params['user_id']);
            if (!$tag || !$tag->getIdentity()) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('User not found.'));
                return false;
            }
        }

        // STRING tagging
        else if (!empty($params['label'])) {
            $tag = $params['label'];
        }

        // Missing params
        else {
            self::setError(400, 'invalid_parameters', Zend_Registry::get('Zend_Translate')->_('Missing tag parameter(s).'));
            return false;
        }

        $tagmap = $subject->tags()->addTagMap($viewer, $tag, $params['extra']);

        if (false === $tagmap) {
            // item has already been tagged
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Item has already been tagged.'));
            return false;
        }

        if (!$tagmap instanceof Core_Model_TagMap) {
            throw new Engine_Exception('Tagmap was not recognised');
        }

        // Do stuff when users are tagged
        if ($tag instanceof User_Model_User && !$subject->isOwner($tag) && !$viewer->isSelf($tag)) {
            // Add activity
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(
                $viewer,
                $tag,
                'tagged',
                '',
                array(
                    'label' => str_replace('_', ' ', $subject->getShortType()),
                )
            );
            if ($action) {
                $action->attach($subject);
            }

            // Add notification
            $type_name = Zend_Registry::get('Zend_Translate')->_(str_replace('_', ' ', $subject->getShortType()));
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $tag,
                $viewer,
                $subject,
                'tagged',
                array(
                    'object_type_name' => $type_name,
                    'label' => $type_name,
                )
            );
        }

        $data = array(
            'id' => $tagmap->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Delete comment
     *
     * @param  $params
     * @return mixed
     */
    public function deleteComments($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['comment_id'])) {
            self::setParamError('comment_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        $subject = Engine_Api::_()->getItem($type, $id);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($subject, null, 'comment')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Comment id
        $comment_id = (int) $params['comment_id'];

        // Comment
        $comment = $subject->comments()->getComment($comment_id);
        if (!$comment) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent'));
            return false;
        }

        // Authorization
        if (!$subject->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity())) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            $subject->comments()->removeComment($comment_id);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Comment deleted'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Post comment
     *
     * @param  $params
     * @return mixed
     */
    public function postComments($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['body'])) {
            self::setParamError('body', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        $subject = Engine_Api::_()->getItem($type, $id);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($subject, null, 'comment')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
        // Process

        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Html(array('AllowedTags' => $allowed_html)));
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $params['body'];
        $body = $filter->filter($body);

        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->comments()->addComment($viewer, $body);

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            // Activity
            $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                'owner' => $subjectOwner->getGuid(),
                'body' => $body,
            ));

            //$activityApi->attachActivity($action, $subject);

            // Notifications

            // Add notification for owner (if user and not viewer)
            $this->view->subject = $subject->getGuid();
            $this->view->owner = $subjectOwner->getGuid();
            if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                    'label' => $subject->getShortType(),
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity()) {
                    continue;
                }

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser->getIdentity();

                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                    'label' => $subject->getShortType(),
                ));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
                // Skip viewer and owner
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity()) {
                    continue;
                }

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser->getIdentity(), $commentedUserNotifications)) {
                    continue;
                }

                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                    'label' => $subject->getShortType(),
                ));
            }

            // Increment comment count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Comment added'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get comments
     *
     * @param  $params
     * @return mixed
     */
    public function getComments($params)
    {
        self::requireScope('basic');

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        $subject = Engine_Api::_()->getItem($type, $id);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Perms
        $exportParams = array(
            'canComment' => $subject->authorization()->isAllowed($viewer, 'comment'),
            'canDelete' => $subject->authorization()->isAllowed($viewer, 'edit'),
        );

        $page = isset($params['page']) ? (int) $params['page'] : null;
        $limit = isset($params['limit']) ? (int) $params['limit'] : null;

        // Comments

        // If has a page, display oldest to newest
        if (null !== $page && null !== $limit) {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $data = Ynrestapi_Helper_Meta::exportByPage($comments, $page, $limit, array('listing'), $exportParams);
        }

        // If not has a page, show the
        else {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
            $data = Ynrestapi_Helper_Meta::exportAll($comments, array('listing'), $exportParams);
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Unlike an item
     *
     * @param  $params
     * @return mixed
     */
    public function deleteLikes($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        $subject = Engine_Api::_()->getItem($type, $id);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($subject, null, 'comment')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $comment_id = isset($params['comment_id']) ? (int) $params['comment_id'] : null;

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
            if (!$commentedItem) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
                return false;
            }
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try
        {
            $commentedItem->likes()->removeLike($viewer);

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
            'message' => Zend_Registry::get('Zend_Translate')->_('Like removed'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Like an item
     *
     * @param  $params
     * @return mixed
     */
    public function postLikes($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        $subject = Engine_Api::_()->getItem($type, $id);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($subject, null, 'comment')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $comment_id = isset($params['comment_id']) ? (int) $params['comment_id'] : null;

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
            if (!$commentedItem) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
                return false;
            }
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {
            $commentedItem->likes()->addLike($viewer);

            // Add notification
            $owner = $commentedItem->getOwner();
            $this->view->owner = $owner->getGuid();
            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                    'label' => $commentedItem->getShortType(),
                ));
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

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
            'message' => Zend_Registry::get('Zend_Translate')->_('Like added'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get likes
     *
     * @param  $params
     * @return mixed
     */
    public function getLikes($params)
    {
        self::requireScope('basic');

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        $subject = Engine_Api::_()->getItem($type, $id);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        // Likes
        $likes = $subject->likes()->getLikePaginator();

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : $likes->getTotalItemCount(); // view all likes

        $data = Ynrestapi_Helper_Meta::exportByPage($likes, $page, $limit, array('listing'));

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Post report
     *
     * @param  $params
     * @return mixed
     */
    public function postReport($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        $categories = $this->getReportTypes(null, true);
        if (!isset($params['type'])) {
            self::setParamError('type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (!array_key_exists($params['type'], $categories)) {
            self::setParamError('type', Zend_Registry::get('Zend_Translate')->_('Invalid data'), 400);
        }

        if (!isset($params['description'])) {
            self::setParamError('description', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $subject = Engine_Api::_()->getItem($params['item_type'], (int) $params['item_id']);
        if (!$subject) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Item not found.'));
            return false;
        }

        $values = array(
            'category' => $params['type'],
            'description' => $params['description'],
        );

        // Process
        $table = Engine_Api::_()->getItemTable('core_report');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            $viewer = Engine_Api::_()->user()->getViewer();

            $report = $table->createRow();
            $report->setFromArray(array_merge($values, array(
                'subject_type' => $subject->getType(),
                'subject_id' => $subject->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            )));
            $report->save();

            // Increment report count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.reports');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your report has been submitted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get report types
     *
     * @param  $params
     * @return mixed
     */
    public function getReportTypes($params, $return = false)
    {
        self::requireScope('basic');

        $multiOptions = array(
            // '' => '(select)',
            'spam' => 'Spam',
            'abuse' => 'Abuse',
            'inappropriate' => 'Inappropriate Content',
            'licensed' => 'Licensed Material',
            'other' => 'Other',
        );

        if ($return) {
            return $multiOptions;
        }

        $data = array();
        foreach ($multiOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get Search
     *
     * @param  $params
     * @return mixed
     */
    public function getSearch($params)
    {
        self::requireScope('basic');

        $searchApi = Engine_Api::_()->getApi('search', 'core');

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
        if (!$require_check) {
            if (!$this->isViewer()) {
                if (!$this->isViewer()) {
                    self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
                    return false;
                }
            }
        }

        // Check form validity?
        $query = isset($params['keywords']) ? $params['keywords'] : '';
        $type = isset($params['type']) ? $params['type'] : '';
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;

        if (!$query) {
            self::setParamError('keywords', Zend_Registry::get('Zend_Translate')->_('Please enter a search query.'), 400);
            return false;
        }

        $paginator = $searchApi->getPaginator($query, $type);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        $items = array();
        $helperHighlightText = new Engine_View_Helper_HighlightText();
        foreach ($paginator as $item) {
            $item = Engine_Api::_()->getItem($item->type, $item->id);
            if (!$item) {
                continue;
            }
            $items[] = array(
                'id' => $item->getIdentity(),
                'type' => $item->getType(),
                'title' => $helperHighlightText->highlightText($item->getTitle(), $query),
                'description' => $helperHighlightText->highlightText($item->getDescription(), $query),
                'img' => Ynrestapi_Helper_Utils::prepareUrl(Ynrestapi_Helper_ItemPhoto::getInstance()->itemPhoto($item, 'thumb.icon')),
            );
        }

        $data = array(
            'count' => $paginator->getTotalItemCount(),
            'items' => $items,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get available search types
     *
     * @param  $params
     * @return mixed
     */
    public function getSearchTypes($params)
    {
        self::requireScope('basic');

        $data = array();
        $searchApi = Engine_Api::_()->getApi('search', 'core');

        $availableTypes = $searchApi->getAvailableTypes();
        if (is_array($availableTypes) && count($availableTypes) > 0) {
            foreach ($availableTypes as $index => $type) {
                $data[] = array(
                    'id' => $type,
                    'title' => Zend_Registry::get('Zend_Translate')->_(strtoupper('ITEM_TYPE_' . $type)),
                );
            }
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get preview of a link
     *
     * @param  $params
     * @return mixed
     */
    public function getLinkPreview($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('core_link', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (empty($params['uri'])) {
            self::setParamError('uri', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        if (!filter_var($params['uri'], FILTER_VALIDATE_URL)) {
            self::setParamError('uri', Zend_Registry::get('Zend_Translate')->_('Invalid URI'), 400);
            return false;
        }

        // clean URL for html code
        $uri = trim(strip_tags($params['uri']));

        try
        {
            $client = new Zend_Http_Client($uri, array(
                'maxredirects' => 2,
                'timeout' => 10,
            ));

            // Try to mimic the requesting user's UA
            $client->setHeaders(array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'X-Powered-By' => 'Zend Framework',
            ));

            $response = $client->request();

            // Get content-type
            list($contentType) = explode(';', $response->getHeader('content-type'));

            $data = array(
                'url' => $uri,
                'content_type' => $contentType,
                'title' => '',
                'description' => '',
                'thumb' => '',
                'image_count' => 0,
                'images' => array(),
            );

            // Handling based on content-type
            switch (strtolower($contentType)) {

                // Images
                case 'image/gif':
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/tif': // Might not work
                case 'image/xbm':
                case 'image/xpm':
                case 'image/png':
                case 'image/bmp': // Might not work
                    $this->_previewImage($uri, $response, $data);
                    break;

                // HTML
                case '':
                case 'text/html':
                    $this->_previewHtml($uri, $response, $data);
                    break;

                // Plain text
                case 'text/plain':
                    $this->_previewText($uri, $response, $data);
                    break;

                // Unknown
                default:
                    break;
            }

            self::setSuccess(200, $data);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $uri
     * @param Zend_Http_Response $response
     */
    protected function _previewImage($uri, Zend_Http_Response $response, &$data)
    {
        $data['image_count'] = 1;
        $data['images'] = array($uri);
    }

    /**
     * @param $uri
     * @param Zend_Http_Response $response
     */
    protected function _previewText($uri, Zend_Http_Response $response, &$data)
    {
        $body = $response->getBody();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }

        // Reduce whitespace
        $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);

        $data['title'] = substr($body, 0, 63);
        $data['description'] = substr($body, 0, 255);
    }

    /**
     * @param $uri
     * @param Zend_Http_Response $response
     */
    protected function _previewHtml($uri, Zend_Http_Response $response, &$data)
    {
        $body = $response->getBody();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
            $data['charset'] = $charset = trim($matches[1]);
        } else {
            $data['charset'] = $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }

        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null; // Maybe add b/c later
        }

        $title = '';
        if ($dom) {
            $titleList = $dom->query('title');
            if (count($titleList) > 0) {
                $title = trim($titleList->current()->textContent);
                $title = substr($title, 0, 255);
            }
        }
        $data['title'] = $title;

        $description = '';
        if ($dom) {
            $descriptionList = $dom->queryXpath("//meta[@name='description']");
            // Why are they using caps? -_-
            if (count($descriptionList) == 0) {
                $descriptionList = $dom->queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList->current()->getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }
        $data['description'] = $description;

        $thumb = '';
        if ($dom) {
            $thumbList = $dom->queryXpath("//link[@rel='image_src']");
            if (count($thumbList) > 0) {
                $thumb = $thumbList->current()->getAttribute('href');
            }
        }
        $data['thumb'] = $thumb;

        $medium = '';
        if ($dom) {
            $mediumList = $dom->queryXpath("//meta[@name='medium']");
            if (count($mediumList) > 0) {
                $medium = $mediumList->current()->getAttribute('content');
            }
        }
        $data['medium'] = $medium;

        // Get baseUrl and baseHref to parse . paths
        $baseUrlInfo = parse_url($uri);
        $baseUrl = '';
        $baseHostUrl = '';
        $baseUrlScheme = $baseUrlInfo['scheme'];
        $baseUrlHost = $baseUrlInfo['host'];
        if ($dom) {
            $baseUrlList = $dom->query('base');
            if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href')) {
                $baseUrl = $baseUrlList->current()->getAttribute('href');
                $baseUrlInfo = parse_url($baseUrl);
                if (!isset($baseUrlInfo['scheme']) || empty($baseUrlInfo['scheme'])) {
                    $baseUrlInfo['scheme'] = $baseUrlScheme;
                }
                if (!isset($baseUrlInfo['host']) || empty($baseUrlInfo['host'])) {
                    $baseUrlInfo['host'] = $baseUrlHost;
                }
                $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            }
        }
        if (!$baseUrl) {
            $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            if (empty($baseUrlInfo['path'])) {
                $baseUrl = $baseHostUrl;
            } else {
                $baseUrl = explode('/', $baseUrlInfo['path']);
                array_pop($baseUrl);
                $baseUrl = join('/', $baseUrl);
                $baseUrl = trim($baseUrl, '/');
                $baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
            }
        }

        $images = array();
        if ($thumb) {
            $images[] = $thumb;
        }
        if ($dom) {
            $imageQuery = $dom->query('img');
            foreach ($imageQuery as $image) {
                $src = $image->getAttribute('src');
                // Ignore images that don't have a src
                if (!$src || false === ($srcInfo = @parse_url($src))) {
                    continue;
                }
                $ext = ltrim(strrchr($src, '.'), '.');
                // Detect absolute url
                if (strpos($src, '/') === 0) {
                    // If relative to root, add host
                    $src = $baseHostUrl . ltrim($src, '/');
                } else if (strpos($src, './') === 0) {
                    // If relative to current path, add baseUrl
                    $src = $baseUrl . substr($src, 2);
                } else if (!empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // Contians host and scheme, do nothing
                } else if (empty($srcInfo['scheme']) && empty($srcInfo['host'])) {
                    // if not contains scheme or host, add base
                    $src = $baseUrl . ltrim($src, '/');
                } else if (empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // if contains host, but not scheme, add scheme?
                    $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
                } else {
                    // Just add base
                    $src = $baseUrl . ltrim($src, '/');
                }
                // Ignore images that don't come from the same domain
                //if( strpos($src, $srcInfo['host']) === false ) {
                // @todo should we do this? disabled for now
                //continue;
                //}
                // Ignore images that don't end in an image extension
                if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    // @todo should we do this? disabled for now
                    //continue;
                }
                if (!in_array($src, $images)) {
                    $images[] = $src;
                }
            }
        }

        // Unique
        $images = array_values(array_unique($images));

        // Truncate if greater than 20
        if (count($images) > 30) {
            array_splice($images, 30, count($images));
        }

        $data['image_count'] = count($images);
        $data['images'] = $images;
    }
}
