<?php
class Ynbusinesspages_Model_Photo extends Core_Model_Item_Collectible
{
	protected $_parent_type = 'ynbusinesspages_album';
	protected $_type = 'ynbusinesspages_photo';
	protected $_owner_type = 'user';

	protected $_collection_type = 'ynbusinesspages_album';

	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'ynbusinesspages_extended',
			'reset' => true,
			'controller' => 'photo',
			'action' => 'view',
			'business_id' => $this -> getCollection() -> getOwner() -> getIdentity(),
			'photo_id' => $this -> getIdentity(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getPhotoUrl($type = null)
	{
		if (empty($this -> file_id))
		{
			return null;
		}

		$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> file_id, $type);
		if (!$file)
		{
			return null;
		}

		return $file -> map();
	}

	public function getBusiness()
	{
		return Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> business_id);
	}

	public function isSearchable()
	{
		$collection = $this -> getCollection();
		if (!$collection instanceof Core_Model_Item_Abstract)
		{
			return false;
		}
		return $collection -> isSearchable();
	}

	public function getAuthorizationItem()
	{
		return $this -> getParent('ynbusinesspages_business');
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
			'parent_type' => $this -> getType(),
			'parent_id' => $this -> getIdentity(),
			'user_id' => $this -> user_id,
			'name' => $fileName,
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
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> file_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function tags()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	protected function _postDelete()
	{
		// This is dangerous, what if something throws an exception in postDelete
		// after the files are deleted?
		try
		{

			if ($this -> file_id)
			{
				$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> file_id);
				if ($file && is_object($file))
				{
					$file -> remove();
				}
			}
			if ($this -> file_id)
			{
				$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> file_id, 'thumb.normal');
				if ($file && is_object($file))
				{
					$file -> remove();
				}
			}

			$album = $this -> getCollection();

			if ((int)$album -> photo_id == (int)$this -> getIdentity())
			{
				$album -> photo_id = $this -> getNextCollectible() -> getIdentity();
				$album -> save();
			}
		}
		catch( Exception $e )
		{
			// @todo completely silencing them probably isn't good enough
			throw $e;
		}
	}

	public function canEdit($user)
	{
		return $this -> getParent() -> getParent() -> authorization() -> isAllowed($user, 'edit') || $this -> isOwner($user);
	}

}
