<?php

class Suggest_Model_ProfilePhoto extends Core_Model_Item_Abstract
{

  protected $_type = 'suggest_profile_photo';

  protected $_primary = array(
    1 => 'profilephoto_id'
  );

  public function getShortType()
  {
    return 'profilephoto';
  }

  public function getIdentity()
  {
    return $this->profilephoto_id;
  }

  public function getType()
  {
    return 'suggest_profile_photo';
  }

  protected function _delete()
  {
    $this->deletePhoto();
    parent::_delete();
  }

  public function deletePhoto()
  {
    if (isset($this->file_id) && $this->file_id != 0){
    	$storage = Engine_Api::_()->storage();
	    $file = $storage->get($this->file_id);
	    if ($file !== null) $file->remove();
    }
  }

  public function getPhotoUrl()
  {
    if( empty($this->file_id) )
    {
      return null;
    }

    $file = $this->api()->getApi('storage', 'storage')->get($this->file_id);
    if( !$file )
    {
      return null;
    }

    return $file->map();
  }

  public function setPhoto($file)
  {
    if( $file instanceof Storage_Model_File )
    {
      $file_id = $file->getIdentity();
    }
    else
    {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');
      $mainName  = $path.'/m_'.$name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(720, 720)
          ->write($mainName)
          ->destroy();

      // Store photos
      $photo_params = array(
        'parent_id'  => $this->profilephoto_id,
        'parent_type'=> 'suggest_profile_photo',
      );

      try {
        $photoFile = Engine_Api::_()->storage()->create($mainName,  $photo_params);
      } catch (Exception $e) {
        if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
          echo $e->getMessage();
          exit();
        }
      }

      // Remove temp files
      @unlink($mainName);

      $file_id  = $photoFile->file_id;
    }

    $this->file_id = $file_id;
    $this->save();

    return $this;
  }


  public function isSearchable()
  {
    return false;
  }

}