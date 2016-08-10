<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation_Model_Photo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       01.08.12
 * @time       12:44
 */
class Donation_Model_Photo extends Core_Model_Item_Collectible
{
  protected $_parent_type = 'donation_album';

  protected $_owner_type = 'user';

  protected $_collection_type = 'donation_album';

  protected $_searchTriggers = false;

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'donation_extended',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'view',
      'donation_id' => $this->getCollection()->getOwner()->getIdentity(),
      'photo_id' => $this->getIdentity(),
    ));
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function setPhoto($photo)
  {
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
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if (!$fileName) {
      $fileName = $file;
    }

    $base = basename($fileName);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->user_id,
      'name' => $fileName,
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path . '/m_' . $base)
      ->destroy();

    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path . '/p_' . $base)
      ->destroy();

    // Resize image (donation_normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(230, 180)
      ->write($path . '/normal_' . $base)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path . '/in_' . $base)
      ->destroy();


    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path . '/is_' . $base)
      ->destroy();

    // Store
    try {
      $iMain = $storage->create($path . '/m_' . $base, $params);
      $iProfile = $storage->create($path . '/p_' . $base, $params);
      $iIconNormal = $storage->create($path . '/in_' . $base, $params);
      $iDonationNormal = $storage->create($path . '/normal_' . $base, $params);
      $iSquare = $storage->create($path . '/is_' . $base, $params);

      $iMain->bridge($iProfile, 'thumb.profile');
      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iMain->bridge($iDonationNormal, 'thumb.donation');
      $iMain->bridge($iSquare, 'thumb.icon');

    } catch (Exception $e) {
      // Remove temp files
      @unlink($iMain);
      @unlink($iProfile);
      @unlink($iIconNormal);
      @unlink($iSquare);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Zend_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($path . '/p_' . $base);
    @unlink($path . '/m_' . $base);
    @unlink($path . '/in_' . $base);
    @unlink($path . '/is_' . $base);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();

    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }

    return $this;
  }

  public function getPhotoUrl($type = null)
  {
    if (empty($this->file_id)) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if (!$file) {
      return null;
    }
    return $file->map();
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getDonation()
  {
    return Engine_Api::_()->getItem('donation', $this->donation_id);
  }

  protected function _postDelete()
  {
    $mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
    $thumbPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');

    // Delete thumb
    if ($thumbPhoto && $thumbPhoto->getIdentity()) {
      try {
        $thumbPhoto->delete();
      } catch (Exception $e) {
      }
    }

    // Delete main
    if ($mainPhoto && $mainPhoto->getIdentity()) {
      try {
        $mainPhoto->delete();
      } catch (Exception $e) {
      }
      // Change product cover if applicable
      try {
        if (!empty($this->collection_id)) {
          $product = $this->getCollection();
          $nextPhoto = $this->getNextCollectible(); // Note: this isn't working quite right because it's deleted first

          if (($product instanceof Core_Model_Item_Collection) &&
            ($nextPhoto instanceof Core_Model_Item_Collectible) &&
            (int)$product->photo_id == (int)$this->getIdentity()
          ) {
            $product->photo_id = $nextPhoto->getIdentity();
            $product->save();
          }
        }
      } catch (Exception $e) {
      }

      parent::_postDelete();
    }
  }

  public function canEdit($user)
  {
    return $this->isOwner($user);
  }

}
