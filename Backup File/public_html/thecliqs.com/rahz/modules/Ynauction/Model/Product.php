<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Product.php
 * @author     Minh Nguyen
 */
class Ynauction_Model_Product extends Core_Model_Item_Abstract
{
    protected $_parent_type = 'user';
    protected $_owner_type = 'user';
    
	public function getHref($params = array())
	{
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynauction_general',
			'reset' => true,
			'action' => 'detail',
			'auction' => $this -> product_id,
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getDescription()
	{
		$tmpBody = strip_tags($this -> description);
		return (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
	}

	public function getDealEndIn()
	{
		$translate = Zend_Registry::get("Zend_Translate");
		$str = "";
		$time = strtotime($this -> end_time) - time();
		$min = floor($time / 60);
		if ($min > 1440)
		{
			$days = floor($min / 1440);
			$str .= $days . $translate->_("d"). " ";
			$min = $min - $days * 1440;
		}
		$h = floor($min / 60);
		$str .= $h . $translate->_("h")." ";
		$min = $min - $h * 60;
		$str .= $min . $translate->_("m"). " ";
		echo $str;
	}

	public function getSlug()
	{
		return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this -> title))), '-');
	}

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

	public function getProducts($where = null, $order = null, $limit = null)
	{
		$table = Engine_Api::_() -> getDbtable('products', 'ynauction');
		$rName = $table -> info('name');
		$select = $table -> select();
		if ($where)
			$select -> where($where);
		if ($order)
			$select -> order($order);
		if ($limit)
			$select -> limit($limit);
		$select -> where('is_delete = 0');
		return $table -> fetchAll($select);
	}

	public function getMostLikeProducts($where = null, $limit = null)
	{
		$table = Engine_Api::_() -> getDbtable('products', 'ynauction');
		$rName = $table -> info('name');
		$table_L = Engine_Api::_() -> getDbtable('likes', 'core');
		$Name_L = $table_L -> info('name');
		$select = $table -> select() -> from($rName);
		$select -> join($Name_L, "$Name_L.resource_id = $rName.product_id AND resource_type LIKE 'ynauction_product'", '') -> group("$rName.product_id") -> order("Count($rName.product_id) DESC") -> where("display_home = ?", '1') -> where("approved = ?", '1');
		if ($limit)
			$select -> limit($limit);
		$select -> where('is_delete = 0');
		return $table -> fetchAll($select);
	}

	public function addPhoto($file_id)
	{
		$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($file_id);
		$album = $this -> getSingletonAlbum();
		$params = array(
			// We can set them now since only one album is allowed
			'collection_id' => $album -> getIdentity(),
			'album_id' => $album -> getIdentity(),
			'product_id' => $this -> getIdentity(),
			'user_id' => $file -> user_id,
			'file_id' => $file_id
		);

		$photo = Engine_Api::_() -> getDbtable('photos', 'ynauction') -> createRow();
		$photo -> setFromArray($params);
		$photo -> save();
		return $photo;
	}

	public function getPhoto($photo_id)
	{
		$photoTable = Engine_Api::_() -> getItemTable('ynauction_photo');
		$select = $photoTable -> select() -> where('file_id = ?', $photo_id) -> limit(1);
		$photo = $photoTable -> fetchRow($select);
		return $photo;
	}

	public function getSingletonAlbum()
	{
		$table = Engine_Api::_() -> getItemTable('ynauction_album');
		$select = $table -> select() -> where('product_id = ?', $this -> getIdentity()) -> order('album_id ASC') -> limit(1);

		$album = $table -> fetchRow($select);

		if (null === $album)
		{
			$album = $table -> createRow();
			$album -> setFromArray(array(
				'title' => $this -> getTitle(),
				'product_id' => $this -> getIdentity()
			));
			$album -> save();
		}

		return $album;
	}

	public function getUserBids()
	{
		$table = Engine_Api::_() -> getDbtable('bids', 'ynauction');
		$rName = $table -> info('name');
		$select = $table -> select() -> distinct() -> from($rName, "ynauction_user_id") -> where("product_id = ?", $this -> getIdentity());
		$users = $table -> fetchAll($select);
		return count($users);
	}

	public function getProposals()
	{
		$table = Engine_Api::_() -> getDbtable('proposals', 'ynauction');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where("product_id = ?", $this -> getIdentity()) -> where("type = 0") -> order("proposal_price DESC");
		$proposals = $table -> fetchAll($select);
		return $proposals;
	}

	public function getProposalsBuyUser($user_id = null)
	{
		$table = Engine_Api::_() -> getDbtable('proposals', 'ynauction');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where("product_id = ?", $this -> getIdentity()) -> where("type = 0") -> where("ynauction_user_id = ?", $user_id) -> order("proposal_price DESC");
		$proposals = $table -> fetchAll($select);
		return $proposals;
	}

	public function getLocation()
	{
		if ($this -> location_id > 0)
		{
			$location = Engine_Api::_() -> getItem("ynauction_location", $this -> location_id);
			if ($location -> parent_id == 1)
				return Engine_Api::_() -> getDbTable('locations', 'ynauction') -> getNode($this -> location_id, false);
			else
				return Engine_Api::_() -> getDbTable('locations', 'ynauction') -> getNode($this -> location_id, false) . ", " . Engine_Api::_() -> getDbTable('locations', 'ynauction') -> getNode($location -> parent_id, false);
		}
	}

	function isViewable()
	{
		return $this -> authorization() -> isAllowed(null, 'view');
	}

	function isEditable()
	{
		return $this -> authorization() -> isAllowed(null, 'edit');
	}

	function isDeleteable()
	{
		return $this -> authorization() -> isAllowed(null, 'delete');
	}

	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Ynauction_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynauction',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(155, 155) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(100, 100) -> write($path . '/in_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;

		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/is_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}

}
