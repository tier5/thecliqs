<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Adapter_Uploaded
{

	protected $_params;

	public function compileVideo($params)
	{
		$video_id = $params['video_id'];
		$location = $params['location'];
		$location1 = $params['location1'];
		$view = $params['view'];
		$duration = $params['duration'];
		$mobile = $params['mobile'];
		$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
		$class = "";
		if($location1)
		{
			if($mobile)
			{
				$class="video-js vjs-default-skin";
			}
			$embedded = '
			 <img class = "thumb_video" src ="'.$video -> getPhotoUrl("thumb.main").'"/>
			 <video id="player_' . $video_id . '" class="ynultimatevideo-player '.$class.'" controls
				 preload="auto" poster="' . $video -> getPhotoUrl("thumb.main") . '"
				 data-setup="{}">
				  <source src="' . $location1 . '" type="video/mp4">
				</video>';
		}
		else
		{
			 $embedded = "
		  <div id='videoFrame".$video_id."'></div>
		  <script type='text/javascript'>
		  en4.core.runonce.add(function(){\$('video_thumb_".$video_id.$params['count_video']."').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$video_id',{src: '".Zend_Registry::get('StaticBaseUrl')."externals/flowplayer/flowplayer-3.1.5.swf', width: ".($view?"480":"420").", height: ".($view?"386":"326").", wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: ".($view?"false":"true").", duration: '$duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})});
		  </script>";
		}
		return $embedded;
	}

	public function extractVideo($params)
	{
		$video_id = $params['video_id'];
		$location = $params['location'];
		$location1 = $params['location1'];
		$view = $params['view'];
		$duration = $params['duration'];
		$mobile = $params['mobile'];
		$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
		$class = "";
		if($location1)
		{
			if($mobile)
			{
				$class="video-js vjs-default-skin";
			}
			$embedded = '
			 <img class = "thumb_video" src ="'.$video -> getPhotoUrl("thumb.main").'"/>
			 <video id="player_' . $video_id . '" class="ynultimatevideo-player '.$class.'" controls
				 preload="auto" poster="' . $video -> getPhotoUrl("thumb.main") . '"
				 data-setup="{}" style="display:none;" data-type="3" width="766" height="426">
				 <source src="' . $location1 . '" type="video/mp4">
				</video>';
		}
		else
		{
			$embedded = '
			 <img class = "thumb_video" src ="'.$video -> getPhotoUrl("thumb.main").'"/>
			 <video id="player_' . $video_id . '" class="ynultimatevideo-player '.$class.'" controls
				 preload="auto" poster="' . $video -> getPhotoUrl("thumb.main") . '"
				 data-setup="{}" style="display:none;" data-type="3" width="766" height="426">
				 <source src="' . $location . '" type="video/mp4">
				</video>';
		}
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
			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);

			if ($video instanceof Ynultimatevideo_Model_Video)
			{
				$storageObject = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if (!$storageObject)
				{
					throw new Ynultimatevideo_Model_Exception('Video storage file was missing');
				}

				$thumb_splice = $video -> duration / 2;
				$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'ynultimatevideo';
				$thumbPathLarge = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vthumb_large.jpg';
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
				$output .= $storageObject -> temporary() . PHP_EOL;
				$output .= $thumbPathLarge . PHP_EOL;

				$thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($storageObject -> storage_path) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbPathLarge) . ' ' . '2>&1';
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
						return $thumbFileRowLarge -> file_id;
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
			}
		}
	}
}
