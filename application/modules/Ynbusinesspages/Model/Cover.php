<?php

class Ynbusinesspages_Model_Cover extends Core_Model_Item_Abstract 
{
	protected $_type = 'ynbusinesspages_cover';
	protected $_searchTriggers = false;
	
	protected function _postDelete()
	{
		$mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id);
		$profilePhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.profile');
		$thumbPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.normal');

		// Delete thumb
		if( $thumbPhoto && $thumbPhoto->getIdentity() ) {
			try {
				$thumbPhoto->delete();
			} catch( Exception $e ) {}
		}

		// Delete profile
		if( $profilePhoto && $profilePhoto->getIdentity() ) {
			try {
				$profilePhoto->delete();
			} catch( Exception $e ) {}
		}
		
		// Delete main
		if( $mainPhoto && $mainPhoto->getIdentity() ) {
			try {
				$mainPhoto->delete();
			} catch( Exception $e ) {}
		}
		parent::_postDelete();
	}
	
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
			$fileName = $file;
		}
		else
		if ($photo instanceof Storage_Model_File)
		{
			$file = $photo -> temporary();
			$fileName = $photo -> name;
		}
		else
		if ($photo instanceof Core_Model_Item_Abstract && !empty($photo -> file_id))
		{
			$tmpRow = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
			$file = $tmpRow -> temporary();
			$fileName = $tmpRow -> name;
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
			$fileName = $photo['name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
			$fileName = $photo;
		}
		else
		{
			throw new Classified_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName)
		{
			$fileName = basename($file);
		}

		$extension = ltrim(strrchr(basename($fileName), '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'ynbusinesspages_cover'
		);

		// Save
		$filesTable = Engine_Api::_() -> getItemTable('storage_file');

		// Resize image (main)
		$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
		$image = Engine_Image::factory();
		$exif = array();
		if(function_exists('exif_read_data'))
		{
			$exif = exif_read_data($file);
		}
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
		}
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(720, 720) -> write($mainPath) -> destroy();

		// Resize image (normal)
		$normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(140, 160) -> write($normalPath) -> destroy();

		// Store
		$iMain = $filesTable -> createFile($mainPath, $params);
		$iIconNormal = $filesTable -> createFile($normalPath, $params);

		$iMain -> bridge($iIconNormal, 'thumb.normal');

		// Remove temp files
		@unlink($mainPath);
		@unlink($normalPath);

		// Update row
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}
}
