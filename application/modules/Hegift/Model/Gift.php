<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Gift.php 03.02.12 16:19 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Model_Gift extends Core_Model_Item_Abstract
{
  protected $_type = 'gift';

  public function getHref()
  {
    if (!$this->isGeneral()) {
      return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(array(), 'hegift_general', true);
    }

    $params = array(
      'route' => 'hegift_general',
      'gift_id' => $this->getIdentity(),
      'reset' => true,
    );
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function isSearchable()
  {
    return $this->isGeneral();
  }

  public function isGeneral()
  {
    if ($this->amount === 0) {
      return false;
    }

    if ($this->photo_id == 0) {
      return false;
    }

    if ($this->type == 3 && $this->status != 1) {
      return false;
    }

    if ($this->enabled != 1) {
      return false;
    }

    return true;
  }

  public function removePhotos()
  {
    if (isset($this->photo_id) && $this->photo_id != 0){
    	$storage = Engine_Api::_()->storage();
	    $file = $storage->get($this->photo_id);
	    if ($file !== null) $file->remove();
	    $file = $storage->get($this->photo_id, 'thumb.normal');
	    if ($file !== null) $file->remove();
	    $file = $storage->get($this->photo_id, 'thumb.icon');
	    if ($file !== null) $file->remove();
    }
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } elseif ( !is_array($photo) && is_numeric($photo)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = $file;
    }

    //$is_ani = Engine_Api::_()->hegift()->is_ani($file, $photo);

    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity()
    );

    // Remove photos
    $this->removePhotos();

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    if( $extension == 'gif' ){
      @copy($file, $mainPath);
    } else{
      $image = Engine_Image::factory();
      $image->open($file)
        ->resize(720, 720)
        ->write($mainPath)
        ->destroy();
    }

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

    if( $extension == 'gif' ){
      @copy($file, $normalPath);
    } else{
      $image = Engine_Image::factory();
      $image->open($file)
        ->resize(80, 90)
        ->write($normalPath)
        ->destroy();
    }

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;

    if( $extension == 'gif' ){
      @copy($file, $squarePath);
    } else{
      $image = Engine_Image::factory();
      $image->open($file);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 48, 48)
        ->write($squarePath)
        ->destroy();
    }

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    $iSquare = $filesTable->createFile($squarePath, $params);

    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);
    @unlink($squarePath);

    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }

  public function setAudio($audio)
  {
    $storage = Engine_Api::_()->getItemTable('storage_file');
    $file = $storage->findRow($audio);

    if( $file ) {
      $file->parent_id = $this->getIdentity();
      $file->user_id   = Engine_Api::_()->user()->getViewer()->getIdentity();
      $this->file_id   = $file->getIdentity();
      $this->save();
      $file->save();
      return $file;
    } else {
      return null;
    }
  }

  public function setVideo($video)
  {
    /**
     * @var $file Storage_Model_File
     */

    $storage = Engine_Api::_()->getItemTable('storage_file');
    $file = $storage->findRow($video);

    if( $file ) {
      $file->parent_id = $this->getIdentity();
      $file->user_id   = Engine_Api::_()->user()->getViewer()->getIdentity();
      $this->file_id   = $file->getIdentity();
      $this->save();
      $file->save();

      if ($this->owner_id === 0) {
        // Add to jobs
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('gift_video_encode', array(
          'gift_id' => $this->getIdentity(),
        ));
      }

      return $file;
    } else {
      return null;
    }
  }

  public function payOff($count = 1)
  {
    /**
     * @var $api Credit_Api_Core
     */

    if ($this->type == 3 && $this->owner_id) { // Problems with temporary pay
      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      $this->credits = $settings->getSetting('hegift.video.credits', 100);
      $this->save();
    }

    $sender = Engine_Api::_()->user()->getViewer();
    $credits = (-1)*$this->credits*$count;

    $api = Engine_Api::_()->credit();
    $api->sendGift($sender, $credits, $count);
  }

  public function temporaryPay($count)
  {
    $credits = (-1)*$this->credits*$count;
    Engine_Api::_()->getItem('credit_balance', $this->owner_id)->temporaryPay($credits);
    $this->credits = $credits;
    $this->save();
  }

  public function updateGift($count)
  {
    if (!$this->owner_id && $this->amount !== null) {
      $this->amount -= $count;
    }

    $this->sent_count += $count;
    $this->save();
  }

  public function sendVideoGift()
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     */
    if (!$this->owner_id) {
      return ;
    }
    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');
    $counter = 0;
    $db = $table->getAdapter();
    $db->beginTransaction();
    $recipients = $table->getPaginator(array('subject_id' => $this->owner_id, 'gift_id' => $this->getIdentity(), 'ipp' => 1000, 'approved' => true));
    $sender = Engine_Api::_()->getItem('user', $this->owner_id);
    // Sending gift
    try {
      foreach($recipients as $recipient) {
        $recipient->approved = 1;
        $recipient->save();
        $counter++;
        //activity feed
        if ($recipient->privacy) {
          $action = Engine_Api::_()->getDbTable('actions', 'activity')->addActivity($sender, Engine_Api::_()->getItem('user', $recipient->object_id), 'sent_gift');
          if( $action ) {
            Engine_Api::_()->getDbTable('actions', 'activity')->attachActivity($action, $this);
          }
        }
        //send notification
        $notificationTable->addNotification(Engine_Api::_()->getItem('user', $recipient->object_id), $sender, Engine_Api::_()->getItem('user', $recipient->object_id), 'send_gift', array(
          'action' => Zend_Registry::get('Zend_View')->url(array('action' => 'manage'), 'hegift_general', true),
          'label' => Zend_Registry::get('Zend_Translate')->_('here')
        ));
      }

      $this->payOff($counter);
      $this->updateGift($counter);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function getCategoryName()
  {
    /**
     * @var $table Hegift_Model_DbTable_Categories
     */
    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $select = $table->select()
      ->where('category_id = ?', $this->category_id);

    $row = $table->fetchRow($select);
    return $row->title;
  }

  public function getTypeName()
  {
    $type = 'photo';
    if ($this->type == 2) {
      $type = 'audio';
    } elseif ($this->type == 3) {
      $type = 'video';
    }
    return $type;
  }

  public function isSent()
  {
    if ($this->sent_count) {
      return true;
    } else {
      return false;
    }
  }

  public function getPhotoUrl($type = null)
  {
    if ($url = parent::getPhotoUrl($type)) {
      return $url;
    }
    if ($type == 'thumb.icon') {
      return 'application/modules/Hegift/externals/images/'.$this->getTypeName().'_gift_icon.png';
    } else {
      return 'application/modules/Hegift/externals/images/'.$this->getTypeName().'_gift_normal.png';
    }
  }

  public function getRemovingDate($format = true)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $expiration_date = strtotime($this->creation_date) + 60*60*24*(int)$settings->getSetting('gift.expiration.date', 2); // sec, min, hour, day
    if ($format) {
      return date("Y-m-d H:i:s", $expiration_date);
    }
    return $expiration_date;
  }

  public function getStatus()
  {
    if ($this->type == 3) {
      $where = '{"gift_id":'.$this->getIdentity().'}';
      $jobsTable = Engine_Api::_()->getDbTable('jobs', 'core');
      $select = $jobsTable->select()
        ->where('data = ?', $where);
      $job = $jobsTable->fetchRow($select);
      if ($job !== null) {
        return true;
      }
    }

    return false;
  }

  public function deleteRecipients()
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     */

    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
    $recipients = $table->getPaginator(array('subject_id' => $this->owner_id, 'gift_id' => $this->getIdentity(), 'ipp' => 1000, 'approved' => true));
    foreach ($recipients as $recipient) {
      $recipient->delete();
    }
  }
}
