<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Core.php 03.02.12 12:11 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Api_Core extends Core_Api_Abstract
{
  public function getFriends($params = array())
  {
    /**
     * @var $table User_Model_DbTable_Users
     * @var $recipientsTbl Hegift_Model_DbTable_Recipients
     * @var $user User_Model_User
     **/

    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $recipientsTbl = Engine_Api::_()->getDbtable('recipients', 'hegift');

    $select = $user->membership()->getMembersOfSelect();
    $friends = $table->fetchAll($select);

    $ids = array();
    $gift_id = $params['gift_id'];
    foreach( $friends as $friend ) {
      if (!$recipientsTbl->checkGiftForUser($friend->resource_id, $gift_id)) {
        $ids[] = $friend->resource_id;
      }
    }

    if (!count($ids)) {
      $ids = array(0);
    }

    if (!empty($params['user_id'])) {
      if (in_array($params['user_id'], $ids)) {
        $ids = array($params['user_id']);
      } else {
        $ids = array(0);
      }
    }

    $select = $table->select()
      ->where("user_id IN(".join(',', $ids).")")
      ->order('displayname ASC')
    ;

    if( !empty($params['keyword']) ) {
      $select
        ->where('displayname LIKE ?', '%'.$params['keyword'].'%')
      ;
    }

    $paginator = Zend_Paginator::factory($select);
    return $paginator;
  }

  public function setPhoto($photo)
  {
    if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = $file;
    }

    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'gift',
      //'parent_id' => $this->getIdentity()
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;

    if($extension == 'gif') {
      copy($file, $mainPath);
    } else{
      $image = Engine_Image::factory();
      $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
        ->destroy();
    }

    $iMain = $filesTable->createFile($mainPath, $params);

    // Remove temp files
    @unlink($mainPath);

    return $iMain->file_id;
  }

  public function setAudio($audio)
  {
    if( is_array($audio) ) {
      if( !is_uploaded_file($audio['tmp_name']) ) {
        throw new Storage_Model_Exception('Invalid upload or file too large');
      }
      $filename = $audio['name'];
    } else {
      throw new Storage_Model_Exception('Invalid upload or file too large');
    }

    // Check file extension
    if( !preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $filename) ) {
      throw new Storage_Model_Exception('Invalid file type');
    }

    $storage = Engine_Api::_()->getItemTable('storage_file');

    $row = $storage->createRow();
    $row->setFromArray(array(
      'parent_type' => 'gift',
    ));

    $row->store($audio);
    return $row;
  }

  public function setVideo($video)
  {
    $storage = Engine_Api::_()->getItemTable('storage_file');

    $row = $storage->createRow();
    $row->setFromArray(array(
      'parent_type' => 'gift',
    ));

    $row->store($video);

    return $row;
  }

  public function getTask($module = 'hegift')
  {
    /**
     * @var $table Core_Model_DbTable_Tasks
     */
    $table = Engine_Api::_()->getDbTable('tasks', 'core');
    $select = $table->select()
      ->where('module = ?', $module);
    if ($module == 'core') {
      $select->where('plugin = ?', 'Core_Plugin_Task_Jobs');
    }
    return $table->fetchRow($select);
  }

  public function getCategories()
  {
    /**
     * @var $catTbl Hegift_Model_DbTable_Categories
     * @var $giftsTbl Hegift_Model_DbTable_Gifts
     */
    $catTbl = Engine_Api::_()->getDbTable('categories', 'hegift');
    $giftsTbl = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $select = $catTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $catTbl->info('name')), array('c.*', 'count' => 'COUNT(c.category_id)'))
      ->joinLeft(array('g' => $giftsTbl->info('name')), 'c.category_id = g.category_id', array())
      ->where(new Zend_Db_Expr('IF(type = 3, status=1, true)'))
      ->where(new Zend_Db_Expr('IF(type = 3, photo_id <> 0, true)'))
      ->where(new Zend_Db_Expr('IF(type = 2, photo_id <> 0, true)'))
      ->where('enabled = 1')
      ->where('owner_id = 0')
      ->where('amount <> 0 OR ISNULL(amount)')
      ->group('g.category_id')
      ->order('c.title')
    ;

    return $catTbl->fetchAll($select);
  }

  public function is_ani($fileName, $photoId) {
    $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
    $photo = $storageTable->getFile($photoId);
    if( !$photo ) {
      return false;
    }

    $p = file_get_contents($fileName);


    return (bool)preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $p, $matches);
  }
}
