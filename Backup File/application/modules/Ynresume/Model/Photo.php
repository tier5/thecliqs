<?php
class Ynresume_Model_Photo extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_photo';
    protected $_searchTriggers = false;
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getPhotoUrl($type = null) {
        $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
        if( !$photo ) {
            return 'application/modules/Ynresume/externals/images/nophoto_icon.jpg';
        }
        return $photo->map();
    }
}
