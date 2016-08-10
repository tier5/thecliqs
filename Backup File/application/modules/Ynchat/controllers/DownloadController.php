<?php

class Ynchat_DownloadController extends Core_Controller_Action_Standard {
    public function indexAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $id = $this->_getParam('id');
        if (!$id) {
            $this -> _helper -> requireSubject() -> forward();
        }
        $file = Engine_Api::_()->getItem('ynchat_file', $id);
        if (!$file) {
            $this -> _helper -> requireSubject() -> forward();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $file->user_id && $viewer->getIdentity() != $file->receiver_id) {
            $this -> _helper -> requireAuth() -> forward();
        }
        $file_link = Engine_Api::_()->getItemTable('storage_file')->getFile($file->storage_file_id);
        header("Location: ".$file_link->map());
    }
}
