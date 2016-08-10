<?php

/**
 * class Ynrestapi_Api_Music
 */
class Ynrestapi_Api_Music extends Ynrestapi_Api_Base
{
    protected $_roles = array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member'        => 'Friends Only',
        'owner'               => 'Just Me'
    );

    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'music';
        $this->mainItemType = 'music_playlist';
    }

    /**
     * Upload a song
     *
     * @param  $params
     * @return mixed
     */
    public function postComposeUpload($params)
    {
        self::requireScope('music');

        // Check user
        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Check auth
        if (!$this->requireAuthIsValid('music_playlist', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to upload songs.'));
            return false;
        }

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        $playlistTable = Engine_Api::_()->getDbTable('playlists', 'music');

        // Get special playlist
        if (0 >= ($playlist_id = $params['playlist_id']) &&
            false != ($type = $params['type'])) {
            $playlist = $playlistTable->getSpecialPlaylist($viewer, $type);
            Engine_Api::_()->core()->setSubject($playlist);
        }

        // Check subject
        if (!$this->requireSubjectIsValid('music_playlist')) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid playlist'));
            return false;
        }

        $data = array();

        // Get playlist
        $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
        $data['playlist_id'] = $playlist_id = $playlist->getIdentity();

        // check auth
        if (!$this->requireAuthIsValid($playlist, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to edit this playlist'));
            return false;
        }

        // Check file
        if (empty($params['Filename']) || empty($_FILES['Filedata'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('No file'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
        $db->beginTransaction();

        try {

            // Create song
            $file = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
            if (!$file) {
                throw new Music_Model_Exception(Zend_Registry::get('Zend_Translate')->_('Song was not successfully attached'));
            }

            // Add song
            $song = $playlist->addSong($file);
            if (!$song) {
                throw new Music_Model_Exception(Zend_Registry::get('Zend_Translate')->_('Song was not successfully attached'));
            }

            // Response
            $data['song_id'] = $song->getIdentity();
            $data['song_url'] = Ynrestapi_Helper_Utils::prepareUrl($song->getFilePath());
            $data['song_title'] = $song->getTitle();

            $db->commit();

            self::setSuccess(200, $data);
            return true;

        } catch (Music_Model_Exception $e) {
            $db->rollback();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function get($params)
    {
        // REDIRECT TO GET AN EXISTED PLAYLIST
        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        self::requireScope('music');

        if (!$this->requireAuthIsValid('music_playlist', null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $values = array(
            'search' => isset($params['keywords']) ? $params['keywords'] : '',
            'sort' => 'recent'
        );

        // SORT PARAMETER
        if (isset($params['sort'])){
            if (!in_array($params['sort'], array('recent', 'popular'))) {
                self::setParamError('sort');
                return false;
            } else {
                $values['sort'] = $params['sort'];
            }
        }

        // GET PLAYLIST OF A USER
        if (isset($params['user_id'])) {
            $values['user'] = $params['user_id'];
        }

        $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
        $items_count = isset($params['limit']) ? (int) $params['limit'] : Engine_Api::_()->getApi('settings', 'core')->getSetting('video.playlistsperpage', 10);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $fields = $this->_getFields($params, 'listing');

        // EXPORT ARRAY DATA
        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    public function getItem($params)
    {
        self::requireScope('music');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        //GET PLAYLIST
        $playlist = Engine_Api::_()->getItem('music_playlist', $params['id']);

        // CHECK PLAYLIST EXISTENCE
        if (!($playlist instanceof Core_Model_Item_Abstract) || !$playlist->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Playlist not found'));
            return false;
        }

        if (!$this->requireAuthIsValid($playlist, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $fields = $this->_getFields($params, 'detail');
        $data = Ynrestapi_Helper_Meta::exportOne($playlist, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    // GET MY PLAYLIST
    public function getMy($params)
    {
        self::requireScope('music');

        if (!$this->requireAuthIsValid('music_playlist', null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $values = array(
            'search' => isset($params['keywords']) ? $params['keywords'] : '',
            'sort' => 'recent',
            'user' => Engine_Api::_()->user()->getViewer()->getIdentity()
        );

        if (isset($params['keywords'])){
            if (!in_array($params['keywords'], array('recent', 'popular'))) {
                self::setParamError('sort');
                return false;
            } else {
                $values['sort'] = $params['keywords'];
            }
        }

        $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
        $items_count = isset($params['limit']) ? (int) $params['limit'] : Engine_Api::_()->getApi('settings', 'core')->getSetting('video.playlistsperpage', 10);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $fields = $this->_getFields($params, 'listing');

        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    public function postSongs($params)
    {
        // RENAME SONG
        if (isset($params['id'])) {
            return $this->postSongsItem($params);
        }

        self::requireScope('music');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // CHECK PERMISSION, USE PERMISSION TO CREATE PLAYLIST
        if (!$this->requireAuthIsValid('music_playlist', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to upload songs.'));
            return false;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
        $db->beginTransaction();

        try {
            // CREATE FILE ITEM
            $song = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
            $db->commit();

            $data = array(
                'song_id' => $song->getIdentity()
            );

            self::setSuccess(200, $data);
            return true;

        } catch (Music_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function postAddSong($params)
    {
        self::requireScope('music');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $playlist_id = $params['playlist_id'];

        if (empty($playlist_id) || !filter_var($playlist_id, FILTER_VALIDATE_INT)) {
            self::setParamError('playlist_id');
            return false;
        }

        // CHECK PERMISSION, USE PERMISSION TO CREATE PLAYLIST
        if (!$this->requireAuthIsValid('music_playlist', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to upload songs.'));
            return false;
        }

        $playlist = Engine_Api::_()->getItem('music_playlist', $playlist_id);

        if (!$playlist || !$playlist->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Playlist not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($playlist, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to edit this playlist'));
            return false;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
        $db->beginTransaction();

        try {

            // Create song
            $file = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
            if( !$file ) {
                throw new Music_Model_Exception('Song was not successfully attached');
            }

            // Add song
            $song = $playlist->addSong($file);
            if( !$song ) {
                throw new Music_Model_Exception('Song was not successfully attached');
            }

            $db->commit();

            $data = array(
                'song_id' => $song->getIdentity()
            );

            self::setSuccess(200, $data);
            return true;

        } catch( Music_Model_Exception $e ) {
            $db->rollback();

            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch( Exception $e ) {
            $db->rollback();
            throw $e;
        }
    }

    public function postSongsItem($params)
    {
        self::requireScope('music');

        $song = Engine_Api::_()->getItem('music_playlist_song', $params['id']);
        if (!$song || !$song->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Song not found.'));
            return false;
        }

        $playlist = $song->getParent();
        if (!$playlist || !$playlist->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Invalid playlist.'));
            return false;
        }

        if (!$this->requireAuthIsValid($playlist, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to edit this playlist'));
            return false;
        }

        if (!isset($params['title'])) {
            self::setParamError('title', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        if (!empty($params['title'])) {

            // Process
            $db = $song->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                $song->setTitle($params['title']);
                $db->commit();

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        self::setSuccess(200);
        return true;
    }

    public function deleteSongs($params) {
        self::requireScope('music');

        $song = Engine_Api::_()->getItem('music_playlist_song', $params['id']);
        if (!$song || !$song->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Song not found.'));
            return false;
        }

        $playlist = $song->getParent();
        if (!$playlist || !$playlist->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Invalid playlist.'));
            return false;
        }

        if (!$this->requireAuthIsValid($playlist, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to edit this playlist'));
            return false;
        }

        // Get file
        $file = Engine_Api::_()->getItem('storage_file', $song->file_id);
        if( !$file ) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Invalid playlist.'));
            return false;
        }

        $db = $song->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $song->deleteUnused();

            $db->commit();
        } catch( Exception $e ) {
            $db->rollback();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    public function delete($params)
    {
        self::requireScope('music');

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

        $playlist = Engine_Api::_()->getItem('music_playlist', $params['id']);
        if (!$playlist || !$playlist->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Playlist doesn\'t exists or not authorized to delete'));
            return false;
        }

        if (!$this->requireAuthIsValid($playlist, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $playlist->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            foreach( $playlist->getSongs() as $song ) {
                $song->deleteUnused();
            }
            $playlist->delete();
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

    public function post($params)
    {
        if (isset($params['id'])) {
            return $this->postItem($params);
        }

        self::requireScope('music');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('music_playlist', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are not allowed to create playlists.'));
            return false;
        }

        $fieldMaps = array(
            'song_ids' => 'fancyuploadfileids'
        );

        //GET VALUES AND POPULATE FORM
        if (!$values = $this->_getPostValues($params)){
            return false;
        }
        $form = new Music_Form_Create();

        $size = $form->art->getMaxFileSize();
        if ($size > 0) {
            $form->art->setMaxFileSize(0);
            $values['MAX_FILE_SIZE'] = $size;
        }

        if (empty($values['art'])){
            $form->removeElement('art');
        }

        $form->populate($values);

        // Process form
        if (!$form->isValid($values)) {
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
        $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
        $db->beginTransaction();
        try {
            $playlist = $form->saveValues();
            $db->commit();
        } catch( Exception $e ) {
            $db->rollback();
            throw $e;
        }

        $data = array(
            'id' => $playlist->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    public function postItem($params)
    {
        self::requireScope('music');

        $id = $params['id'];

        if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $playlist = Engine_Api::_()->getItem('music_playlist', $id);

        if (!$playlist || !$playlist->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Playlist not found.'));
            return false;
        }

        if (!$this->requireAuthIsValid($playlist, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // POPULATE PLAYLIST
        $form = new Music_Form_Edit();
        $form->populate($playlist);

        $fieldMaps = array(
            'song_ids' => 'fancyuploadfileids'
        );

        //GET VALUES AND POPULATE FORM
        if (!$values = $this->_getPostValues($params, $playlist)){
            return false;
        }

        $size = $form->art->getMaxFileSize();
        if ($size > 0) {
            $form->art->setMaxFileSize(0);
            $values['MAX_FILE_SIZE'] = $size;
        }

        if (empty($values['art'])){
            $form->removeElement('art');
        }

        foreach ($values as $key => $value){
            if ($form->getElement($key)) {
                $form->getElement($key)->setValue($value);
            }
        };

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

        // Process
        $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
        $db->beginTransaction();
        try {
            $form->saveValues();
            $db->commit();
        } catch( Exception $e ) {
            $db->rollback();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    public function getViewOptions($params, $return = false)
    {
        self::requireScope('music');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $availableLabels = $this->_roles;
        $user = Engine_Api::_()->user()->getViewer();

        // Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('music_playlist', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

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

    public function getCommentOptions($params, $return = false)
    {
        self::requireScope('music');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $availableLabels = $this->_roles;
        $user = Engine_Api::_()->user()->getViewer();

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('music_playlist', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

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

    private function _getPostValues($params, $playlist = null)
    {
        // CREATE EMPTY RETURN VALUES
        $values = array();

        // MAP VALUES

        if (isset($params['title'])) {
            $values['title'] = $params['title'];
        }

        if (isset($params['description'])) {
            $values['description'] = $params['description'];
        }

        if (isset($params['search'])) {
            if ('0' !== strval($params['search']) && '1' !== strval($params['search'])) {
                self::setParamError('search');
                return false;
            }
            $values['search'] = $params['search'];
        } elseif (empty($playlist)) {
            $values['search'] = 1;
        }

        if (isset($_FILES['art']) && is_uploaded_file($_FILES['art']['tmp_name'])) {
            $values['art'] = $_FILES['art']['name'];
        }

        // CHECK FOR AUTH VIEW IN AVAILABLE VIEW OPTIONS
        if (isset($params['auth_view'])) {
            if (!array_key_exists($params['auth_view'], $this->getViewOptions(null, true))) {
                self::setParamError('auth_view');
                return false;
            } else {
                $values['auth_view'] = $params['auth_view'];
            }
        } else {
            $values['auth_view'] = 'everyone';
        }
        if (isset($params['auth_comment'])) {
            if (!array_key_exists($params['auth_comment'], $this->getCommentOptions(null, true))) {
                self::setParamError('auth_comment');
                return false;
            } else {
                $values['auth_comment'] = $params['auth_comment'];
            }
        } else {
            $values['auth_comment'] = 'everyone';
        }

        // CHECK FOR SONG IDS EXISTENCE, SONG IDS ARE ACTUALLY STORAGE FILE IDS
        $fileIds = !empty($params['song_ids']) ? explode(',', $params['song_ids']) : array();
        $fileIds = array_unique($fileIds);
        foreach ($fileIds as $fileId) {
            $file = Engine_Api::_()->getItem('storage_file', $fileId);
            // Not a file
            if (!($file instanceof Core_Model_Item_Abstract) || !$file->getIdentity()) {
                self::setParamError('song_ids');
                return false;
            }
            // Not owner
            if ($file->getOwner()->getIdentity() != Engine_Api::_()->user()->getViewer()->getIdentity()) {
                self::setParamError('song_ids');
                return false;
            }
        }

        // GLUE FILE IDS ARRAY BACK TO STRING TO BE PROCESS BY MUSIC CREATE FORM
        $values['fancyuploadfileids'] = count($fileIds) ? implode(' ', $fileIds) : '';

        return $values;
    }
}
