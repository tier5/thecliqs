<?php
if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

class Ynchat_AjaxController extends Core_Controller_Action_Standard {
    public function indexAction() {

    }
    
    public function initLangAndConfigAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $data = Engine_Api::_()->ynchat()->getLangAndConfig();
        $friendList = Engine_Api::_()->ynchat()->getFriendsList($viewer->getIdentity());
        $countOnine = Engine_Api::_()->ynchat()->getTotalOnlineFriends();
        echo json_encode(array(
            'result' => true,
            'iUserId' => $viewer->getIdentity(),
            'lang' => $data['lang'],
            'config' => $data['config'],
            'usersettings' => $data['usersettings'],
            'friendList' => $friendList,
            'countOnine' => $countOnine,
            'error_message' => '',
            'error_code' => 0
        ));
        return;
    }

    public function updateAgentAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }

        $type = 'web';
        $session = new Zend_Session_Namespace('mobile');
        if ($session -> mobile) {
            $type = 'mobile';
        }     
        
        $table = Engine_Api::_()->getDbTable('status', 'ynchat');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $row = $table->fetchRow($select);
        if ($row) {
            $row->agent = $type;
            $row->save();
        }
    }
    
    public function getAdvancedSettingAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $blockList = Engine_Api::_()->ynchat()->getBlockListBySideMoreInfo($viewer->getIdentity(), 'to');
        $aBlockList = array();
        foreach($blockList as $key => $val){
            $user = Engine_Api::_()->user()->getUser($val['user_id']);
            $aBlockList[] = array(
                'id' => $val['user_id'],
                'name' => $val['full_name'],
                'user_id' => $val['user_id'],
                'full_name' => $val['full_name'],
                'user_name' => $val['user_name'],
                'avatar' => Engine_Api::_()->ynchat()->getAvatar($val['user_image']),
            );
        }

        $allowList = Engine_Api::_()->ynchat()->getAllowListBySideMoreInfo($viewer->getIdentity(), 'to');
        $aAllowList = array();
        foreach($allowList as $key => $val){
            $aAllowList[] = array(
                'id' => $val['user_id'],
                'name' => $val['full_name'],
                'user_id' => $val['user_id'],
                'full_name' => $val['full_name'],
                'user_name' => $val['user_name'],
                'avatar' => Engine_Api::_()->ynchat()->getAvatar($val['user_image']),
            );
        }

        echo json_encode(array(
            'result' => true,
            'error_message' => '',
            'aBlockList' => $aBlockList,
            'aAllowList' => $aAllowList,
            'error_code' => 0
        ));
        return;
    }
    
    public function updateUserBoxSettingAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        $iUserId = $this->_getParam('iUserId', 0);
        $sType = $this->_getParam('sType', '');
        $table = Engine_Api::_()->getDbTable('usersettings', 'ynchat');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $row = $table->fetchRow($select);
        switch ($sType) {
            case 'open':
                
                $usersettings = Engine_Api::_()->ynchat()->getUserSettingsByUserId($viewer->getIdentity());
                $aData = $usersettings['aData'];
                if(is_array($aData)){
                    
                    $open = isset($aData['open']) ? $aData['open'] : array();
                    if(isset($open[$iUserId]) == false){
                        $open[$iUserId] = '';
                        $aData['open'] = $open;
                        $row->data =  serialize($aData);
                        $row->save();                      
                    }
                }
                break;
            case 'close':
                $usersettings = Engine_Api::_()->ynchat()->getUserSettingsByUserId($viewer->getIdentity());
                $aData = $usersettings['aData'];
                if(is_array($aData)){
                    $open = isset($aData['open']) ? $aData['open'] : array();
                    if(isset($open[$iUserId]) == true){
                        unset($open[$iUserId]);
                        $aData['open'] = $open;
                        $row->data =  serialize($aData);
                        $row->save();                      
                    }
                }
                break;
            case 'removeall':
                $row->data =  '';
                $row->save();                       
                break;
        }
    }
    
    public function updateStatusGoOnlineAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $iStatus = $this->_getParam('iStatus', 1);
        
        $table = Engine_Api::_()->getDbTable('usersettings', 'ynchat');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $row = $table->fetchRow($select);
        if ($row) {
            $row->is_goonline = $iStatus;
            $row->save();
        }

        // $this->removeCache();
        echo json_encode( array(
            'result' => true,
            'error_message' => '',
            'error_code' => 0
        ));
    }
    
    public function updateStatusPlaySoundAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }

        $iStatus = $this->_getParam('iStatus', 1);
        
        $table = Engine_Api::_()->getDbTable('usersettings', 'ynchat');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        $row = $table->fetchRow($select);
        if ($row) {
            $row->is_notifysound = $iStatus;
            $row->save();
        }
        
        echo json_encode( array(
            'result' => true,
            'error_message' => '',
            'error_code' => 0
        ));
    }
    
    /**
     * Get unread and opening box 
     */ 
    public function getUnreadBoxAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $aUnreadBox = array();
        // process
        // get old messages (compare with iMessageId)
        $listOfUserId = array();
        $aUnreadSenders = Engine_Api::_()->ynchat()->getAllSenderIdOfReceiverId($viewer->getIdentity(), 0);
        foreach ($aUnreadSenders as $key => $value) {
            $listOfUserId[$value['from']] = 1;    // check can send message 
        }
        $usersettings = Engine_Api::_()->ynchat()->getUserSettingsByUserId($viewer->getIdentity());
        $aData = $usersettings['aData'];
        if(is_array($aData)){
            $open = isset($aData['open']) ? $aData['open'] : array();
            if(count($open) > 0){
                foreach ($open as $key => $value) {
                    $listOfUserId[$key] = 0;    // NOT check can send message 
                }
            }
        }
        foreach($listOfUserId as $userid => $check){
            
            if($check && Engine_Api::_()->ynchat()->canSendMessage($userid, $viewer->getIdentity()) == false){
                continue;
            }
            $aUnreadMessages = array();
            if($check){
                // get unread box
                $aUnreadMessages = Engine_Api::_()->ynchat()->getMessageWithSenderIdAndReceiverId($userid, $viewer->getIdentity(), 0);
            } else {
                // get opening box 
                $aUnreadMessages = Engine_Api::_()->ynchat()->getMessageWithSenderIdAndReceiverId($userid, $viewer->getIdentity(), null);
            }
            
            // if(count($aUnreadMessages) > 0){
                // get sender's info
                $aSenderInfo = Engine_Api::_()->ynchat()->__getInfoFriend($userid);
                $aOldMessages = array();
                if($check){ 
                    $aOldMessages = Engine_Api::_()->ynchat()->__getMessagesByFriendId($userid, $aUnreadMessages[0]['message_id'], false, 'ynm.message_id DESC');
                }
                $aMergeMessages = array();
                
                $aMergeMessages = array_merge($aOldMessages, $aUnreadMessages);
                
                $aMessages = array();
                foreach($aMergeMessages as $aItem){
                    $aMessages[] = Engine_Api::_()->ynchat()->__generateMessageData($aItem, 'large');
                }
                
                $aUnreadBox[] = array(
                    'aSenderInfo' => $aSenderInfo,
                    'aMessages' => $aMessages,
                );
            // }
        }
        // end
        echo json_encode(array(
            'aUnreadBox' => $aUnreadBox,
            'error_message' => '',
            'error_code' => 0
        ));
        return;
    }
    
    public function searchFriendAction() {
        // init
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        $results = array();
        $aData = $this->_getAllParams();
        $sSearch = '';
        $type = '';
        if(isset($aData['input'])){
            $type = 'friend_chat';
            $sSearch = trim($aData['input']);
        } else if(isset($aData['q'])){
            $type = 'friend_setting';
            $sSearch = trim($aData['q']);
        }
        if(strlen($sSearch) > 0){
            switch($type){
                case 'friend_chat';
//                    $aRows = $this->getFriendsList(Phpfox::getUserId(), null, $sSearch);
                    $aRows = Engine_Api::_()->ynchat()->getFriendsListNotRestriction($viewer->getIdentity(), null, $sSearch);
                    foreach($aRows as $key => $val){
                        $results[] = array_merge($val, array(
                            'id' => $val['user_id'],
                            'value' => $val['full_name'],
                            'info' => $val['user_name'],
                        ));
                    }
                    echo json_encode(array('results' => $results));
                    return;
                    break;
                case 'friend_setting';
                    $aRows = Engine_Api::_()->ynchat()->getFriendsListNotRestriction($viewer->getIdentity(), null, $sSearch);
                    foreach($aRows as $key => $val){
                        $results[] = array(
                            'id' => $val['user_id'],
                            'name' => $val['full_name'],
                            'user_id' => $val['user_id'],
                            'full_name' => $val['full_name'],
                            'user_name' => $val['user_name'],
                            'avatar' => ($val['avatar']),
                        );
                    }
                    echo json_encode($results);
                    return;
                    break;
            }
        }
    }
    
    public function threadInfoAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $aData = $this->_getAllParams();
        $iFriendId = isset($aData['iFriendId']) ? (int)$aData['iFriendId'] : 0;
        $iReceiver = isset($aData['iReceiver']) ? (int)$aData['iReceiver'] : 0;
        $iMessageId = isset($aData['iMessageId']) ? (int)$aData['iMessageId'] : 0;
        $iNew = isset($aData['iNew']) ? (int)$aData['iNew'] : 0;

        if($iNew){
            // get ONE message
            $aFriend = Engine_Api::_()->ynchat()->__getInfoFriend($iFriendId);
            $aMessage = Engine_Api::_()->ynchat()->__getMessageByMessageId($iMessageId);
            if(isset($aMessage['message_id'])){
                $aMessage = Engine_Api::_()->ynchat()->__generateMessageData($aMessage, 'large');
            }


            echo json_encode(array(
                'aFriend' => $aFriend,
                'aMessage' => $aMessage,
                'iReceiver' => $iReceiver,
                'error_message' => '',
                'error_code' => 0
            ));
            return;
        } else {
            // get old messages (compare with iMessageId)
            $aMoreMessages = array();
            $aRows = Engine_Api::_()->ynchat()->__getMessagesByFriendId($iFriendId, $iMessageId);
            if(count($aRows) > 0){
                foreach($aRows as $aItem){
                    $aMoreMessages[] = Engine_Api::_()->ynchat()->__generateMessageData($aItem, 'large');
                }
            }
            // add separator
            $aMoreMessages = array_reverse($aMoreMessages);
            $aMoreMessages = Engine_Api::_()->ynchat()->addSeparatorMessage($aMoreMessages, $viewer->getIdentity(), $iFriendId);

            echo json_encode (array(
                'aMoreMessages' => $aMoreMessages,
                'iFriendId' => $iFriendId,
                'iReceiver' => $iReceiver,
                'error_message' => '',
                'error_code' => 0
            ));
            return;
        }
    }
    
    public function getOldConversationAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $aData = $this->_getAllParams();
        $iUserId = isset($aData['iUserId']) ? (int)$aData['iUserId'] : 0;
        // yesterday/week/month/quarter
        $sType = isset($aData['sType']) ? $aData['sType'] : 'yesterday';
        $now = Engine_Api::_()->ynchat()->getTimeStamp();
        $iStartTime = 0;
        $iEndTime = $now;
        
        // process
        switch($sType){
            case 'yesterday':
                $yesterday = new DateTime('yesterday');
                $iStartTime = $yesterday->getTimeStamp();
                break;
            case 'week':
                $iStartTime = $now - (7 * 24 * 60 * 60);
                break;
            case 'month':
                $iStartTime = $now - (30 * 24 * 60 * 60);
                break;
            case 'quarter':
                $iStartTime = $now - (90 * 24 * 60 * 60);
                break;
        }

        $aMessages = Engine_Api::_()->ynchat()->__getMessagesByFriendId($iUserId, null, true, null, $iStartTime, $iEndTime);
        $result = array();
        foreach($aMessages as $aItem){
            $result[] = Engine_Api::_()->ynchat()->__generateMessageData($aItem, 'large');
        }

        // end
        echo json_encode( array(
            'result' => true,
            'error_message' => '',
            'aMessages' => $result,
            'iUserId' => $iUserId,
            'sType' => $sType,
            'error_code' => 0
        ));
    }
    
    public function uploadAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        $params = $this->_getAllParams();
        $sender = $viewer->getIdentity();
        $receiver = $params['iReceiverId'];
        if (!$sender || !$receiver) {
            return array(
                'error_message' => $view->translate('Invalid request'),
                'error_code' => 1
            );
        }
        
        $name = $params['fileName'];
        $file_data = $params['fileData'];
        
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';;
        $file_data = file_get_contents($file_data);
        file_put_contents($path.'/'.$name ,$file_data, FILE_APPEND);
        $type = mime_content_type($path.'/'.$name);
        
        //rotate image
        if (preg_match('/image*/', $type)) {
            $image = Engine_Image::factory();
            $image -> open($path.'/'.$name);
            if (function_exists('exif_read_data')) { 
                $exif = exif_read_data($path.'/'.$name);
                $angle = 0;
                if (!empty($exif['Orientation']))
                {
                    switch($exif['Orientation'])
                    {
                        case 8 :
                            $angle = 90;
                            break;
                        case 3 :
                            $angle = 180;
                            break;
                        case 6 :
                            $angle = -90;
                            break;
                    }
                };
                if ($angle != 0) {
                    $image -> rotate($angle);
                }
            }
            $image -> resize(720, 720) -> write($path.'/'.$name);
        }
        $params = array(
            'parent_type' => 'user',
            'parent_id' => $viewer->getIdentity(),
        );
        $storage = Engine_Api::_() -> storage();
        $aMain = $storage -> create($path.'/'.$name, $params);
        $storage_file_id = $aMain -> file_id;
        
        $id = Engine_Api::_()->ynchat()->addFile($sender, $receiver, $name, $type, $storage_file_id);
        echo json_encode( array(
            'error_message' => '',
            'error_code' => 0,
            'id' => $id, 
            'name' => $name, 
            'type' => $type
        ));
        return;        
    }

    public function updateStatusMessageAction(){
        // init
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $aData = $this->_getAllParams();
        $iFriendId = isset($aData['iFriendId']) ? (int)$aData['iFriendId'] : 0;

        // process
        Engine_Api::_()->ynchat()->updateStatusMessageBySenderId($iFriendId, 1);

        // end
        echo json_encode( array(
            'error_message' => '',
            'error_code' => 0
        ));
        return;
    }
    
    public function sendMessageByAjaxAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $aData = $this->_getAllParams();
        $iUserId = isset($aData['iUserId']) ? $aData['iUserId'] : '';
        $text = isset($aData['text']) ? $aData['text'] : '';
        $title = isset($aData['title']) ? $aData['title'] : '';
        $url = isset($aData['url']) ? $aData['url'] : '';
        $imageUrl = isset($aData['imageUrl']) ? $aData['imageUrl'] : '';
        $iframe = isset($aData['iframe']) ? $aData['iframe'] : '';
        $widthIframe = isset($aData['widthIframe']) ? (int)$aData['widthIframe'] : 0;
        $heightIframe = isset($aData['heightIframe']) ? (int)$aData['heightIframe'] : 0;

        $type = 'link';
        if(strlen($iframe) > 0){
            $type = 'video';
        }
        $dataField = array(
            'type' => $type, 
            'iframe' => $iframe, 
            'widthIframe' => $widthIframe, 
            'heightIframe' => $heightIframe, 
            'url' => $url, 
            'imageUrl' => $imageUrl, 
            'title' => ($title), 
        );
        $url = base64_decode($url);
        $imageUrl = base64_decode($imageUrl);

        // process
        $text = Engine_Api::_()->ynchat()->checkBan($text, 'word');
        $text = htmlspecialchars($text);
        $text = nl2br($text);
        $text = Engine_Api::_()->ynchat()->parseEmoticon($text);

        $message = '';
        $message .= $text;
        $aVals = array(
            'from' => $viewer->getIdentity(),
            'to' => (int) $iUserId,
            'message' => $message,
            'read' => 0,
            'direction' => 0,
            'message_type' => $type,
            'data' => json_encode($dataField),
        );
        $aMessage = Engine_Api::_()->ynchat()->addMessage($aVals);

        echo json_encode( array(
            'result' => true,
            'iUserId' => $iUserId,
            'aMessage' => $aMessage,
            'error_message' => '',
            'error_code' => 0
        ));
        return;
    }
    
    public function saveAdvancedSettingAction(){
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Please login in and try again'),
                'error_code' => 1
            ));
            return;
        }
        
        $aData = $this->_getAllParams();

        $sTurn = isset($aData['sTurn']) ? $aData['sTurn'] : '';
        $sBlockList = isset($aData['sBlockList']) ? $aData['sBlockList'] : '';
        $sAllowList = isset($aData['sAllowList']) ? $aData['sAllowList'] : '';

        if(strlen($sTurn) > 0){
            if(strlen(trim($sBlockList)) == 0){
                $aBlockUserId = array();
            } else {
                $aBlockUserId = explode(',', $sBlockList);
            }
            if(strlen(trim($sAllowList)) == 0){
                $aAllowUserId = array();
            } else {
                $aAllowUserId = explode(',', $sAllowList);
            }

            $sTurnonoff = '';
            switch($sTurn){
                case 'turnon':
                    $sTurnonoff = 'onsome';
                    // remove duplicate in block list
                    foreach($aBlockUserId as $key => $val){
                        if(in_array($val, $aAllowUserId)){
                            unset($aBlockUserId[$key]);
                        }
                    }
                    if(count($aAllowUserId) == 0){
                        $sTurnonoff = 'onall';
                    }
                    break;
                case 'turnoff':
                    $sTurnonoff = 'offsome';
                    // remove duplicate in allow list
                    foreach($aAllowUserId as $key => $val){
                        if(in_array($val, $aBlockUserId)){
                            unset($aAllowUserId[$key]);
                        }
                    }
                    if(count($aBlockUserId) == 0){
                        $sTurnonoff = 'onall';
                    }
                    break;
            }

            // remove old settings
            $blockTable = Engine_Api::_()->getDbTable('block', 'ynchat');
            $blockRows = $blockTable->fetchAll($blockTable->select()->where('to_id = ?', $viewer->getIdentity()));
            foreach ($blockRows as $blockRow) {
                $blockRow->delete();
            } 
            $allowTable = Engine_Api::_()->getDbTable('allow', 'ynchat');
            $allowRow = $allowTable->fetchAll($allowTable->select()->where('to_id = ?', $viewer->getIdentity()));
            foreach ($allowRows as $allowRow) {
                $allowRow->delete();
            }
            
            // update
            $settingsTable = Engine_Api::_()->getDbtable('usersettings', 'ynchat');
            $db = $settingsTable->getAdapter();
            $db->beginTransaction();
            
            try {
                $settingsTable->update(array(
                    'turnonoff' => $sTurnonoff,
                ), array(
                    'user_id = ?' => $viewer->getIdentity(),
                ));
          
            
            } catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }
            $db->commit();
            
            // add new setting
            foreach($aBlockUserId as $val){
                $block = $blockTable->createRow();
                $block->from_id = $val;
                $block->to_id = $viewer->getIdentity();
                $block->save();
            }
            foreach($aAllowUserId as $val){
                $allow = $allowTable->createRow();
                $allow->from_id = $val;
                $allow->to_id = $viewer->getIdentity();
                $allow->save();
            }
        }

        echo json_encode( array(
            'result' => true,
            'error_message' => '',
            'sTurnonoff' => $sTurnonoff,
            'error_code' => 0
        ));
        return;
    }

    public function removeOldMessageAction(){
        // init
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        $aData = $this->_getAllParams();
        $iMessageId = isset($aData['iMessageId']) ? (int)$aData['iMessageId'] : 0;
        $message = Engine_Api::_()->getItem('ynchat_message', $iMessageId);
        if (!$message) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('Can not find the message'),
                'error_code' => 1
            ));
            return;
        }
        if ($message->from != $viewer->getIdentity()) {
            echo json_encode(array(
                'result' => FALSE,
                'error_message' => $view->translate('You don\'t have permission to delete this message.'),
                'error_code' => 1
            ));
            return;
        }
        
        $message->message_type = 'deleted';
        $message->save();

        // end
        echo json_encode( array(
            'error_message' => '',
            'error_code' => 0
        ));
        return;
    }
}
