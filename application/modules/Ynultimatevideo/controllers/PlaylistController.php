<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_PlaylistController extends Core_Controller_Action_Standard
{

    protected $_roles;

    public function init()
    {
        $this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> menus_navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynultimatevideo_main', array(), 'ynultimatevideo_main_playlist');
        $this -> _roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );
    }

    public function indexAction()
    {
        // Render
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }

    public function viewAction()
    {
        $this->_helper->content->setEnabled();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        $viewer = Engine_Api::_() -> user() -> getViewer();

        $subject = null;
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            $id = $this -> _getParam('id');
            if (null !== $id) {
                $subject = $playlist = Engine_Api::_() -> getItem('ynultimatevideo_playlist', $id);
                if ($subject && $subject -> getIdentity()) {
                    Engine_Api::_() -> core() -> setSubject($subject);
                } else {
                    return $this -> _helper -> requireSubject() -> forward();
                }
                // Check authorization to view playlist.
                if (!$subject->isViewable()) {
                    return $this -> _helper -> requireAuth() -> forward();
                }

                $viewerId = $viewer->getIdentity();
                if ($viewerId) {
                    // update this video to history
                    Engine_Api::_()->getDbTable('history', 'ynultimatevideo')->updateItem($viewer, $playlist);
                }

                if (!$subject->isOwner($viewer)) {
                    $subject->view_count++;
                    $subject->save();
                }
            }
        }
        $this -> _helper -> requireSubject('ynultimatevideo_playlist');
    }

    public function manageAction() {
        $this->_helper->content->setEnabled();

        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }

    public function quickCreateAction()
    {
        if (0 !== ($video_id = (int)$this -> getRequest() -> getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id)) && $video instanceof Ynultimatevideo_Model_Video)
        {
            Engine_Api::_() -> core() -> setSubject($video);
        }
        if (!$this -> _helper -> requireSubject('ynultimatevideo_video') -> isValid())
        {
            return;
        }
    }

    public function editAction()
    {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $this->_helper->content->setEnabled();

        $playlist = Engine_Api::_() -> getItem('ynultimatevideo_playlist', $this ->_getParam('playlist_id'));

        if(!$playlist) {
            return $this->_helper->requireSubject()->forward();
        }

        if(!$playlist->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }

        Engine_Api::_() -> core() -> setSubject($playlist);

        // Get navigation
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynultimatevideo_main', array(), 'ynultimatevideo_main_manage');

        // Populate category list.
        $this -> view -> categories = $categories = Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo') -> getCategories(0);

        $this -> view -> form = $form = new Ynultimatevideo_Form_Playlist_Edit( array('playlist' => $playlist));

        $this -> view -> playlist = $playlist;

        $categoryElement = $form -> getElement('category_id');
        foreach ($categories as $item)
        {
            $categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
        }

        if (count($form -> category_id -> getMultiOptions()) < 1)
        {
            $form -> removeElement('category_id');
        }

        $populateArray = $playlist->toArray();
        $form -> populate($populateArray);

        // Check post/form
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        try
        {
            $values = $form -> getValues();
            $post = $this -> getRequest() -> getPost();

            $playlist -> setFromArray($values);
            $playlist -> modified_date = date('Y-m-d H:i:s');
            $playlist -> save();

            if (!empty($values['photo']))
            {
                $playlist -> setPhoto($form -> photo);
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
                $auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                $auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($playlist) as $action)
            {
                $actionTable -> resetActivityBindings($action);
            }

            // update videos order in playlist
            if (!empty($post['order'])) {
                $order = explode(',', $post['order']);
                $playlist->updateVideosOrder($order);
            }

            // remove video from playlist
            if (!empty($post['deleted'])) {
                $deleted = explode(',', $post['deleted']);
                $playlist->deleteVideos($deleted);
            }

            $db -> commit();
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => $playlist -> getHref(),
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
        ));
    }

    public function deleteAction()
    {
        $playlist = Engine_Api::_() -> getItem('ynultimatevideo_playlist', $this -> getRequest() -> getParam('playlist_id'));

        if (!$playlist -> isDeletable())
            return;

        // In smoothbox
        $this -> _helper -> layout -> setLayout('default-simple');

        $this -> view -> form = $form = new Ynultimatevideo_Form_Playlist_Delete();

        if (!$playlist)
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Playlist doesn't exist or not authorized to delete.");
            return;
        }

        if (!$this -> getRequest() -> isPost())
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
            return;
        }

        $db = $playlist -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $playlist -> delete();

            $db -> commit();
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The video playlist has been deleted.');

        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _($this -> view -> message)),
            'layout' => 'default-simple',
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynultimatevideo_playlist', true),
        ));
    }

    public function addAction()
    {
        if (!$this -> _helper -> requireSubject('ynultimatevideo_playlist') -> isValid())
        {
            return;
        }

        $this -> view -> playlist = $playlist = Engine_Api::_() -> core() -> getSubject('ynultimatevideo_playlist');

        $video_id = (int)$this -> _getParam('video_id');
        if ($video_id)
        {
            $video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
        }

        if (isset($video))
        {
            if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
            {
                $data = array(
                    'result' => 0,
                    'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have the authorization to view this video.'),
                );
                return $this -> _helper -> json($data);
            }

            $viewer = Engine_Api::_() -> user() -> getViewer();
            $playlistTbl = Engine_Api::_() -> getDbTable('playlists', 'ynultimatevideo');
            $db = $playlistTbl -> getAdapter();
            $db -> beginTransaction();

            $tablePlaylistAssoc = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
            $row = $tablePlaylistAssoc -> getMapRow($playlist -> getIdentity(), $video -> getIdentity());
            if(isset($row) && !empty($row)) {
                $playlist -> removeVideoFromPlaylist($video);
                $message = $this->view->translate('Video successfully removed from playlist %s.', $playlist->getTitle());
            } else {
                $playlistAssoc = $playlist -> addVideoToPlaylist($video);
                if ($playlistAssoc)
                {
                    $auth = Engine_Api::_() -> authorization() -> context;
                    $auth -> setAllowed($playlistAssoc, 'registered', 'view', true);
                    $auth -> setAllowed($playlistAssoc, 'registered', 'comment', true);
                    $message = $this->view->translate('Video successfully added to playlist %s.', $playlist->getTitle());
                }

                $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
                $action = $actionTable -> addActivity($viewer, $playlist, 'ynultimatevideo_playlist_add_video', '');

                if ($action != null)
                {
                    $actionTable -> attachActivity($action, $video);
                }

                foreach ($actionTable->getActionsByObject($video) as $action)
                {
                    $actionTable -> resetActivityBindings($action);
                }
            }

            $db -> commit();

            $data = array(
                'result' => 1,
                'message' => $message
            );
            return $this -> _helper -> json($data);
        }

        $data = array(
            'result' => 0,
            'message' => Zend_Registry::get('Zend_Translate') -> _('This video doesn\'t exist. Please try another one !!!'),
        );
        return $this -> _helper -> json($data);
    }

    public function removeAction()
    {
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_playlist', null, 'remove') -> isValid())
        {
            return;
        }

        $video_id = (int)$this -> _getParam('video_id');
        $playlist_id = (int)$this -> _getParam('playlist_id');
        if ($video_id)
        {
            $video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
        }
        if ($playlist_id)
        {
            $playlist = Engine_Api::_() -> getItem('ynultimatevideo_playlist', $playlist_id);
        }
        if (!(isset($video) && $video != null))
        {
            return $this -> _helper -> requireSubject() -> forward();
        }
        if (!(isset($playlist) && $playlist != null))
        {
            return $this -> _helper -> requireSubject() -> forward();
        }
        $this -> view -> playlist = $playlist;

        // In smoothbox
        $this -> _helper -> layout -> setLayout('default-simple');

        $this -> view -> form = $form = new Ynultimatevideo_Form_Remove( array(
            'remove_title' => 'Remove video',
            'remove_description' => 'Are you sure you want to remove this video from this playlist?'
        ));

        if (!$video)
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Video doesn't exist.");
            return;
        }

        if (!$this -> getRequest() -> isPost())
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();
        try
        {
            if (Engine_Api::_() -> ynultimatevideo() -> removeVideoFromPlaylist($video -> getIdentity(), $playlist -> getIdentity()))
            {
                $this -> view -> status = true;
                $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The video has been removed from the playlist.');
            }
            else
            {
                $this -> view -> status = false;
                $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('There is an error occured, please try again.');
            }
            $db -> commit();

            return $this -> _forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _($this -> view -> message)),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }
        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }
    }

    public function renderPlaylistListAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $item = Engine_Api::_()->core()->getSubject();

        if (!$item) {
            return $this->_helper->requireSubject()->forward();
        }
        echo $this->view->partial('_add_exist_playlist.tpl', 'ynultimatevideo', array('item' => $item));
    }

    public function getPlaylistFormAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> view -> item = $item = Engine_Api::_() -> core() -> getSubject();
    }

    public function createPlaylistAction()
    {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_playlist', null, 'create') -> isValid())
            return;

        //get viewer
        $viewer = Engine_Api::_() -> user() -> getViewer();

        //get params
        $params = $this ->_getAllParams();
        $params['user_id'] = $viewer -> getIdentity();
        $params['title'] = strip_tags($params['title']);
        $video_id = $this ->_getParam('video_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            //create playlists
            $playlistTable = Engine_Api::_() -> getItemTable('ynultimatevideo_playlist');
            $playlist = $playlistTable -> createRow();
            $playlist = $playlist -> setFromArray($params);
            $playlist -> video_count = 1;
            $playlist -> save();

            //add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($playlist -> getOwner(), $playlist, 'ynultimatevideo_playlist_new');
            if($action) {
                $activityApi->attachActivity($action, $playlist);
            }

            //set auth
            $auth = Engine_Api::_() -> authorization() -> context;
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'registered',
                'everyone',
            );

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
                $user = $playlist -> getOwner();
                if($user -> getIdentity())
                    Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynultimatevideo_playlist', $user);
            }
            //add assoc
            $playlistAssocTable = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
            $mapRow = $playlistAssocTable -> createRow();
            $mapRow -> playlist_id = $playlist -> getIdentity();
            $mapRow -> video_id = $video_id;
            $mapRow -> creation_date = date('Y-m-d H:i:s');
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

        //get viewer
        $viewer = Engine_Api::_() -> user() -> getViewer();

        //get params
        $params = $this ->_getAllParams();
        $item = Engine_Api::_() -> core() -> getSubject();
        $playlist_id = $params['playlist_id'];
        $checked = $params['checked'];
        $message = '';
        $status = true;
        $playlist = Engine_Api::_()->getItem('ynultimatevideo_playlist', $playlist_id);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            $playlistAssocTable = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
            $videoIds = $playlistAssocTable -> getVideoIds($playlist_id);
            $type = $item -> getType();
            //check exist before insert
            if($checked == "true"){
                //if checked means add
                if(!in_array($item -> getIdentity(), $videoIds)) {
                    if ($playlist->canAddVideos()) {
                        $mapRow = $playlistAssocTable -> createRow();
                        $mapRow -> playlist_id = $playlist_id;
                        $mapRow -> video_id = $item -> getIdentity();
                        $mapRow -> save();
                        $playlist -> video_count = new Zend_Db_Expr('video_count + 1');
                        $message .= $this->view->translate('This video has been added to playlist "%s" successfully. ', $playlist->getTitle());
                    }
                    else {
                        $status = false;
                        $message .= $this->view->translate('You can not add this more video to playlist %s. ', $playlist->getTitle());
                    }
                }
                else {
                    $status = false;
                    $message .= $this->view->translate('This video already has been in playlist %s. ', $playlist->getTitle());
                }
            } else if($checked == "false"){
                //if checked means remove
                $mapRow = $playlistAssocTable -> getMapRow($playlist_id, $item -> getIdentity());
                if($mapRow){
                    $mapRow -> delete();

                    // reduce video count
                    if ($playlist -> video_count > 0)
                    {
                        $playlist -> video_count = new Zend_Db_Expr('video_count - 1');
                        $playlist -> save();
                    }
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
}
?>