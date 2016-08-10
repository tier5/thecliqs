<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_IndexController extends Core_Controller_Action_Standard
{
    public function init()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_channel', null, 'view')->isValid()
            && !$this->_helper->requireAuth()->setAuthParams('ynvideochannel_video', null, 'view')->isValid()
            && !$this->_helper->requireAuth()->setAuthParams('ynvideochannel_playlist', null, 'view')->isValid()
        )
            return;
    }

    public function indexAction()
    {
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function browseVideosAction()
    {
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }

    public function browseChannelsAction()
    {
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }

    public function browsePlaylistsAction()
    {
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }

    public function channelsAction()
    {
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function playlistsAction()
    {
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function manageVideosAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function managePlaylistsAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function manageChannelsAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function favoritesAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function subscriptionsAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        // Render
        $this->_helper->content->setNoRender()->setEnabled();
    }

    /**
     * Channel actions
     */
    public function addChannelAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_channel', null, 'create')->isValid()) {
            return;
        }
        // Render
        $this->_helper->content->setEnabled();
        $this->view->exist = $this->_getParam('exist');
        $this->view->inValid = $this->_getParam('inValid');

    }

    public function getChannelAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_channel', null, 'create')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        // Render
        $this->_helper->content->setEnabled();
        $sUrl = $this->_getParam('channel_url', '');

        $pattern = "/((http|https):\/\/|)(www\.|)youtube\.com\/(channel|user)\/([a-zA-Z0-9-_]{1,})/";
        $aMatch = array();
        preg_match($pattern, $sUrl . '?', $aMatch);
        if (!$aMatch) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'add-channel', 'url' => $sUrl, 'inValid' => true), 'ynvideochannel_general', true);
        }
        $for = $aMatch[4];
        $channelId = $userId = $aMatch[5];
        $channelVideosUrl = '';
        $channelInfoUrl = '';
        $info = array();
        $channelCover = '';
        $channelThumb ='';
        switch ($for) {
            case 'user':
                $channelUserurl = Engine_Api::_()->ynvideochannel()->getChannelUserUrl($userId);
                $data = @file_get_contents($channelUserurl);
                $data = json_decode($data);
                $items = $data->items;
                if (count($items) && !empty($items[0]->id)) {
                    $channelId = $items[0]->id;
                    $channelVideosUrl = Engine_Api::_() -> ynvideochannel() -> getChannelVideosUrl($channelId);
                    $channelInfoUrl = Engine_Api::_() -> ynvideochannel() -> getChannelInfoUrl($channelId);
                }
                break;

            case 'channel':
                $channelVideosUrl = Engine_Api::_() -> ynvideochannel() -> getChannelVideosUrl($channelId);
                $channelInfoUrl = Engine_Api::_() -> ynvideochannel() -> getChannelInfoUrl($channelId);
                break;
        }

        // get channel information
        $data = @file_get_contents($channelInfoUrl);
        $data = json_decode($data);

        $items = $data->items;
        if (count($items)) {
            if (!empty($items[0]->brandingSettings->channel)) {
                $info = $items[0]->brandingSettings->channel;
            }
            if (!empty($items[0]->brandingSettings->image)) {
                $channelCover = $items[0]->brandingSettings->image->bannerTvHighImageUrl;
            }
            if (!empty($items[0]->snippet->thumbnails->high)) {
                $channelThumb = $items[0]->snippet->thumbnails->high->url;
            }
        }

        if (Engine_Api::_()->ynvideochannel()->isExistChannelCode($channelId, $viewer->getIdentity())) {
            // TODO: Show message channel url exists
            return $this->_helper->redirector->gotoRoute(array('action' => 'add-channel', 'exist' => true), 'ynvideochannel_general', true);
        }

        $channelTitle = null;
        $channelDescription = null;
        if ($info) {
            $channelTitle = $info->title;
            $channelDescription = $info->description;
        }

        // get videos form this channel
        $maxGrabVideos = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.grab.videos', 50);
        $aVideos = Engine_Api::_()->ynvideochannel()->getVideosFromChannelUrl($channelVideosUrl, $maxGrabVideos);

        $this->view->form = $form = new Ynvideochannel_Form_Channel_Create(array(
            'title' => 'Add a Channel',
            'url' => $sUrl,
            'cover' => $channelCover,
            'videos' => $aVideos,
            'thumb' => $channelThumb

        ));
        $form->getElement('title')->setValue($channelTitle);
        $form->getElement('description')->setValue($channelDescription);
        //Get category
        $tableCategory = Engine_Api::_()->getItemTable('ynvideochannel_category');
        $firstCategory = $tableCategory->getFirstCategory();
        $category_id = $this->_getParam('category_id', $firstCategory->category_id);
        $categoryElement = $form->getElement('category_id');
        $categories = $tableCategory->getCategories();
        unset($categories[0]);
        foreach ($categories as $item) {
            $categoryElement->addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this->view->translate($item['title']));
        }
        //populate category
        if ($category_id) {
            $form->category_id->setValue($category_id);
        } else {
            $form->addError('Create video require at least one category. Please contact admin for more details.');
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $post = $this->getRequest()->getPost();
        if (!$form->isValid($post)) {
            return;
        }
        // Process
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = $form->getValues();
        $values['owner_type'] = 'user';
        $values['owner_id'] = $viewer->getIdentity();
        $insert_action = false;
        $db = Engine_Api::_()->getDbtable('channels', 'ynvideochannel')->getAdapter();
        $db->beginTransaction();
        try {
            // Create channel
            $table = Engine_Api::_()->getDbtable('channels', 'ynvideochannel');
            $channel = $table->createRow();
            $channel->setFromArray($values);
            $channel->channel_code = $channelId;

            // Thumbnail
            if (!empty($values['thumbnail'])) {
                $channel->setPhoto($form->thumbnail);
            }
            elseif (!empty($channelThumb)) {
                $channel->setPhoto($channelThumb);
            }

            // Cover photo
            if (!empty($values['cover_photo'])) {
                $channel->setCoverPhoto($form->cover_photo);
            } elseif (!empty($channelCover)) {
                $channel->setCoverPhoto($channelCover);
            }

            $channel->save();
            $insert_action = true;

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'registered',
                'everyone'
            );
            if ($values['auth_view']) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = "everyone";
            }

            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($channel, $role, 'view', ($i <= $viewMax));
            }

            if ($values['auth_comment'])
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($channel, $role, 'comment', ($i <= $commentMax));
            }

            if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $channel -> getTitle(), 'ynvideochannel_channel', $channel);
            }

            //Add videos of channel
            $aVideos = $values['videos'];
            $vCount = 0;
            foreach ($aVideos as $code) {
                if (Engine_Api::_()->ynvideochannel()->isExistVideoCode($code, $channel->channel_id, null)) {
                    continue;
                }
                $videoInformation = Engine_Api::_()->ynvideochannel()->fetchVideoLink($code);
                if ($videoInformation) {
                    $videoInformation['code'] = $code;
                    $videoInformation['parent_type'] = 'user';
                    $videoInformation['parent_id'] = $viewer->getIdentity();
                    $videoInformation['owner_type'] = 'user';
                    $videoInformation['owner_id'] = $viewer->getIdentity();
                    $videoInformation['channel_id'] = $channel->channel_id;
                    $videoInformation['category_id'] = $channel->category_id;
                    $db = Engine_Api::_()->getDbtable('videos', 'ynvideochannel')->getAdapter();
                    $db->beginTransaction();
                    try {
                        $table = Engine_Api::_()->getDbtable('videos', 'ynvideochannel');
                        $video = $table->createRow();
                        $video->setFromArray($videoInformation);
                        if (!empty($videoInformation['large-thumbnail'])) {
                            $video->setPhoto($videoInformation['large-thumbnail']);
                        }
                        $video->save();
                        if (isset($values['auth_view'])) {
                            $auth_view = $values['auth_view'];
                        } else {
                            $auth_view = "everyone";
                        }
                        $viewMax = array_search($auth_view, $roles);
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                        }
                        if (isset($values['auth_comment']))
                            $auth_comment = $values['auth_comment'];
                        else
                            $auth_comment = "everyone";
                        $commentMax = array_search($auth_comment, $roles);
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                        }

                        if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                            Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $video -> getTitle(), 'ynvideochannel_video', $video);
                        }

                        $db->commit();
                        $vCount++;
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
            }
            $channel->video_count += $vCount;
            $channel->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //Set channel feed
        $db->beginTransaction();
        try {
            if ($insert_action) {
                $owner = $channel->getOwner();
                if($vCount > 0){
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'ynvideochannel_channel_video_new', null, array('count' => $vCount));
                }
                else{
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'ynvideochannel_channel_new');
                }
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $channel);
                }
            }
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($channel) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array(
            'channel_id' => $channel->getIdentity(),
            'slug' => $channel->getSlug(),
        ), 'ynvideochannel_channel_detail', true);

    }

    public function findChannelAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_channel', null, 'create')->isValid()) {
            return;
        }

        // Render
        $this->_helper->content->setEnabled();
        $viewer = Engine_Api::_()->user()->getViewer();
        $keyword = $this->_getParam('keyword', "");

        if (!empty($keyword)) {
            $sQuery = urlencode($keyword); //Set search query
            $sPageToken = "";

            $iMaxResult = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.channels', 50); //Set max result per page

            if (isset($_REQUEST['next_channels'])) {
                $sPageToken = $_REQUEST['next_channels'];
            } else if (isset($_REQUEST['prev_channels'])) {
                $sPageToken = $_REQUEST['prev_channels'];
            }

            // Generate search channel URL
            $findChannelUrl =
                Engine_Api::_()->ynvideochannel()->getFindChannelUrl($sQuery, $sPageToken, $iMaxResult);

            //Find channels  search channels
            list($aChannels, $sPageTokenPrev, $sPageTokenNext) = Engine_Api::_()->ynvideochannel()->getChannels($findChannelUrl,$viewer->getIdentity());
//            $aChannels = array_reverse($aChannels);

            $this->view->aChannels = $aChannels;
            $this->view->sPageTokenPrev = $sPageTokenPrev;
            $this->view->sPageTokenNext = $sPageTokenNext;
        }
        $this->view->keyword = $keyword;
    }

    /**
     * Video actions
     */
    public function shareVideoAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_video', null, 'create')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        // Render
        $this->_helper->content->setEnabled();
        $values['user_id'] = $viewer->getIdentity();
        //Get max
        $paginator = Engine_Api::_()->getItemTable('ynvideochannel_video')->getVideosPaginator($values);
        $this->view->quota = $quota = (int)Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynvideochannel_video', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();

        //Get category
        $tableCategory = Engine_Api::_()->getItemTable('ynvideochannel_category');
        $firstCategory = $tableCategory->getFirstCategory();
        $category_id = $this->_getParam('category_id', $firstCategory->category_id);

        //get current category
        $category = Engine_Api::_()->getItem('ynvideochannel_category', $category_id);

        //get profile question
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('ynvideochannel_video');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $formArgs = array('topLevelId' => $profileTypeField->field_id, 'topLevelValue' => $category->option_id);
        }

        $parent_type = $this->_getParam('parent_type');
        $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));
        if (Engine_Api::_()->hasItemType($parent_type)) {
            $this->view->item = $item = Engine_Api::_()->getItem($parent_type, $parent_id);
            if (!$this->_helper->requireAuth()->setAuthParams($item, null, 'video')->isValid()) {
                return;
            }
        } else {
            $parent_type = 'user';
            $parent_id = $viewer->getIdentity();
        }

        // Create form
        $this->view->form = $form = new Ynvideochannel_Form_Video_Create(array(
            'title' => 'Share a Video',
            'formArgs' => $formArgs,
            'parent_type' => $parent_type,
            'parent_id' => $parent_id
        ));

        $categoryElement = $form->getElement('category_id');
        $categories = $tableCategory->getCategories();
        unset($categories[0]);
        foreach ($categories as $item) {
            $categoryElement->addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this->view->translate($item['title']));
        }
        //populate category
        if ($category_id) {
            $form->category_id->setValue($category_id);
        } else {
            $form->addError('Create video require at least one category. Please contact admin for more details.');
        }

        $submit_button = $this->_getParam('upload');
        if (!isset($submit_button) && empty($values['code'])) {
            return;
        }
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $post = $this->getRequest()->getPost();
        if (!$form->isValid($post)) {
            return;
        }
        // Process
        $values = $form->getValues();

        if (Engine_Api::_()->ynvideochannel()->isValidVideo($values['code']) == false) {
           $form->addError('This video is invalid. Please choose another one.');
            return;
        }

        $values['parent_type'] = $parent_type;
        $values['parent_id'] = $parent_id;
        $values['owner_type'] = 'user';
        $values['owner_id'] = $viewer->getIdentity();

        $insert_action = false;
        $db = Engine_Api::_()->getDbtable('videos', 'ynvideochannel')->getAdapter();
        $db->beginTransaction();
        try {
            // Create video
            $table = Engine_Api::_()->getDbtable('videos', 'ynvideochannel');
            $video = $table->createRow();
            $video->setFromArray($values);

            if (!empty($values['photo'])) {
                $video->setPhoto($form->photo);
            } elseif (!empty($values['largeThumbnail'])) {
                $video->setPhoto($values['largeThumbnail']);
            }

            $video->save();
            $insert_action = true;
            // Save custom field values of category
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($video);
            $customfieldform->saveValues();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            if ($parent_type == 'user' || empty($parent_type)) {
                $roles = array(
                    'owner',
                    'owner_member',
                    'owner_member_member',
                    'owner_network',
                    'registered',
                    'everyone'
                );
            } else {
                $roles = array(
                    'owner',
                    'parent_member',
                    'registered',
                    'everyone'
                );
            }
            if (isset($values['auth_view'])) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = "everyone";
            }
            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }
            if (isset($values['auth_comment']))
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }

            if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $video -> getTitle(), 'ynvideochannel_video', $video);
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $video->tags()->addTagMaps($viewer, $tags);

            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $db->beginTransaction();
        try {
            if ($insert_action) {
                $owner = $video->getOwner();
                if ($parent_type == 'event') {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $item, 'ynevent_video_create');
                } else {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'ynvideochannel_video_new');
                }
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                }
            }
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
            'video_id' => $video->getIdentity(),
            'slug' => $video->getSlug(),
        ), 'ynvideochannel_video_detail', true);
    }

    public function validationVideoAction()
    {
        $code = $this->_getParam('code', '');
        $title = $description = "";
        $valid = false;
        if ($code) {
            $videoInformation = Engine_Api::_()->ynvideochannel()->fetchVideoLink($code);
            if ($videoInformation) {
                $title = $videoInformation['title'];
                $description = $videoInformation['description'];
                $duration = $videoInformation['duration'];
                $mediumThumbnail = $videoInformation['medium-thumbnail'];
                $largeThumbnail = $videoInformation['large-thumbnail'];
                $valid = true;
            }
        }
        echo Zend_Json::encode(array('valid' => $valid,
            'title' => $title,
            'description' => $description,
            'duration' => $duration,
            'mediumThumbnail' => $mediumThumbnail,
            'largeThumbnail' => $largeThumbnail));
        exit;
    }

    public function getPlaylistFormAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> view -> item = $item = Engine_Api::_() -> core() -> getSubject();
    }

    public function createPlaylistAction()
    {
        // VALIDATING
        if( !$this->_helper->requireUser()->isValid() ) return;
        if( !$this->_helper->requireAuth()->setAuthParams('ynvideochannel_playlist', null, 'create')->isValid()) return;

        // RENDER
        $this->_helper->content->setEnabled();
        // GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        // GET FORM
        $this->view->form = $form = new Ynvideochannel_Form_Playlist_Create();

        // GET CATEGORY
        $tableCategory = Engine_Api::_()->getItemTable('ynvideochannel_category');
        $firstCategory = $tableCategory->getFirstCategory();
        $category_id = $firstCategory->getIdentity();
        $categoryElement = $form->getElement('category_id');
        $categories = $tableCategory->getCategories();
        unset($categories[0]);
        foreach ($categories as $item) {
            $categoryElement->addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this->view->translate($item['title']));
        }
        // populate category
        if ($category_id) {
            $form->category_id->setValue($category_id);
        } else {
            $form->addError('Create playlist require at least one category. Please contact admin for more details.');
        }

        if (!$this->getRequest()->isPost())
        {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost()))
        {
            return;
        }

        // Process saving the new playlist
        $values = $form->getValues();
        $values['owner_id'] = $viewer -> getIdentity();
        $values['owner_type'] = 'user';
        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'ynvideochannel');

        $db = $playlistTable->getAdapter();
        $db->beginTransaction();
        try
        {
            $playlist = $playlistTable->createRow();
            $playlist->setFromArray($values);
            $playlist->save();

            if (!empty($values['photo']))
            {
                try
                {
                    $playlist->setPhoto($form->photo);
                }
                catch (Engine_Image_Adapter_Exception $e)
                {
                }
            }

            // Auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'registered',
                'everyone');
            if (empty($values['auth_view']))
            {
                $values['auth_view'] = 'everyone';
            }
            if (empty($values['auth_comment']))
            {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role)
            {
                $auth->setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
            }

            // SUPPORT YNCREDIT MODULE
            if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $playlist -> getTitle(), 'ynvideochannel_playlist', $playlist);
            }

            //add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $playlist, 'ynvideochannel_playlist_new');
            if($action) {
                $activityApi->attachActivity($action, $playlist);
            }

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return $this -> _helper -> redirector -> gotoRoute(array('action'=>'manage-playlists'), 'ynvideochannel_general', true);
    }

    public function ajaxCreatePlaylistAction()
    {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynvideochannel_playlist', null, 'create') -> isValid())
            return;
        //get viewer
        $viewer = Engine_Api::_() -> user() -> getViewer();
        //get params
        $params = $this ->_getAllParams();
        $params['owner_id'] = $viewer -> getIdentity();
        $params['owner_type'] = 'user';
        $params['view_mode'] = 1;
        $params['title'] = strip_tags($params['title']);
        $video_id = $this ->_getParam('video_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            //create playlists
            $playlistTable = Engine_Api::_() -> getItemTable('ynvideochannel_playlist');
            $playlist = $playlistTable -> createRow();
            $playlist = $playlist -> setFromArray($params);
            $playlist -> video_count = 1;
            $playlist -> save();

            //add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $playlist, 'ynvideochannel_playlist_new');
            if($action) {
                $activityApi->attachActivity($action, $playlist);
            }

            //set auth
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'registered',
                'everyone'
            );
            $auth = Engine_Api::_() -> authorization() -> context;
            if (empty($params['auth_view']))
            {
                $params['auth_view'] = 'everyone';
            }

            if (empty($params['auth_comment']))
            {
                $params['auth_comment'] = 'everyone';
            }
            $viewMax = array_search($params['auth_view'], $roles);
            $commentMax = array_search($params['auth_comment'], $roles);
            foreach ($roles as $i => $role)
            {
                $auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                $auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
            }

            if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $playlist -> getTitle(), 'ynvideochannel_playlist', $playlist);
            }

            //add playlist videos
            $playlistVideoTable = Engine_Api::_() -> getDbTable('playlistvideos', 'ynvideochannel');
            $mapRow = $playlistVideoTable -> createRow();
            $mapRow -> playlist_id = $playlist -> getIdentity();
            $mapRow -> video_id = $video_id;
            $mapRow -> creation_date = date('Y-m-d H:i:s');
            $mapRow -> video_order = 999;
            $mapRow -> save();
            $db -> commit();

            echo Zend_Json::encode(array('json' => 'true'));
            return true;
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    public function addToPlaylistAction()
    {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        //get params
        $params = $this ->_getAllParams();
        $item = Engine_Api::_() -> core() -> getSubject();
        $playlist_id = $params['playlist_id'];
        $checked = $params['checked'];
        $message = '';
        $status = true;
        $playlist = Engine_Api::_()->getItem('ynvideochannel_playlist', $playlist_id);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            $playlistVideoTable = Engine_Api::_() -> getDbTable('playlistvideos', 'ynvideochannel');
            $videoIds = $playlistVideoTable -> getVideoIds($playlist_id);
            //check exist before insert
            if($checked == "true"){
                //if checked means add
                if(!in_array($item -> getIdentity(), $videoIds)) {
                    $mapRow = $playlistVideoTable -> createRow();
                    $mapRow -> playlist_id = $playlist_id;
                    $mapRow -> video_id = $item -> getIdentity();
                    $mapRow -> video_order = 999;
                    $mapRow -> save();
                    $playlist -> video_count = new Zend_Db_Expr('video_count + 1');
                    $playlist -> save();
                    $message .= $this->view->translate('This video has been added to playlist "%s" successfully. ', $playlist->getTitle());
                }
                else {
                    $status = false;
                    $message .= $this->view->translate('This video already has been in playlist %s. ', $playlist->getTitle());
                }
            } else if($checked == "false"){
                //if checked means remove
                $mapRow = $playlistVideoTable -> getMapRow($playlist_id, $item -> getIdentity());
                if($mapRow){
                    $mapRow -> delete();
                    $message .= $this->view->translate('This video has been removed from playlist "%s" successfully. ', $playlist->getTitle());
                }
            }

            $db -> commit();
            $data = Zend_Json::encode(
                array(
                    'status' => $status,
                    'message' => $message
                )
            );

            echo $data;
            return true;
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function sendToFriendsAction() {

        $id = $this->_getParam('id', $this->_getParam('id', null));
        $type = $this->_getParam('type', $this->_getParam('type', null));
        if ($id && $type) {
            switch($type)
            {
                case "video":
                    $video = Engine_Api::_()->getItem('ynvideochannel_video', $id);
                    if ($video) {
                        Engine_Api::_()->core()->setSubject($video);
                    }
                    break;
                case "channel":
                    $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $id);
                    if ($channel) {
                        Engine_Api::_()->core()->setSubject($channel);
                    }
                    break;
            }
        }
        // Require subject
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }
        $item = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> item = $item;
        $this -> view -> type = $type;
        $this -> view -> form = $form = new Ynvideochannel_Form_SendToFriends();
        $form-> setDescription($this->view->translate('Please choose friends to send this %s.',$type));
        // Not posting
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }
        $values = $this -> getRequest() -> getPost();

        if (empty($values['users']))
        {
            $form->addError($this->view->translate('Please choose friends to send this %s.',$type));
            return;
        }
        // Process
        $db =  Engine_Api::_() -> getDbtable('usershareds', 'ynvideochannel') -> getAdapter();;
        $db -> beginTransaction();
        try
        {
            $usersIds = $values['users'];
            if (!empty($usersIds))
            {
                $friendTbl = Engine_Api::_() -> getItemTable('user');
                $select = $friendTbl -> select() -> where('user_id IN (?)', $usersIds) -> order('displayname');
                $friends = $friendTbl -> fetchAll($select);
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                foreach ($friends as $friend)
                {
                    $table = Engine_Api::_()->getDbtable('usershareds', 'ynvideochannel');
                    $usershared = $table->createRow();
                    $usershared -> user_id = $friend -> getIdentity();
                    $usershared -> item_id = $item -> getIdentity();
                    $usershared -> type = $type;
                    $usershared->save();
                    switch($type)
                    {
                        case "video":
                            $notifyApi -> addNotification($friend, $viewer, $item, 'ynvideochannel_send_video_to_friends');
                            break;
                        case "channel":
                            $notifyApi -> addNotification($friend, $viewer, $item, 'ynvideochannel_send_channel_to_friends');
                            break;
                    }
                }
                $db -> commit();
            }
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Send to friends successfully.')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));

    }

    public function ajaxGetFriendsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', $this->_getParam('id', null));
        $type = $this->_getParam('type', $this->_getParam('type', null));
        if ($id && $type) {
            switch($type)
            {
                case "video":
                    $video = Engine_Api::_()->getItem('ynvideochannel_video', $id);
                    if ($video) {
                        Engine_Api::_()->core()->setSubject($video);
                    }
                    break;
                case "channel":
                    $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $id);
                    if ($channel) {
                        Engine_Api::_()->core()->setSubject($channel);
                    }
                    break;
            }
        }
        // Prepare data
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $item = Engine_Api::_()->core()->getSubject();
        // Prepare friends
        $params = $this->_getAllParams();
        $search = $params['search'];
        $friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $usersharedsTable = Engine_Api::_() -> getDbtable('usershareds', 'ynvideochannel');
        $usersharedIds = $usersharedsTable->select() -> from($usersharedsTable, 'user_id')->where('item_id = ?', $item -> getIdentity()) -> where('type = ?', $type) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
        $friendsTable = $friendsTable -> select() -> from($friendsTable, 'user_id')
            -> where('resource_id = ?', $viewer->getIdentity())
            -> where('active = ?', 1);
        if(count($usersharedIds) > 0){
            $friendsTable -> where('user_id NOT IN (?)', $usersharedIds);
        }
        $friendsIds = $friendsTable -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
        if (!empty($friendsIds))
        {
            $friendTbl = Engine_Api::_() -> getItemTable('user');
            if($search)
            {
                $select = $friendTbl -> select() -> where('user_id IN (?)', $friendsIds) ->where('displayname LIKE ?', '%' . $search . '%')-> order('displayname');
            }
            else
            {
                $select = $friendTbl -> select() -> where('user_id IN (?)', $friendsIds)-> order('displayname');
            }
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(10);
            if(isset($params['page'])) $paginator->setCurrentPageNumber($params['page'],1);
        }
        else
        {
            $paginator = Zend_Paginator::factory(array());
        }
        echo $this->view->partial('_ajax_get_friends.tpl',array('paginator' => $paginator));
    }
}
