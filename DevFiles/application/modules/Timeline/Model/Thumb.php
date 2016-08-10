<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 13.10.12
 * Time: 11:17
 * To change this template use File | Settings | File Templates.
 */
class Timeline_Model_Thumb extends Core_Model_Item_Abstract
{
  public function getPhotoUrl($type = null)
  {
    if (!$this->photo_id) {
      return null;
    }
    if (!$file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type)) {
      return null;
    }
    return $file->map();
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'name' => basename($fileName),
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (profile)
    $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($profilePath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file);

    if ($image->width > 115 && $image->height > 76) {
      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 115, 76)
        ->write($squarePath)
        ->destroy();
    } else  {
      $image->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
        ->write($squarePath)
        ->destroy();
    }


    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iProfile = $filesTable->createFile($profilePath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    $iSquare = $filesTable->createFile($squarePath, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($mainPath);
    @unlink($profilePath);
    @unlink($normalPath);
    @unlink($squarePath);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();

    return $this;
  }
}
