<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Core.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Core extends Ynmobile_Api_Base
{
	const MINUTE = 60;
	const HOUR = 3600;
	const DAY = 86400;
	const WEEK = 604800;
	const MONTH = 2419200;
	// 4 weeks approximation
	const YEAR = 31536000;
	// 365 days approximation
	
	protected $injector;
    
	function __construct(){
	   
	}
    
	public function modelHelper($entry, $options = array()){
	    $type  = $entry->getType();
        
        $defaults = $this->appmeta->getMeta('model', $type);
        
        $define =  $defaults['def'];
        
        
        return new $define($entry, array_merge($options, $defaults));
	}
	
	
	public function getGenderOptions(){
            
        $gender_options = array();
        
        $fieldTable = Engine_Api::_()->fields()->getTable('user', 'meta');
        $optionTable = Engine_Api::_()->fields()->getTable('user', 'options');
        
        $genderField = $fieldTable->fetchRow(
            $fieldTable->select()
            ->where('type=?','gender')
            ->limit(1));
        ;
        
        
        if($genderField){
            $gender_select =  $optionTable->select()
                ->where('field_id=?',$genderField->field_id);
           

           foreach($optionTable->fetchAll($gender_select) as $entry){
               $gender_options[] = array('key'=>$entry->option_id, 'val'=>$entry->label);
           };
        }
        
        return $gender_options;
    }
    
	/**
	* @param int $len OPTIONAL default = 8
	* @return string
	*/
	public function getRandomString($len = 8)
	{
		$seek = '0123456789AWETYUIOPASDFGHJKLZXCVBNMqwertyuioppasdfghjklzxcvbnm';
		$max = strlen($seek) - 1;
		$str = '';
		for ($i = 0; $i < $len; ++$i)
		{
			$str .= substr($seek, mt_rand(0, $max), 1);
		}
		return $str;
	}

	public function calculateDefaultTimestamp($time)
	{
		$now = time();
		$deltaNormal = $time - $now;
		$delta = abs($deltaNormal);
		$isPlus = ($deltaNormal > 0);
		$view = Zend_Registry::get('Zend_View');
		// Prepare data in locale timezone
		$timezone = null;
		if (Zend_Registry::isRegistered('timezone'))
		{
			$timezone = Zend_Registry::get('timezone');
		}
		if (null !== $timezone)
		{
			$prevTimezone = date_default_timezone_get();
			if ($timezone == 'India/Cocos') // Timezone ID 'India/Cocos' is invalid
			$timezone = 'Indian/Cocos';
			date_default_timezone_set($timezone);
		}

		$nowDay = date('d', $now);
		$tsDay = date('d', $time);
		$nowWeek = date('W', $now);
		$tsWeek = date('W', $time);
		$tsDayOfWeek = date('D', $time);

		if (null !== $timezone)
		{
			if ($prevTimezone == 'India/Cocos')  // Timezone ID 'India/Cocos' is invalid
			$prevTimezone = 'Indian/Cocos';
			date_default_timezone_set($prevTimezone);
		}

		// Right now
		if ($delta < 1)
		{
			$val = null;
			if ($isPlus)
			{
				$key = 'now';
			}
			else
			{
				$key = 'now';
			}
		}

		// less than a minute
		else
		if ($delta < 60)
		{
			$val = null;
			if ($isPlus)
			{
				$key = 'in a few seconds';
			}
			else
			{
				$key = 'a few seconds ago';
			}
		}

		// less than an hour ago
		else
		if ($delta < self::HOUR)
		{
			$val = floor($delta / 60);
			if ($isPlus)
			{
				$key = array(
					'in %s minute',
					'in %s minutes',
				$val
				);
			}
			else
			{
				$key = array(
					'%s minute ago',
					'%s minutes ago',
				$val
				);
			}
		}

		// less than 12 hours ago, or less than a day ago and same day
		else
		if ($delta < self::HOUR * 12 || ($delta < self::DAY && $tsDay == $nowDay))
		{
			$val = floor($delta / (60 * 60));
			if ($isPlus)
			{
				$key = array(
					'in %s hour',
					'in %s hours',
				$val
				);
			}
			else
			{
				$key = array(
					'%s hour ago',
					'%s hours ago',
				$val
				);
			}
		}

		// less than a week ago and same week
		else
		if ($delta < self::WEEK && $tsWeek == $nowWeek)
		{
			// Get day of week
			$dayOfWeek = Zend_Locale_Data::getContent(Zend_Registry::get('Locale'), 'day', array(
				'gregorian',
				'format',
				'abbreviated',
			strtolower($tsDayOfWeek)
			));

			return $view -> translate('%s at %s', $dayOfWeek, $view -> locale() -> toTime($time, array('size' => 'short')));
		}

		// less than a year and same year
		else
		if ($delta < self::YEAR && date('Y', $time) == date('Y', $now))
		{
			return $view -> locale() -> toTime($time, array(
				'type' => 'dateitem',
				'size' => 'MMMMd'
				));
		}

		// Otherwise use the full date
		else
		{
			return $view -> locale() -> toDate($time, array('size' => 'long'));
		}

		return $view -> translate($key, $val);
	}

	/*
	 * get My Latest Photo
	 *
	 */
	public function getMyLatestPhoto($iUser)
	{
		if (!Engine_Api::_() -> hasModuleBootstrap("album") && !Engine_Api::_() -> hasModuleBootstrap("advalbum"))
		{
			return null;
		}

		if (Engine_Api::_() -> hasModuleBootstrap("album"))
		{
			$photoTable = Engine_Api::_() -> getItemTable('album_photo');
		}
		else
		{
			$photoTable = Engine_Api::_() -> getItemTable('advalbum_photo');
		}

		$select = $photoTable -> select() -> where('owner_id = ?', $iUser) -> order('photo_id  DESC') -> limit(1);
		return $photoTable -> fetchRow($select);
	}

	public function finalizeUrl($url)
	{
		if ($url)
		{
			if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
			{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
					$pageURL .= "s";
				}
				$pageURL .= "://";
				$pageURL .= $_SERVER["SERVER_NAME"];
				$url = $pageURL . '/'. ltrim( $url, '/');
			}
		}

		return $url;
	}

	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + sISO: string.
	 * + sCountry: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see core/country
	 *
	 * @param array $aData Array of data
	 * @return array
	 */
	public function country($aData)
	{
		$locale = Zend_Registry::get('Locale');
		$territories = Zend_Locale::getTranslationList('territory', $locale, 2);
		asort($territories);
		$aResult = array();
		foreach ($territories as $sISO => $sCountry)
		{
			$aResult[] = array(
				'sISO' => $sISO,
				'sCountry' => $sCountry
			);
		}
		return $aResult;
	}

	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + sISO: string.
	 * + sCountry: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see core/leftMenu
	 *
	 * @param array $aData Array of data
	 * @return array
	 */
	public function leftMenu($aData)
	{
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$menuItemsSelect = $menuItemsTable -> select() -> where('enabled = ?', 1) -> order('order');
		$enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core') -> getEnabledModuleNames();
		if (!empty($enabledModuleNames))
		{
			$menuItemsSelect -> where('module IN(?)', $enabledModuleNames);
		}
		$menuItems = $menuItemsTable -> fetchAll($menuItemsSelect);
		$aResult = array();
		foreach ($menuItems as $row)
		{
			$aResult[] = array(
				'sName' => $row -> name,
				'sLabel' => $row -> label,
				'sLayout' => $row -> layout,
				'sIcon' => $row -> icon,
				'sUrl' => $row -> uri
			);
		}
		return $aResult;
	}

	public function sidebar()
	{
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$menuItemsSelect = $menuItemsTable -> select() -> where('enabled = ?', 1) -> order('order');
		$enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core') -> getEnabledModuleNames();
		if (!empty($enabledModuleNames))
		{
			$menuItemsSelect -> where('module IN(?)', $enabledModuleNames);
		}
		$menuItems = $menuItemsTable -> fetchAll($menuItemsSelect);
		$aResult = array();
		foreach ($menuItems as $row)
		{
			$aResult[] = array(
				'sName' => $row -> name,
				'sLabel' => $row -> label,
				'sLayout' => $row -> layout,
				'sIcon' => $row -> icon,
				'sUrl' => $row -> uri
			);
		}
		return $aResult;
	}


	public function getSiteUrl()
	{
		$pageURL = 'http';
		$pageURL .= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "s" : "";
		$pageURL .= "://";
		$pageURL .= str_replace("/index.php", '', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		return $pageURL;
	}

	public function createVideo($params, $file, $create_feed = 1)
	{
		if ($file instanceof Storage_Model_File)
		{
			$params['file_id'] = $file -> getIdentity();
		}
		else
		{
		    
            $video = $this->getWorkingTable('videos','video')->createRow($params);
			
			$file_ext = pathinfo($file['name']);
			$file_ext = $file_ext['extension'];
			$video -> code = (string)$file_ext;
			$video -> save();

			// Store video in temporary storage object for ffmpeg to handle
			$storage = Engine_Api::_() -> getItemTable('storage_file');
			$storageObject = $storage -> createFile($file, array(
				'parent_id' => $video -> getIdentity(),
				'parent_type' => $video -> getType(),
				'user_id' => $video -> owner_id,
			));
			// Make sure FFMPEG path is set
			if ($this->getWorkingModule('video') == 'video'){
			     $ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> video_ffmpeg_path;    
			}else{
			     $ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> ynvideo_ffmpeg_path;    
			}
			

			if (!$ffmpeg_path)
			{
				throw new Exception('Ffmpeg not configured');
			}
			// Make sure FFMPEG can be run
			if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
			{
				$output = null;
				$return = null;
				exec($ffmpeg_path . ' -version', $output, $return);
				if ($return > 0)
				{
					throw new Exception('Ffmpeg found, but is not executable');
				}
			}
			// Check we can execute
			if (!function_exists('shell_exec'))
			{
				throw new Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
			}
			// Check the video temporary directory
			$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'video';
			if (!is_dir($tmpDir))
			{
				if (!mkdir($tmpDir, 0777, true))
				{
					throw new Exception('Video temporary directory did not exist and could not be created.');
				}
			}
			if (!is_writable($tmpDir))
			{
				throw new Exception('Video temporary directory is not writable.');
			}
			$originalPath = $storageObject -> temporary();

			$ffprobe = str_replace('ffmpeg', 'ffprobe', $ffmpeg_path);
			$cmd = $ffprobe . " " . $originalPath . " -show_streams 2>/dev/null";
			$result = shell_exec($cmd);
			$orientation = 0;
			if (strpos($result, 'TAG:rotate') !== FALSE)
			{
				$result = explode("\n", $result);
				foreach ($result as $line)
				{
					if (strpos($line, 'TAG:rotate') !== FALSE)
					{
						$stream_info = explode("=", $line);
						$orientation = $stream_info[1];
					}
				}
			}
			if ($orientation)
			{
				$transpose = 1;
				switch ($orientation)
				{
					case 90 :
						$transpose = 1;
						break;

					case 180 :
						$transpose = 3;
						break;

					case 270 :
						$transpose = 2;
						break;
				}
				$outputPath = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vrotated.' . $file_ext;
				// Check and rotate video
				$cmd = '';
				$h = '';
				if (strtolower($file_ext) == '3gp')
				{
					$h = '-s 352x288';
				}
				if ($transpose == 3)
				{
					$cmd = $ffmpeg_path . ' -i ' . escapeshellarg($originalPath) . ' -vf "vflip,hflip' . '" ' . $h . ' -b 2000k -r 30 -acodec copy -metadata:s:v:0 rotate=0 ' . escapeshellarg($outputPath);
				}
				else
				{
					$cmd = $ffmpeg_path . ' -i ' . escapeshellarg($originalPath) . ' -vf "transpose=' . $transpose . '" ' . $h . ' -b 2000k -r 30 -acodec copy -metadata:s:v:0 rotate=0 ' . escapeshellarg($outputPath);
				}
				shell_exec($cmd);
				$storageObject -> store($outputPath);
				@unlink($outputPath);
			}
			// Remove temporary file
			@unlink($file['tmp_name']);
			@unlink($originalPath);

			$video -> file_id = $storageObject -> file_id;
			$video -> save();
				
			// Add to jobs
			Engine_Api::_() -> getDbtable('jobs', 'core') -> addJob('ynmobile_encode', array('video_id' => $video -> getIdentity(), 'create_feed'=>$create_feed));
		}
		return $video;
	}

	public function setPhoto($oPhoto, $photo)
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
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName)
		{
			$fileName = $file;
		}

		$name = basename($file);
		$extension = ltrim(strrchr($fileName, '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => $oPhoto -> getType(),
			'parent_id' => $oPhoto -> getIdentity(),
			'user_id' => $oPhoto -> owner_id,
			'name' => $fileName,
		);

		// Save
		$filesTable = Engine_Api::_() -> getDbtable('files', 'storage');

		// Resize image (main)
		$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file);

		$angle = 0;
		if (function_exists("exif_read_data"))
		{
			$exif = exif_read_data($file);
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
		}
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
		try
		{
			$iMain = $filesTable -> createFile($mainPath, $params);
			$iIconNormal = $filesTable -> createFile($normalPath, $params);

			$iMain -> bridge($iIconNormal, 'thumb.normal');
		}
		catch( Exception $e )
		{
			// Remove temp files
			@unlink($mainPath);
			@unlink($normalPath);
			// Throw
			if ($e -> getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE)
			{
				throw new Album_Model_Exception($e -> getMessage(), $e -> getCode());
			}
			else
			{
				throw $e;
			}
		}

		// Remove temp files
		@unlink($mainPath);
		@unlink($normalPath);

		// Update row
		$oPhoto -> modified_date = date('Y-m-d H:i:s');
		$oPhoto -> file_id = $iMain -> file_id;
		$oPhoto -> save();

		// Delete the old file?
		if (!empty($tmpRow))
		{
			$tmpRow -> delete();
		}

		return $oPhoto;
	}

	public function setEventPhoto($oEvent, $photo)
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
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName)
		{
			$fileName = $file;
		}

		$name = basename($file);

		$extension = ltrim(strrchr($fileName, '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$name = $base . "." . $extension;

		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $oEvent -> getIdentity(),
			'parent_type' => 'event',
			'extension' => $extension
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if (function_exists("exif_read_data"))
		{
			$exif = exif_read_data($file);
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
		}
		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (feature)
		$image = Engine_Image::factory();
		@$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(242, 150) -> write($path . '/fe_' . $name) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(140, 160) -> write($path . '/in_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;
		if ($angle != 0)
		$image -> rotate($angle);
		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iFeature = $storage -> create($path . '/fe_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iFeature, 'thumb.feature');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/fe_' . $name);
		@unlink($path . '/is_' . $name);

		// Update row
		$oEvent -> modified_date = date('Y-m-d H:i:s');
		$oEvent -> photo_id = $iMain -> file_id;
		$oEvent -> save();

		// Add to album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photoTable = $this->getWorkingTable('photos','event');
		$eventAlbum = $oEvent -> getSingletonAlbum();
		$photoItem = $photoTable -> createRow();
		$photoItem -> setFromArray(array(
			'event_id' => $oEvent -> getIdentity(),
			'album_id' => $eventAlbum -> getIdentity(),
			'user_id' => $viewer -> getIdentity(),
			'file_id' => $iMain -> getIdentity(),
			'collection_id' => $eventAlbum -> getIdentity(),
			'user_id' => $viewer -> getIdentity(),
		));
		$photoItem -> save();

		return $oEvent;
	}

	public function setGroupPhoto($oGroup, $photo)
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
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		if (!$fileName)
		{
			$fileName = $file;
		}

		$name = basename($file);
		$extension = ltrim(strrchr($fileName, '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$name = $base . "." . $extension;

		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
				'parent_id' => $oGroup -> getIdentity(),
				'parent_type' => 'group',
				'extension' => $extension
		);

		// Save
		$storage = Engine_Api::_()->storage();
		$angle = 0;
		if (function_exists("exif_read_data"))
		{
			$exif = exif_read_data($file);
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
		}
		
		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image->resize(720, 720)
		->write($path.'/m_'.$name)
		->destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image->resize(200, 400)
		->write($path.'/p_'.$name)
		->destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image->resize(140, 160)
		->write($path.'/in_'.$name)
		->destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image->open($file);

		$size = min($image->height, $image->width);
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample($x, $y, $size, $size, 48, 48)
		->write($path.'/is_'.$name)
		->destroy();

		// Store
		$iMain = $storage->create($path.'/m_'.$name, $params);
		$iProfile = $storage->create($path.'/p_'.$name, $params);
		$iIconNormal = $storage->create($path.'/in_'.$name, $params);
		$iSquare = $storage->create($path.'/is_'.$name, $params);

		$iMain->bridge($iProfile, 'thumb.profile');
		$iMain->bridge($iIconNormal, 'thumb.normal');
		$iMain->bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path.'/p_'.$name);
		@unlink($path.'/m_'.$name);
		@unlink($path.'/in_'.$name);
		@unlink($path.'/is_'.$name);

		// Update row
		$oGroup->modified_date = date('Y-m-d H:i:s');
		$oGroup->photo_id = $iMain->file_id;
		$oGroup->save();

		// Add to album
		$viewer = Engine_Api::_()->user()->getViewer();
		$photoTable = $this->getWorkingTable('photos','group');
        
		$groupAlbum = $oGroup->getSingletonAlbum();
		$photoItem = $photoTable->createRow();
		$photoItem->setFromArray(array(
				'group_id' => $oGroup->getIdentity(),
				'album_id' => $groupAlbum->getIdentity(),
				'user_id' => $viewer->getIdentity(),
				'file_id' => $iMain->getIdentity(),
				'collection_id' => $groupAlbum->getIdentity(),
		));
		$photoItem->save();

		return $oGroup;
	}


	public function setClassifiedPhoto($oListing, $photo)
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
			throw new Classified_Model_Exception('invalid argument passed to setPhoto');
		}

		if( !$fileName ) {
			$fileName = basename($file);
		}

		$extension = ltrim(strrchr(basename($fileName), '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

		$params = array(
		      'parent_type' => 'classified',
		      'parent_id' => $oListing->getIdentity(),
		      'user_id' => $oListing->owner_id,
		      'name' => $fileName,
		);

		// Save
		$filesTable = Engine_Api::_()->getDbtable('files', 'storage');
		$angle = 0;
		if (function_exists("exif_read_data"))
		{
			$exif = exif_read_data($file);
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
		}
		
		// Resize image (main)
		$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image->resize(720, 720)
		->write($mainPath)
		->destroy();

		// Resize image (profile)
		$profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image->resize(200, 400)
		->write($profilePath)
		->destroy();

		// Resize image (normal)
		$normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image->resize(140, 160)
		->write($normalPath)
		->destroy();

		// Resize image (icon)
		$squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
		$image = Engine_Image::factory();
		$image->open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$size = min($image->height, $image->width);
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample($x, $y, $size, $size, 48, 48)
		->write($squarePath)
		->destroy();

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


		// Add to album
		$viewer = Engine_Api::_()->user()->getViewer();
		$photoTable = Engine_Api::_()->getItemTable('classified_photo');
		$classifiedAlbum = $oListing->getSingletonAlbum();
		$photoItem = $photoTable->createRow();
		$photoItem->setFromArray(array(
	      'classified_id' => $oListing->getIdentity(),
	      'album_id' => $classifiedAlbum->getIdentity(),
	      'user_id' => $viewer->getIdentity(),
	      'file_id' => $iMain->getIdentity(),
	      'collection_id' => $classifiedAlbum->getIdentity(),
		));
		$photoItem->save();

		// Update row
		$oListing->modified_date = date('Y-m-d H:i:s');
		$oListing->photo_id = $photoItem->file_id;
		$oListing->save();

		return $oListing;
	}


	public function rate($aData)
	{
		if (empty($aData['sItemType']) || empty($aData['iItemId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Can not find the item!")
			);
		}

		if (!isset($aData['iRating']))
		{
			return array(
				'error_code' => 2,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Rating value!")
			);
		}

		if ($aData['sItemType'] == 'video')
		{
			$aParams = array(
				'iVideoId' => $aData['iItemId'],
				'iRating' => $aData['iRating']
			);
			return Engine_Api::_() -> getApi('Video', 'Ynmobile') -> rate($aParams);
		}
		else
		{
			return array(
				'error_code' => 3,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Sorry, we do not support rating this item!")
			);
		}

	}

	public function ping(){
		return array('error_code'=>0,'error_message'=>'');
	}

	public function settings()
	{
		$classifiedSearchFields = Engine_Api::_()->getApi("classified", "ynmobile")->getAliasFields();
		$classifiedCustomFields = Engine_Api::_()->getApi("classified", "ynmobile")->getCustomFields();
		$chatName = Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynmobile.chat', '');
		
		return array(
			'chat_module'=> $chatName,
			'classifield_search_fields' => $classifiedSearchFields,
			'classifield_custom_fields' => $classifiedCustomFields,
		);
	}

	public function getUserTotalPhoto($iUserId)
	{
		if (null == $iUserId)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$iUserId = $viewer -> getIdentity();
		}

		if (!$iUserId)
		return 0;

		if (Engine_Api::_() -> hasModuleBootstrap("album"))
		{
			$photoTable = Engine_Api::_() -> getItemTable('album_photo');
		}
		else
		{
			$photoTable = Engine_Api::_() -> getItemTable('advalbum_photo');
		}

		return $photoTable->select()
		->from($photoTable, new Zend_Db_Expr('COUNT(photo_id)'))
		->where("owner_type = 'user'")
		->where("owner_id = ?", $iUserId)
		->where("album_id > 0")
		->limit(1)
		->query()
		->fetchColumn();

	}

	/**
	 */
	public function validate_token(){
		return array('error_code'=>0,'error_mesage'=>'');
	}

	public function getUserTotalFriend($iUserId)
	{
		if (!$iUserId)
		return 0;

		$membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
		$membershipName = $membershipTable -> info('name');
		$select = $membershipTable -> select() -> from($membershipTable, new Zend_Db_Expr('COUNT(resource_id)'));

		return $select
		-> where("{$membershipName}.user_id = ?", $iUserId)
		-> where('active = 1')
		-> limit(1)
		-> query()
		-> fetchColumn();
	}


	public function getUserTimeZone($user = null)
	{
		if (is_null($user))
		{
			$user = Engine_Api::_()->user()->getViewer();
		}
		$locale = array(
				'US/Pacific'  => -8.00,
				'US/Mountain' => -7.00,
				'US/Central'  => -6.00,
				'US/Eastern'  => -5.00,
				'America/Halifax'   => -4.00,
				'America/Anchorage' => -9.00,
				'Pacific/Honolulu'  => -10.00,
				'Pacific/Samoa'     => -11.00,
				'Etc/GMT-12' => -12.00,
				'Canada/Newfoundland' => -3.5,
				'America/Buenos_Aires' => -3.00,
				'Atlantic/South_Georgia' => -2.00,
				'Atlantic/Azores' => -1.00,
				'Europe/London' => 0.00,
				'Europe/Berlin' => 1.00,
				'Europe/Athens' => 2.00,
				'Europe/Moscow' => 3.00,
				'Iran' => 3.5,
				'Asia/Dubai' => 4.00,
				'Asia/Kabul' => 4.5,
				'Asia/Yekaterinburg' => 5.00,
				'Asia/Calcutta' => 5.5,
				'Asia/Katmandu' => 5.75,
				'Asia/Omsk' => 6.00,
				'India/Cocos' => 6.5,
				'Asia/Krasnoyarsk' => 7.00,
				'Asia/Hong_Kong' => 8.00,
				'Asia/Tokyo' => 9.00,
				'Australia/Adelaide' => 9.5,
				'Australia/Sydney' => 10.00,
				'Asia/Magadan' => 11.00,
				'Pacific/Auckland' => 12.00,
		);

		$timezone = $user->timezone;
		if (!$timezone)
		{
			$timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.locale.timezone', 'US/Pacific');
		}
		
		if (isset($locale[$timezone]))
		{
			return $locale[$timezone];
		}
		else
		{
			return '';
		}
	}	

    function _getTimezones(){
        $options =  array('US/Pacific'  => '(UTC-8) Pacific Time (US & Canada)',
        'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
        'US/Central'  => '(UTC-6) Central Time (US & Canada)',
        'US/Eastern'  => '(UTC-5) Eastern Time (US & Canada)',
        'America/Halifax'   => '(UTC-4)  Atlantic Time (Canada)',
        'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
        'Pacific/Honolulu'  => '(UTC-10) Hawaii (US)',
        'Pacific/Samoa'     => '(UTC-11) Midway Island, Samoa',
        'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
        'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
        'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
        'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
        'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
        'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
        'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
        'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
        'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
        'Iran' => '(UTC+3:30) Tehran',
        'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
        'Asia/Kabul' => '(UTC+4:30) Kabul',
        'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
        'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
        'Asia/Katmandu' => '(UTC+5:45) Nepal',
        'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
        'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
        'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
        'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
        'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
        'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
        'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
        'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
        'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',);
        
        $return = array();
        
        foreach($options as $key=>$val){
            $return [] =  array('key'=>$key, 'val'=>$val);
        }

        return $return;
    }

    function _getLocales(){
        $localeMultiKeys = array_merge(
          array_keys(Zend_Locale::getLocaleList())
        );
        $localeMultiOptions = array();
        $languages = Zend_Locale::getTranslationList('language', $locale);
        $territories = Zend_Locale::getTranslationList('territory', $locale);
        foreach($localeMultiKeys as $key)
        {     
           if (!empty($languages[$key])) 
           {
             $localeMultiOptions[$key] = $languages[$key];
           }
           else
           {
             $locale = new Zend_Locale($key);
             $region = $locale->getRegion();
             $language = $locale->getLanguage(); 
             if ((!empty($languages[$language]) && (!empty($territories[$region])))) {
               $localeMultiOptions[$key] =  $languages[$language] . ' (' . $territories[$region] . ')';
             }
           }
        }
        $localeMultiOptions = array_merge(array('auto'=>'[Automatic]'), $localeMultiOptions);
        
        $return = array();
        
        foreach($localeMultiOptions as $key=>$val){
            $return[] =  array('key'=>$key, 'val'=>$val);
        }
        
        return $return;
    }

    /**
     * @return array.
     */
    function allows(){
            
        $types =  array();
        $maps  =  array(
            'mp3music_album'=>'music_playlist',
            'advgroup_poll'=>'group_poll',
        );
        
        $viewer  = Engine_Api::_()->user()->getViewer();
        
        if(!$viewer){
            return array();
        }
        
        $engine  =  Engine_Api::_();
        
        $level_id  = $viewer->level_id;
        
        $table = $engine->getDbtable('permissions','authorization');
        
        $select = $table->select()
            ->where('level_id=?', $level_id);
            
        $result = array();
        
        foreach($table->fetchAll($select) as $row){
            $type  = $row->type;
            
            
            if((isset($types[$type]) && $types[$type]) || true == ($types[$type] = $engine->hasItemType($type))){
                $type = isset($maps[$type])?$maps[$type]: $type;
                $action = $row->name;
                if($row->value == 5){
                    $result[$type.'.'.$action] = 1;
                }else if($row->value == 3){
                    $result[$type.'.'.$action] = 0;
                }else{
                    $result[$type.'.'.$action] = $row->value?1:0;    
                }
            }
        }
        
        return $result;
    }

    public function left_nav(){
    	$return = array();

    	$return[] = array(
    			'sLabel'=>'Profile Name',
    			'sUrl' => 'profile',
    			'sIcon'=>'img_profile'
    		);

    	$return[] = array(
    			'sLabel'=>'Search',
    			'sUrl'=>'search',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'News Feed',
    			'sUrl'=>'news_feed',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Members',
    			'sUrl'=>'members',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Friends',
    			'sUrl'=>'friends',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Groups',
    			'sUrl'=>'groups',
    			'sIcon'=>'ic_launcher'
    		);

    	$return[] = array(
    			'sLabel'=>'Events',
    			'sUrl'=>'events',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Videos',
    			'sUrl'=>'videos',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Music',
    			'sUrl'=>'music_playlists',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Forums',
    			'sUrl'=>'forums',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Blogs',
    			'sUrl'=>'blogs',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Classifieds',
    			'sUrl'=>'classifieds',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Memberships',
    			'sUrl'=>'memberships',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Settings',
    			'sUrl'=>'settings',
    			'sIcon'=>'ic_launcher'
    		);
    	$return[] = array(
    			'sLabel'=>'Logout',
    			'sUrl'=>'logout',
    			'sIcon'=>'ic_launcher'
    		);

    	return $return;

    }
}
