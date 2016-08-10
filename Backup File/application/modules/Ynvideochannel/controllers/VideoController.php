<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_VideoController extends Core_Controller_Action_Standard
{
    protected $_roles;

    public function init()
    {
        $this->_roles = array(
            'owner',
            'parent_member',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );

        // Get subject
        $video = null;
        $id = $this->_getParam('video_id', $this->_getParam('id', null));

        if ($id) {
            $video = Engine_Api::_()->getItem('ynvideochannel_video', $id);
            if ($video) {
                Engine_Api::_()->core()->setSubject($video);
            }
        }

        // Require subject
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }
    }

    public function detailAction()
    {
        $video = Engine_Api::_()->core()->getSubject();
        // Must be able to use videos
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_video', null, 'view')->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
            return;
        }
        // Render
        $this->_helper->content->setEnabled();
        $viewer = Engine_Api::_()->user()->getViewer();
        $this -> view -> viewer = $viewer;
        $this -> view -> video = $video;
        $this -> view -> channel = $video->getChannel();
        $this -> view -> videoTags = $video -> tags() -> getTagMaps();
        if (!$video -> isOwner($viewer))
        {
            $video -> view_count++;
            $video -> save();
        }
        //Get Photo Url
        $photoUrl = $video->getPhotoUrl('thumb.profile');
        $pos = strpos($photoUrl, "http");
        if ($pos === false) {
            $photoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $photoUrl;
        }
        //Get Video Url
        $videoUrl = $video->getHref();
        $pos = strpos($videoUrl, "http");
        if ($pos === false) {
            $videoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $videoUrl;
        }
        //Adding meta tags for sharing
        $view = Zend_Registry::get('Zend_View');
        $og = '<meta property="og:image" content="' . $photoUrl . '" />';
        $og .= '<meta property="og:title" content="' . $video->getTitle() . '" />';
        $og .= '<meta property="og:url" content="' . $videoUrl . '" />';
        $og .= '<meta property="og:updated_time" content="' . $video->creation_date . '" />';
        $og .= '<meta property="og:type" content="video" />';
        $view->layout()->headIncludes .= $og;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }

    public function editAction()
    {
        $video = Engine_Api::_()->core()->getSubject();
        // Render
        $this->_helper->content->setEnabled();
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // Populate category list.
        $categories = Engine_Api::_()->getDbTable('categories', 'ynvideochannel')->getCategories();
        unset($categories[0]);

        //get first category
        $tableCategory = Engine_Api::_()->getItemTable('ynvideochannel_category');
        $firstCategory = $tableCategory->getFirstCategory();
        $category_id = $this->_getParam('category_id', $video->category_id);
        if (!$category_id) {
            $category_id = $firstCategory->category_id;
        }
        //get current category
        $category = Engine_Api::_()->getItem('ynvideochannel_category', $category_id);

        //get profile question
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('ynvideochannel_video');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $formArgs = array('topLevelId' => $profileTypeField->field_id, 'topLevelValue' => $category->option_id);
        }
        $this->view->video = $video;
        $this->view->form = $form = new Ynvideochannel_Form_Video_Edit(array(
            'video' => $video,
            'title' => 'Edit Video',
            'parent_type' => $video->parent_type,
            'parent_id' => $video->parent_id,
            'formArgs' => $formArgs,
        ));

        $categoryElement = $form->getElement('category_id');
        foreach ($categories as $item) {
            $categoryElement->addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
        }

        if ($category_id) {
            $form->category_id->setValue($category_id);
        } else {
            $form->addError('Create video require at least one category. Please contact admin for more details.');
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $submit_button = $this->_getParam('upload');
        if (!isset($submit_button)) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        $db = Engine_Api::_()->getDbtable('videos', 'ynvideochannel')->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            if ($video->category_id != $category_id) {
                $old_category_id = $video->category_id;
                $isEditCategory = true;
            }
            $video->setFromArray($values);
            $video->save();
            // Set photo
            if (!empty($values['photo'])) {
                $video->setPhoto($form->photo);
            }

            //save custom field values of category
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($video);
            $customfieldform->saveValues();

            // remove old custom field
            if ($isEditCategory) {
                $old_category = Engine_Api::_()->getItem('ynvideochannel_category', $old_category_id);
                $tableMaps = Engine_Api::_()->getDbTable('maps', 'ynvideochannel');
                $tableValues = Engine_Api::_()->getDbTable('values', 'ynvideochannel');
                if ($old_category) {
                    $fieldIds = $tableMaps->fetchAll($tableMaps->select()->where('option_id = ?', $old_category->option_id));
                    $arr_ids = array();
                    if (count($fieldIds) > 0) {
                        foreach ($fieldIds as $id) {
                            $arr_ids[] = $id->child_id;
                        }
                        //delete in values table
                        if (count($arr_ids) > 0) {
                            $valueItems = $tableValues->fetchAll($tableValues->select()->where('item_id = ?', $video->getIdentity())->where('field_id IN (?)', $arr_ids));
                            foreach ($valueItems as $item) {
                                $item->delete();
                            }
                        }
                    }
                }
            }
            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            if ($values['auth_view']) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = "everyone";
            }

            $viewMax = array_search($auth_view, $this->_roles);
            foreach ($this->_roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }

            if ($values['auth_comment'])
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $this->_roles);
            foreach ($this->_roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $video->tags()->setTagMaps($viewer, $tags);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($video) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array(
            'action' => 'manage-videos',
        ), 'ynvideochannel_general', true);

    }

    public function deleteAction()
    {
        $video = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid()) {
            return;
        }
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Ynvideochannel_Form_Video_Delete();
        if (!$video) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete.");
            return;
        }
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $video->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-videos'), 'ynvideochannel_general', true),
            'messages' => Array($this->view->message)
        ));
    }

    public function renderPlaylistListAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $video = Engine_Api::_()->core()->getSubject();
        echo $this->view->partial('_add_exist_playlist.tpl', 'ynvideochannel', array('item' => $video));
    }

    public function renderFavoriteLinkAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $video = Engine_Api::_()->core()->getSubject();
        echo $this->view->partial('_add_favorite_link.tpl', 'ynvideochannel', array('video' => $video));
    }

    public function favoriteAction() {
        // Disable layout and view renderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $video = Engine_Api::_()->core()->getSubject();
        $translate = Zend_Registry::get('Zend_Translate');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $favoriteTable = Engine_Api::_() -> getDbTable('favorites', 'ynvideochannel');
        $added = $favoriteTable -> isAdded($video -> video_id, $viewer_id);

        if ($added) {
            if ($favoriteTable->removeVideoFromFavorite($video -> video_id, $viewer_id)) {
                $data = array(
                    'result' => '1',
                    'added' => '0',
                    'message' => $translate->_('This video has been remove from your Favorite Videos list.')
                );
            } else {
                $data = array(
                    'result' => '0',
                    'added' => '0',
                    'message' => ''
                );
            }
        } else {
            $favorite = $favoriteTable->addVideoToFavorite($video, $viewer);
            if ($favorite) {
                $data = array(
                    'result' => 1,
                    'added' => 1,
                    'message' => $translate->_('This video has been added to your Favorite Videos list.'),
                );
            } else {
                $data = array(
                    'result' => 0,
                    'added' => 0,
                    'message' => ''
                );
            }
        }
        return $this->_helper->json($data);
    }

    public function unfavoriteAction() {
        $video = Engine_Api::_()->core()->getSubject();
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->form = $form = new Ynvideochannel_Form_Unfavorite();

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }

        if (Engine_Api::_() -> getDbTable('favorites', 'ynvideochannel')->removeVideoFromFavorite($video->video_id, $viewer_id)) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('The video has been removed from your Favorite videos list.');
        } else {
            $this->view->status = false;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('There is an error occurred, please try again.');
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->view->message)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

    public function embedAction()
    {
        $this -> view -> video = $video = Engine_Api::_()->core()->getSubject();
        // Get embed code
        $this -> view -> embedCode = $video -> getEmbedCode();
    }

    public function externalAction()
    {
        // Get subject
        $this -> view -> video = $video = Engine_Api::_()->core()->getSubject();
        // Get embed code
        $video -> view_count++;
        $video -> save();
        $this -> view -> video = $video;
        $this -> view -> videoEmbedded =  $video -> getVideoIframe();;
        if ($video -> category_id != 0)
        {
            $this -> view -> category = Engine_Api::_() -> ynvideochannel() -> getCategory($video -> category_id);
        }
    }

    public function rateAction()
    {
        $video = Engine_Api::_()->core()->getSubject();
        if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
        {
            return;
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $user_id = $viewer -> getIdentity();
        $rating = (int)$this -> _getParam('rating');
        $db = $video -> getTable() -> getAdapter();
        $table = Engine_Api::_() -> getDbtable('ratings', 'ynvideochannel');
        $db -> beginTransaction();
        try
        {
            $ratingRow = $video -> getRated($user_id);
            if(!$ratingRow) {
                $ratingRow = $table->createRow();
                $ratingRow->user_id = $user_id;
                $ratingRow->video_id = $video->getIdentity();
                $video -> rating = ($video -> rating * $video -> rating_count + $rating)/($video -> rating_count + 1);
                $video -> rating_count ++;
            }
            else
            {
                $video -> rating = ($video -> rating * $video -> rating_count + $rating - $ratingRow -> rating)/($video -> rating_count);
            }
            $ratingRow -> rating = $rating;
            $ratingRow->save();

            $video -> save();
            $db -> commit();
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }
        return $this -> _helper -> json(array('rating' => round($video->rating, 2)));
    }
}
