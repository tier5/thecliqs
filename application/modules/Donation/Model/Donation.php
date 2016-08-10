<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 20.07.12
 * Time: 11:37
 * To change this template use File | Settings | File Templates.
 */
class Donation_Model_Donation extends Core_Model_Item_Abstract
{
  protected $_statusChanged = false;
  public function getHref($params = array())
  {
    $title = Engine_Api::_()->getItem('donation', $this->donation_id)->getUrlTitle();
    if ($this->type == 'fundraise') {
      $params = array_merge(array(
        'route' => 'fundraise_profile',
        'reset' => true,
        'fundraise_id' => $this->donation_id,
        'title' => $title
      ), $params);
    } else {
      $params = array_merge(array(
        'route' => 'donation_profile',
        'reset' => true,
        'donation_id' => $this->donation_id,
        'title' => $title
      ), $params);
    }
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function hasStore()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return false;
    }

    $page = Engine_Api::_()->getDbtable('pages', 'page')->findRow($this->page_id);

    if ($page && $page->getIdentity()) return true;

    return false;
  }

  public function getDonation()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return null;
    }

    $page = Engine_Api::_()->getDbTable('pages', 'page')->findRow($this->page_id);

    return $page;
  }

  public function getPage()
  {
    return $this->getDonation();
  }

  public function getOwner()
  {
    return Engine_Api::_()->getItem('user', $this->owner_id);
  }

  public function getParent()
  {
    return Engine_Api::_()->getItem('donation',$this->parent_id);
  }

  public function getDescription($truncate = false, $strip_tags = true, $nl2br = true, $truncate_count = 200)
  {
    $description = $this->description;

    if ($strip_tags) {
      $description = strip_tags($description);
    }

    if ($truncate) {
      $description = Engine_Api::_()->getApi('core', 'hecore')->truncate($description, $truncate_count);
    }

    if ($nl2br) {
      $description = nl2br($description);
    }

    return $description;
  }

  public function getRaised()
  {
    if (isset ($this->raised_sum)) {
      return $this->raised_sum;
    }
    return 0;
  }

  public function getTargetSum()
  {
    if (isset ($this->target_sum)) {
      return $this->target_sum;
    }
    return 1;
  }

  public function getPayPalEmail()
  {
    $table = Engine_Api::_()->getItemTable('donation_fin_info');

    $donation_id = $this->donation_id;

    if($this->type == 'fundraise'){
      $donation_id = $this->getParent()->donation_id;
    }
    $select = $table->select()
      ->from($table->info('name'),array('pemail'))
      ->where('donation_id = ?', $donation_id)
      ->limit(1);

    return $select->query()->fetchColumn();
  }

  public function getAddress()
  {
    $address = '';
    $b = false;
    if (!$this->isNull($this->street)) {
      $address .= $this->street;
      $b = true;
    }

    if (!$this->isNull($this->city)) {
      if ($b) {
        $address .= ', ';
      }
      $address .= $this->city;
      $b = true;
    }

    if (!$this->isNull($this->state)) {
      if ($b) {
        $address .= ', ';
      }
      $address .= $this->state;
      $b = true;
    }

    if (!$this->isNull($this->country)) {
      if ($b) {
        $address .= ', ';
      }
      $address .= $this->country;
    }
    return $address;
  }

  public function getContacts()
  {
    return $this->phone;
  }

  public function getSingletonAlbum()
  {
    $table = Engine_Api::_()->getItemTable('donation_album');
    $select = $table->select()
      ->where('donation_id = ?', $this->getIdentity())
      ->order('album_id ASC')
      ->limit(1);

    $album = $table->fetchRow($select);

    if (null === $album) {
      $album = $table->createRow();
      $album->setFromArray(array(
        'donation_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }

  public function setPhoto($photo)
  {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      throw new Group_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'donation',
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path . '/m_' . $name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path . '/p_' . $name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path . '/in_' . $name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path . '/is_' . $name)
      ->destroy();

    // Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iProfile = $storage->create($path . '/p_' . $name, $params);
    $iIconNormal = $storage->create($path . '/in_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);
    @unlink($path . '/is_' . $name);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();

    // Add to album
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('donation_photo');
    $groupAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
      'donation_id' => $this->getIdentity(),
      'album_id' => $groupAlbum->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'file_id' => $iMain->getIdentity(),
      'collection_id' => $groupAlbum->getIdentity(),
    ));
    $photoItem->save();

    return $this;
  }

  public function getCategories()
  {

  }

  public function isNull($str = null)
  {
    if (!isset($str)) {
      return true;
    }
    if ($str == null) {
      return true;
    }
    return false;
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getMarker($insert = false)
  {
    $markersTbl = Engine_Api::_()->getDbTable('markers', 'donation');
    $select = $markersTbl->select()
      ->where('donation_id = ?', $this->getIdentity());

    $marker = $markersTbl->fetchRow($select);

    if (!$marker && $insert) {
      $marker = $markersTbl->createRow(array(
        'donation_id' => $this->getIdentity(),
        'latitude' => 0,
        'longitude' => 0
      ));
    }

    return $marker;
  }

  public function addMarker(Donation_Model_Marker $marker)
  {
    $marker->getTable()->delete("donation_id = {$this->donation_id}");

    $marker->donation_id = $this->donation_id;
    $marker->save();
  }

  public function addMarkerByAddress($address)
  {
    $marker = Engine_Api::_()->getApi('gmap', 'donation')->getMarker($address);
    if ($marker) {
      $this->addMarker($marker);
    }
  }

  public function deleteMarker()
  {
    Engine_Api::_()->getApi('gmap', 'donation')->deleteMarker($this);
  }

  public function isAddressChanged($address)
  {
    if (!is_array($address) || empty($address)) {
      return false;
    }

    return (($this->country != $address[0] || $this->state != $address[1] || $this->city != $address[2] || $this->street != $address[3]));
  }

  public function changeStatus(){

    if( $this->status != 'expired' ) {
      $this->status = 'expired';
      $this->_statusChanged = true;
    }

    if ($this->enabled){
      $this->enabled = false;
    }

    if($this->save()){
      if($this->status!= 'active'){
        $this->deleteAction();
      }
    }
  }

  public function getUrlTitle(){
    return str_replace(" ", "_", trim($this->title));
  }

  public function approvedStatus($value)
  {
    if ($value != $this->approved) {
      $this->approved = $value;
      if($this->save()){
        if($value){
          $this->addAction();
        }
        else{
          $this->deleteAction();
        }
      }
    }
    return $this;
  }

  public function getAction()
  {
    $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $name = $attachmentTable->info('name');
    $select = $attachmentTable->select()
      ->setIntegrityCheck(false)
      ->from($name, array('action_id'))
      ->where('type = ?', "donation")
      ->where('id = ?', $this->getIdentity());

    $action_id = (int)$attachmentTable->getAdapter()->fetchOne($select);

    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

    $selectAction = $actionTable->select()->where('action_id = ?', $action_id);

    $action = $actionTable->fetchRow($selectAction);

    return $action;
  }

  public function getActionType()
  {
    if($this->page_id){
      return 'page_'.$this->type.'_new';
    }
    else{
      return 'donation_'.$this->type.'_new';
    }
  }

  public function addAction()
  {
    $actionType = $this->getActionType();
    $subject = $this->getOwner();

    $oldAction = $this->getAction();

    if(!$oldAction && $this->status == 'active' && $this->approved){
      if($this->getPage()){
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($subject, $this->getPage(), $actionType);
      }
      else{
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($subject, $this, $actionType);
      }
      if ($action){
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $this);
      }
    }
  }

  public function deleteAction()
  {
    $action = $this->getAction();
    if($action){
      $action->delete();
    }
  }

  public function getPhotoUrl($type = null)
  {
    if( empty($this->photo_id) ) {
      $view = Zend_Registry::get('Zend_View');
      return $view->layout()->staticBaseUrl . 'application/modules/Donation/externals/images/nophoto_donation_thumb_normal.png';
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function getLink()
  {
    return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
  }

  public function getFundraisers()
  {
    $table = Engine_Api::_()->getItemTable('donation');
    $select = $table->select()->where('parent_id = ?', $this->donation_id);

    return $table->fetchAll($select);
  }

  public function deleteFundraisers()
  {
    $fundraisers = $this->getFundraisers();

    if($fundraisers){
      foreach($fundraisers as $fundraiser)
      {
        $fundraiser->status = 'cancelled';
        $fundraiser->deleteAction();
        $fundraiser->save();
      }
    }
  }

  public function deleteDonation()
  {
    $this->status = 'cancelled';
    $this->deleteAction();
    $this->deleteFundraisers();
    $this->save();
  }
}