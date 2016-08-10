<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: User.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Model_User extends User_Model_User
{
    protected $_type = 'user';

    protected $_photo_types = array('cover', 'born');

    public function isPhotoTypeSupported($type)
    {
        return in_array($type, $this->_photo_types);
    }

    public function setTimelinePhoto($photo, $type = 'cover')
    {
        if (!$this->isPhotoTypeSupported($type)) {
            throw new User_Model_Exception('The photo type "' . $type . '" is not supported in setTimelinePhoto');
        }

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setTimelinePhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $this->getType(),
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->getIdentity(),
            'name' => basename($fileName),
        );

        /**
         * Save
         *
         * @var $filesTable Storage_Model_DbTable_Files
         */
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
//      ->resize(850, 315)
            ->write($mainPath)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);

        // Remove temp files
        @unlink($mainPath);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');

        $row_name = $type . '_id';
        $this->$row_name = $iMain->file_id;
        $this->save();
        if ($type == 'cover') {
            /**
             * Save mini cover
             *
             * @var $filesTable Storage_Model_DbTable_Files
             */
            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file)
                ->resize(350, 350)
                ->write($mainPath)
                ->destroy();

            // Resize image (icon)
            $image = Engine_Image::factory();
            $image->open($file);

            // Store
            $iMain = $filesTable->createFile($mainPath, $params);

            // Remove temp files
            @unlink($mainPath);

            // Update row
            $this->modified_date = date('Y-m-d H:i:s');

            $this->mini_cover_id = $iMain->file_id;
            $this->save();
        }


        return $this;
    }

    public function getTimelinePhoto($type = 'cover', $alt = "", $attribs = array())
    {
        $row_name = $type . '_id';
        if (
            !$this->isPhotoTypeSupported($type) ||
            !$this->$row_name
        ) {
            return '';
        }


        /**
         * @var $table Storage_Model_DbTable_Files
         * @var $file Storage_Model_File
         */
        $table = Engine_Api::_()->getDbTable('files', 'storage');
        $file = $table->getFile($this->$row_name);
        $src = $file->map();

        /**
         * @var $table User_Model_DbTable_Settings
         */
        $table = Engine_Api::_()->getDbTable('settings', 'user');
        $position = unserialize($table->getSetting($this, 'timeline-' . $type . '-position'));

        if (!is_array($position) || !array_key_exists('top', $position) || !array_key_exists('left', $position)) {
            $position = array('top' => 0, 'left' => 0);
        }

        $attribs['style'] = 'top:' . $position['top'] . 'px;left:' . $position['left'] . 'px;';

        // User image
        $attribs = array_merge(array('id' => $type . '-photo'), $attribs);

        if ($src) {
            return Zend_Registry::get('Zend_View')->htmlImage($src, $alt, $attribs);
        }

        return '';
    }

    public function hasTimelinePhoto($type = 'cover')
    {
        $row_name = $type . '_id';
        return (boolean)$this->$row_name;
    }


    public function getBirthdate()
    {
        $db = Engine_Db_Table::getDefaultAdapter();

        $sql = "select value from engine4_user_fields_values where field_id = (" .
            "select field_id from engine4_user_fields_meta where type = 'birthdate' AND display=1 LIMIT 1" .
            ") and item_id = " . $this->getIdentity();

        return $db->fetchOne($sql);
    }

    public function getTimelineAlbumPhoto($type = 'cover')
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return null;
        }

        /**
         * @var $table Timeline_Model_DbTable_Settings
         */
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');
        $photo_id = $table->getSetting($this, 'timeline-' . $type . '-photo-id');

        if ($photo_id == null) return null;

        return Engine_Api::_()->getItem('album_photo', $photo_id);
    }
}
