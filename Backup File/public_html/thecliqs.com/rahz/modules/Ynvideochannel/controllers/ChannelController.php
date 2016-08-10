<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_ChannelController extends Core_Controller_Action_Standard
{
    protected $_roles;
    public function init()
    {
        $this->_roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );

        // Must be able to use channel
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_channel', null, 'view')->isValid()) {
            return;
        }

        // Get subject
        $channel = null;
        $id = $this->_getParam('channel_id', $this->_getParam('id', null));
        if ($id) {
            $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $id);
            if ($channel) {
                Engine_Api::_()->core()->setSubject($channel);
            }
        }

        // Require subject
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }

        // Require auth
        if (!$this->_helper->requireAuth()->setAuthParams($channel, null, 'view')->isValid()) {
            return;
        }
    }

    public function detailAction()
    {
        // Render
        $channel = Engine_Api::_()->core()->getSubject();
        // Render
        $this->_helper->content->setEnabled();
        $viewer = Engine_Api::_()->user()->getViewer();
        $this -> view -> viewer = $viewer;
        $this -> view -> channel = $channel;
        if (!$channel -> isOwner($viewer))
        {
            $channel -> view_count++;
            $channel -> save();
        }
    }

    public function ajaxGetVideosAction()
    {
        $params = $this->_getAllParams();
        $channel = Engine_Api::_()->core()->getSubject();
        $videos = $channel -> getVideos();
        $this -> view -> paginator = $paginator = Zend_Paginator::factory($videos);
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($params['page'], 1);
        $this -> _helper -> layout -> setLayout('default-simple');
    }

    public function editAction()
    {
        $channel = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams($channel, null, 'edit')->isValid()) {
            return;
        }
        // Render
        $this->_helper->content->setEnabled();

        // Populate category list.
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynvideochannel') -> getCategories();
        unset($categories[0]);

        //get first category
        $tableCategory = Engine_Api::_() -> getItemTable('ynvideochannel_category');
        $firstCategory = $tableCategory -> getFirstCategory();
        $category_id = $this -> _getParam('category_id', $channel -> category_id);
        if (!$category_id) {
            $category_id = $firstCategory->category_id;
        }
        $this -> view -> form = $form = new Ynvideochannel_Form_Channel_Edit( array(
            'channel' => $channel,
            'title' => 'Edit Channel',
            'cover' => $channel->getCoverUrl(),
            'thumb' => $channel->getPhotoUrl()
        ));

        $categoryElement = $form -> getElement('category_id');
        foreach ($categories as $item)
        {
            $categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
        }

        if ($category_id) {
            $categoryElement -> setValue($category_id);
        } else {
            $form -> addError('Create video require at least one category. Please contact admin for more details.');
        }

        // Check post/form
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        $post = $this -> getRequest() -> getPost();
        if (!$form -> isValid($post))
        {
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        try
        {
            $values = $form -> getValues();
            $channel -> setFromArray($values);
            $channel -> modified_date = date('Y-m-d H:i:s');
            $channel -> save();
            if (!empty($values['thumbnail']))
            {
                $channel -> setPhoto($form -> thumbnail);
            }

            if (!empty($values['cover_photo']))
            {
                $channel -> setCoverPhoto($form -> cover_photo);
            }

            // stop auto-update
            if(isset($values['stop_auto_update']) && $values['stop_auto_update'] == 1)
            {
                $channel -> auto_update = 0;
            }
            // Auth
            if (empty($values['auth_view']))
            {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment']))
            {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $this -> _roles);
            $commentMax = array_search($values['auth_comment'], $this -> _roles);
            $auth = Engine_Api::_() -> authorization() -> context;
            foreach ($this->_roles as $i => $role)
            {
                $auth -> setAllowed($channel, $role, 'view', ($i <= $viewMax));
                $auth -> setAllowed($channel, $role, 'comment', ($i <= $commentMax));
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($channel) as $action)
            {
                $actionTable -> resetActivityBindings($action);
            }

            // update videos order in channel
            if (!empty($post['order'])) {
                $order = explode(',', $post['order']);
                $channel->updateVideosOrder($order);
            }

            // remove video from channel
            if (!empty($post['deleted'])) {
                $deleted = explode(',', $post['deleted']);
                $channel->video_count -= $channel->deleteVideos($deleted);
            }
            $channel->save();
            $db -> commit();
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }
        // Apply Channel privacy for videos
        $videoIds = $values['videos'];
        if($videoIds)
        {
            foreach($videoIds as $id)
            {
                $video = Engine_Api::_()->getItem('ynvideochannel_video', $id);
                foreach ($this->_roles as $i => $role)
                {
                    $auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
                    $auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
                }
            }
        }

        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => $channel -> getHref(),
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
        ));
    }

    public function deleteAction()
    {
        $channel = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams($channel, null, 'delete')->isValid()) {
            return;
        }
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> view -> form = $form = new Ynvideochannel_Form_Channel_Delete();
        if (!$channel)
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Channel doesn't exists or not authorized to delete.");
            return;
        }
        if (!$this -> getRequest() -> isPost())
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }
        $db = $channel -> getTable() -> getAdapter();
        $db -> beginTransaction();
        try
        {
            $channel->delete();
            $db -> commit();
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Channel has been deleted.');
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage-channels'), 'ynvideochannel_general', true),
            'messages' => array($this -> view -> message)
        ));
    }

    public function autoUpdateAction()
    {
        // Disable layout and view renderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $channel = Engine_Api::_()->core()->getSubject();
        $translate = Zend_Registry::get('Zend_Translate');

        $autoUpdated = $channel->isAutoUpdate();
        $db = $channel -> getTable() -> getAdapter();
        $db -> beginTransaction();
        try{
            if ($autoUpdated) {
                $channel->auto_update = 0;
                $data = array(
                        'result' => '1',
                        'autoUpdated' => '0',
                        'message' => $translate->_('Stopped auto update successfully.')
                         );
            }else {
                $channel->auto_update = 1;
                $data = array(
                        'result' => '1',
                        'autoUpdated' => '1',
                        'message' => $translate->_('Changed to auto update mode successfully.')
                        );
                }
            $channel -> save();
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
        return $this->_helper->json($data);
    }

    public function subscribeAction()
    {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $channel = Engine_Api::_()->core()->getSubject();
        $translate = Zend_Registry::get('Zend_Translate');
        $viewer = Engine_Api::_()->user()->getViewer();
        $subscribed = $channel->isSubscribed($viewer->getIdentity());
        $db = $channel -> getTable() -> getAdapter();
        $db -> beginTransaction();
        $subscribeTable = Engine_Api::_()->getDbTable('subscribes','ynvideochannel');
        try{
            if ($subscribed) {
                $select = $subscribeTable->select() ->where('channel_id = ?', $channel->channel_id)->where('user_id = ?', $viewer->getIdentity());;
                $row = $subscribeTable->fetchRow($select);
                $row->delete();
                $channel->subscriber_count--;
                $data = array(
                    'result' => '1',
                    'subscribed' => '0',
                    'count' => $this ->view -> translate(array("<span>%s</span> subcriber", "<span>%s</span> subcribers", $channel->subscriber_count), $channel->subscriber_count)
                );

            }else {
                $subscribe = $subscribeTable->createRow();
                $subscribe->channel_id = $channel->channel_id;
                $subscribe->user_id  = $viewer->getIdentity();
                $subscribe->save();
                $channel->subscriber_count++;
                $data = array(
                    'result' => '1',
                    'subscribed' => '1',
                    'count' => $this ->view -> translate(array("<span>%s</span> subcriber", "<span>%s</span> subcribers", $channel->subscriber_count), $channel->subscriber_count),
                );
            }

            $channel -> save();
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
        return $this->_helper->json($data);
    }
    public function unsubscribeAction()
    {
        $channel = Engine_Api::_()->core()->getSubject();
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->form = $form = new Ynvideochannel_Form_Unsubscribe();
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $db = $channel -> getTable() -> getAdapter();
        $db -> beginTransaction();
        $subscribeTable = Engine_Api::_()->getDbTable('subscribes','ynvideochannel');
        try{
                $select = $subscribeTable->select() ->where('channel_id = ?', $channel->channel_id)->where('user_id = ?', $viewer->getIdentity());;
                $row = $subscribeTable->fetchRow($select);
                $row->delete();
                $channel->subscriber_count--;
                $channel -> save();
                $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->view->message)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
            'messages' => array($this -> view -> translate('Unsubscribe successfully.'))
        ));

    }
    public function addMoreVideosAction()
    {
        $channel = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_channel', null, 'create')->isValid()) {
            return;
        }
        $channel_id = $channel->channel_id;
        $channel_code = $channel->channel_code;

        $channelVideosUrl = Engine_Api::_() -> ynvideochannel() -> getChannelVideosUrl($channel_code);
        $maxGrabVideos = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.grab.videos', 50);
        $aVideos = Engine_Api::_()->ynvideochannel()->getVideosFromChannelUrl($channelVideosUrl, $maxGrabVideos, null, $channel_id);
        $this->view->form = $form = new Ynvideochannel_Form_Channel_addMoreVideos(array(
            'videos' => $aVideos
        ));

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $post = $this->getRequest()->getPost();
        if (!$form->isValid($post)) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = $form->getValues();
        // Get Auth of channel
        $auth = Engine_Api::_()->authorization()->context;
        $viewMax = 0;
        $commentMax = 0;
        foreach ($this->_roles as $role){
            if($auth->getAllowed($channel, $role , 'view') == 1)
            {
               $viewMax++;
            }
        }
        foreach ($this->_roles as $role){
            if($auth->getAllowed($channel, $role , 'comment') == 1)
            {
                $commentMax++;
            }
        }

        $aVideos = $values['videos'];

        if (!$aVideos) {
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'format' => 'smoothbox',
                'messages' => array('No videos added.')
            ));
            return;
        }
        $vCount = 0;
        foreach ($aVideos as $code) {
            if (Engine_Api::_()->ynvideochannel()->isExistVideoCode($code, $channel->channel_id, null)) {
                continue;
            }
            $videoInformation = Engine_Api::_()->ynvideochannel()->fetchVideoLink($code);
            if ($videoInformation) {
                $videoInformation['code'] = $code;
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
                    //Auth
                    foreach ($this->_roles as $i => $role) {
                        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax - 1));
                    }
                    foreach ($this->_roles as $i => $role) {
                        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax - 1));
                    }
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
        //Set channel feed
        $db->beginTransaction();
        try {
            $owner = $channel->getOwner();
            if($vCount > 0){
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'ynvideochannel_channel_video_new', null, array('count' => $vCount));
            }
            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $channel);
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

        $subscribers = $channel-> getSubscribers();
        if($subscribers)
        {
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            foreach ($subscribers as $subscriber)
            {
                $notifyApi -> addNotification($subscriber, $viewer, $channel, 'ynvideochannel_add_video_to_channel', array('count' => $vCount));
            }
        }

        $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'format' => 'smoothbox',
            'messages' => array(sprintf('Added %s video(s) successfully.', $vCount))
        ));
    }
}
