<?php
class Ynultimatevideo_Plugin_Adapter_VideoURL
{

	protected $_params;

	public function compileVideo($params)
	{
		$video_id = $params['video_id'];
		$location = $params['location'];
		$view = $params['view'];
		$mobile = $params['mobile'];
		$class = "";
		if($mobile)
		{
			$class="video-js vjs-default-skin";
		}
		$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
		$embedded = '
		<img class = "thumb_video" src ="'.$video -> getPhotoUrl("thumb.main").'"/>
		 <video id="player_'. $video_id .'" class="ynultimatevideo-player '.$class.'" controls
			 preload="none" poster="' . $video -> getPhotoUrl("thumb.main") . '"
			 data-setup="{}">
			 <source src="' . $location . '" type="video/mp4">
			</video>';
		return $embedded;
	}

	public function extractVideo($params) {
		$video_id = $params['video_id'];
		$location = $params['location'];
		$view = $params['view'];
		$mobile = $params['mobile'];
		$class = "";
		if($mobile)
		{
			$class="video-js vjs-default-skin";
		}
		$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
		$embedded = '
		<img class = "thumb_video" src ="'.$video -> getPhotoUrl("thumb.main").'"/>
		 <video id="player_'. $video_id .'" class="ynultimatevideo-player '.$class.'" controls
			 preload="none" poster="' . $video -> getPhotoUrl("thumb.main") . '"
			 data-setup="{}" style="display:none;" data-type="5" width="766" height="426">
			 <source src="' . $location . '" type="video/mp4" >
			</video>';
		return $embedded;
	}

	public function setParams($options)
	{
		foreach ($options as $key => $value)
		{
			$this -> _params[$key] = $value;
		}
	}
	
	public function getVideoLargeImage()
	{
		if (isset($this -> _params) && array_key_exists('video_id', $this -> _params))
		{
			$video_id = $this -> _params['video_id'];
			return $this->getVideoImage($video_id);
		}
	}
	
	public function getVideoImage($video_id = 0)
	{
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);

			if ($video instanceof Ynultimatevideo_Model_Video)
			{
				$video -> duration = $duration = $this -> getVideoDuration($video_id);
				$thumb_splice = $duration / 2;
				$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'ynultimatevideo';
				$thumbPathLarge = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vthumb_large.jpg';
				$thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vthumb.jpg';
				$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> ynultimatevideo_ffmpeg_path;

				if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
				{
					$output = null;
					$return = null;
					exec($ffmpeg_path . ' -version', $output, $return);
					if ($return > 0)
					{
						return 0;
					}
				}

				// Prepare output header
				$output = PHP_EOL;
				$output .= $video -> code . PHP_EOL;
				$output .= $thumbPathLarge . PHP_EOL;

				$thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($video -> code) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbPathLarge) . ' ' . '2>&1';
				// Process thumbnail
				$thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);
				// Check output message for success
				$thumbSuccess = true;
				if (preg_match('/video:0kB/i', $thumbOutput))
				{
					$thumbSuccess = false;
				}

				// Resize thumbnail
				if ($thumbSuccess && is_file($thumbPathLarge))
				{
					try
					{
						$image = Engine_Image::factory();

						$image -> open($thumbPathLarge) -> resize(640, 360) -> write($thumbPathLarge) -> destroy();

						$thumbFileRowLarge = Engine_Api::_() -> storage() -> create($thumbPathLarge, array(
							'parent_id' => $video -> getIdentity(),
							'parent_type' => $video -> getType(),
							'user_id' => $video -> owner_id
						));

						$image -> open($thumbPathLarge) -> resize(120, 240) -> write($thumbPath) -> destroy();
						$thumbFileRow = Engine_Api::_() -> storage() -> create($thumbPath, array(
							'parent_id' => $video -> getIdentity(),
							'parent_type' => $video -> getType(),
							'user_id' => $video -> owner_id
						));

						$video -> large_photo_id = $thumbFileRowLarge -> file_id;
						$video -> photo_id = $thumbFileRow -> file_id;
						$video -> save();

						unlink($thumbPathLarge);
						unlink($thumbPath);
						return true;
					}
					catch (Exception $e)
					{
						throw $e;
						unlink($thumbPathLarge);
						unlink($thumbPath);
					}
				}
			}
		}
	}

	public function getVideoDuration($video_id = 0)
	{
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);

			if ($video instanceof Ynultimatevideo_Model_Video)
			{
				$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> ynultimatevideo_ffmpeg_path;

				if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
				{
					$output = null;
					$return = null;
					exec($ffmpeg_path . ' -version', $output, $return);
					if ($return > 0)
					{
						return 0;
					}
				}

				// Prepare output header
				$fileCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($video -> code) . ' ' . '2>&1';
				// Process thumbnail
				$fileOutput = shell_exec($fileCommand);
				// Check output message for success
				$infoSuccess = true;
				if (preg_match('/video:0kB/i', $fileOutput))
				{
					$infoSuccess = false;
				}

				// Resize thumbnail
				if ($infoSuccess)
				{
					// Get duration of the video to caculate where to get the thumbnail
					if (preg_match('/Duration:\s+(.*?)[.]/i', $fileOutput, $matches))
					{
						list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
						$duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
					}
					else
					{
						$duration = 0;
						// Hmm
					}
				}
			}
		}
		return $duration;
	}

	public function isValid()
	{
		if (isset($this -> _params['link']))
		{
			$valid = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this -> _params['link']);
			$params = @pathinfo($this -> _params['link']);
			if (isset($params['extension']) && (strtoupper($params['extension']) == 'FLV' || strtoupper($params['extension']) == 'MP4'))
			{
				return true;
			}
		}
		return false;
	}

	public static function getDefaultTitle()
	{
		return Zend_Registry::get('Zend_Translate') -> _('Untitled video');
	}

}
