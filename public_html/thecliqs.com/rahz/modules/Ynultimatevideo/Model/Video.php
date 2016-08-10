<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Model_Video extends Core_Model_Item_Abstract
{
	protected $_owner_type = 'user';
	protected $_type = 'ynultimatevideo_video';

	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'ynultimatevideo_view',
			'reset' => true,
			'user_id' => $this -> owner_id,
			'video_id' => $this -> video_id,
			'slug' => $this -> getSlug(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getRichContent($view = false, $params = array())
	{
		$session = new Zend_Session_Namespace('mobile');
		$mobile = $session -> mobile;
		$count_video = 0;
		if (isset($session -> count))
			$count_video = ++$session -> count;
		$paramsForCompile = array_merge(array(
			'video_id' => $this -> video_id,
			'code' => $this -> code,
			'view' => $view,
			'mobile' => $mobile,
			'duration' => $this -> duration,
			'count_video' => $count_video
		), $params);
		if ($this -> type == Ynultimatevideo_Plugin_Factory::getUploadedType())
		{
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_() -> ynresponsive1() -> isMobile();
			}
			if (!empty($this -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file1_id);
				}
				if ($storage_file)
				{
					$paramsForCompile['location1'] = $storage_file -> getHref();
					$paramsForCompile['location'] = '';
				}
			}
			else 
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
				if ($storage_file)
				{
					$paramsForCompile['location'] = $storage_file -> getHref();
					$paramsForCompile['location1'] = '';
				}
			}
		}
		else
		if ($this -> type == Ynultimatevideo_Plugin_Factory::getVideoURLType())
		{
			$paramsForCompile['location'] = $this -> code;
		}
        $videoEmbedded = Ynultimatevideo_Plugin_Factory::getPlugin((int)$this -> type) -> compileVideo($paramsForCompile);

		// $view == false means that this rich content is requested from the activity feed
		$zend_View = Zend_Registry::get('Zend_View');
		if ($view == false)
		{
			return $zend_View -> partial('_video_feed.tpl', 'ynultimatevideo', array('item' => $this));
		}

		return $videoEmbedded;
	}

	public function getVideo($view = false, $params = array())
	{
		$session = new Zend_Session_Namespace('mobile');
		$mobile = $session -> mobile;
		$count_video = 0;
		if (isset($session -> count))
			$count_video = ++$session -> count;
		$paramsForCompile = array_merge(array(
			'video_id' => $this -> video_id,
			'code' => $this -> code,
			'view' => $view,
			'mobile' => $mobile,
			'duration' => $this -> duration,
			'count_video' => $count_video
		), $params);
		if ($this -> type == Ynultimatevideo_Plugin_Factory::getUploadedType())
		{
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_() -> ynresponsive1() -> isMobile();
			}
			if (!empty($this -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file1_id);
				}
				if ($storage_file)
				{
					$paramsForCompile['location1'] = $storage_file -> getHref();
					$paramsForCompile['location'] = '';
				}
			}
			else
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
				if ($storage_file)
				{
					$paramsForCompile['location'] = $storage_file -> getHref();
					$paramsForCompile['location1'] = '';
				}
			}
		}
		else
			if ($this -> type == Ynultimatevideo_Plugin_Factory::getVideoURLType())
			{
				$paramsForCompile['location'] = $this -> code;
			}
		$videoEmbedded = Ynultimatevideo_Plugin_Factory::getPlugin((int)$this -> type) -> extractVideo($paramsForCompile);

		return $videoEmbedded;
	}

	public function getEmbedCode(array $options = null)
	{
		$options = array_merge(array(
			'height' => '525',
			'width' => '525',
		), (array)$options);

		$view = Zend_Registry::get('Zend_View');
		$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
			'module' => 'ynultimatevideo',
			'controller' => 'video',
			'action' => 'external',
			'video_id' => $this -> getIdentity(),
		), 'default', true) . '?format=frame';
		return '<iframe ' . 'src="' . $view -> escape($url) . '" ' . 'width="' . sprintf("%d", $options['width']) . '" ' . 'height="' . sprintf("%d", $options['width']) . '" ' . 'style="overflow:hidden;"' . '>' . '</iframe>';
	}

	public function getKeywords($separator = ' ')
	{
		$keywords = array();
		foreach ($this->tags()->getTagMaps() as $tagmap)
		{
			$tag = $tagmap -> getTag();
			$keywords[] = $tag -> getTitle();
		}

		if (null === $separator)
		{
			return $keywords;
		}

		return join($separator, $keywords);
	}

	// Interfaces

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function tags()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	protected function _postInsert()
	{
		$table = Engine_Api::_() -> getDbTable('signatures', 'ynultimatevideo');
		$select = $table -> select() -> where('user_id = ?', $this -> owner_id) -> limit(1);
		$row = $table -> fetchRow($select);

		if (null == $row)
		{
			$row = $table -> createRow();
			$row -> user_id = $this -> owner_id;
			$row -> video_count = 1;
		}
		else
		{
			$row -> video_count = new Zend_Db_Expr('video_count + 1');
		}
		$row -> save();
		parent::_postInsert();
	}

	protected function _delete()
	{
		// remove video from favorite table
		Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// remove video from favorite table
		Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// remove video from rating table
		Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// remove video from watchlater table
		Engine_Api::_() -> getDbTable('watchlaters', 'ynultimatevideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// update video count in signature table
		$signatureTbl = Engine_Api::_() -> getDbTable('signatures', 'ynultimatevideo');
		$signature = $signatureTbl -> fetchRow($signatureTbl -> select() -> where('user_id = ?', $this -> owner_id));
		if ($signature)
		{
			$signature -> video_count = new Zend_Db_Expr('video_count - 1');
		}
		$signature -> save();

		// remove video from playlists
		$playlistAssocTbl = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
		$playlistAssocs = $playlistAssocTbl -> fetchAll($playlistAssocTbl -> select() -> where('video_id = ?', $this -> getIdentity()));
		foreach ($playlistAssocs as $playlistAssoc)
		{
			$playlistAssoc -> delete();
		}

		parent::_delete();
	}

	protected function _postDelete()
	{
		parent::_postDelete();
	}

	public function setPhoto($photo, $cronJob = false)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
			$name = basename($file);
		}
		else if( $photo instanceof Storage_Model_File ) {
	      	$file = $photo->temporary();
	      	$name = $photo->name;
	    }
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
			$name = $photo['name'];
		}
		else
		if (is_string($photo))
		{
			if($cronJob || $this -> type == 7)
			{
				$file = $photo;
				$name = basename($photo);
			}
			else {
				$pathInfo = @pathinfo($photo);
				$parts = explode('?', preg_replace("/#!/", "?", $pathInfo['extension']));
				$ext = $parts[0];
				$photo_parsed = @parse_url($photo);
				if ($ext && $photo_parsed) {
					$file = APPLICATION_PATH . '/temporary/ynultimatevideo_' . md5($photo) . '.' . $ext;
					$ch = curl_init("$photo");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
					$rawdata = curl_exec($ch);
					curl_close($ch);

					$fp = fopen($file, 'w');
					// Write the file
					fwrite($fp, $rawdata);
					// And then close it.
					fclose($fp);
					$name = basename($file);
				} else
					throw new User_Model_Exception('can not get get thumbnail image from youtube video');
			}
		}
		else
		{
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynultimatevideo_video',
			'parent_id' => $this -> getIdentity(),
			'user_id' => $this -> owner_id
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(854, 480) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(640, 360) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(420, 236) -> write($path . '/in_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain->bridge($iIconNormal, 'thumb.normal');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path.'/in_'.$name);
		@unlink($file);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}

	public function setVideo($video)
	{
		if ($video instanceof Zend_Form_Element_File)
		{
			$file = $video -> getFileName();
		}
		else if( $video instanceof Storage_Model_File ) {
	      	$file = $video->temporary();
	      	$name = $video->name;
	    }
		else
		if (is_array($video) && !empty($video['tmp_name']))
		{
			$file = $video['tmp_name'];
		}
		else
		if (is_string($video) && file_exists($video))
		{
			$file = $video;
		}
		else
		{
			throw new User_Model_Exception('invalid argument passed to setVideo');
		}

		$params = array(
			'parent_type' => 'ynultimatevideo_video',
			'parent_id' => $this -> getIdentity(),
			'user_id' => $this -> owner_id
		);
		$storageObject = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
		if(!$storageObject)
		{
			$storage = Engine_Api::_()->getItemTable('storage_file');
	        $storageObject = $storage->createFile($file, $params);
			$this -> file_id = $storageObject -> file_id;
			$this -> save();
			@unlink($file);
		}
		else 
		{
			$storageObject -> setFromArray($params);
        	$storageObject -> store($file);
		}
	}

	public function isAddedToWatchLater() {

	}

	public function getCategory() {
		$category = Engine_Api::_()->getItem('ynultimatevideo_category', $this->category_id);
		if ($category) {
			return $category;
		}
	}

	public function sendEmailToFriends($recipients, $message) {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Check recipients
		if( is_string($recipients) ) {
			$recipients = preg_split("/[\s,]+/", $recipients);
		}
		if( is_array($recipients) ) {
			$recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
		}
		if( !is_array($recipients) || empty($recipients) ) {
			return 0;
		}

		// Check message
		$message = trim($message);
		$sentEmails = 0;
		$photo_url = ($this->getPhotoUrl('thumb.profile')) ? $this->getPhotoUrl('thumb.profile') : 'application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png';
		foreach( $recipients as $recipient ) {
			$mailType = 'ynlistings_email_to_friends';
			$mailParams = array(
				'host' => $_SERVER['HTTP_HOST'],
				'email' => $recipient,
				'date' => time(),
				'sender_email' => $viewer->email,
				'sender_title' => $viewer->getTitle(),
				'sender_link' => $viewer->getHref(),
				'sender_photo' => $viewer->getPhotoUrl('thumb.icon'),
				'message' => $message,
				'object_link' => $this->getHref(),
				'object_title' => $this->title,
				'object_photo' => $photo_url,
				'object_description' => $this->description,
			);

			Engine_Api::_()->getApi('mail', 'core')->sendSystem(
				$recipient,
				$mailType,
				$mailParams
			);
			$sentEmails++;
		}
		return $sentEmails;
	}
}
