<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynmobile
 * @author     YouNet Company
 */

class Ynmobile_Plugin_Job_Encode extends Core_Plugin_Job_Abstract {

	var $defaultVideo = TRUE;
    var $createFeed = 1;
    
	
    protected function _execute() {
    	
    	$this->defaultVideo = Engine_Api::_() -> hasModuleBootstrap("video");
    	
        // Get job and params
        $job = $this->getJob();

        $this->createFeed = $this->getParam('create_feed');

        // No video id?
        if (!($video_id = $this->getParam('video_id'))) {
            $this->_setState('failed', 'No video identity provided.');
            $this->_setWasIdle();
            return;
        }
        
        // Get video object
        $video = Engine_Api::_()->getApi('video','ynmobile')->getWorkingItem('video', $video_id);
        
        if ( !is_object($video) || !($video->getIdentity()) ) {
            $this->_setState('failed', 'Video is missing.');
            $this->_setWasIdle();
            return;
        }

        // Check video status
        if (0 != $video->status) {
            $this->_setState('failed', 'Video has already been encoded, or has already failed encoding.');
            $this->_setWasIdle();
            return;
        }

        // Process
        try {
        	
            $this->_process($video);
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());

            // Attempt to set video state to failed
            try {
                if (1 != $video->status) {
                    $video->status = 3;
                    $video->save();
                }
            } catch (Exception $e) {
                $this->_addMessage($e->getMessage());
            }
        }
    }

    protected function _process($video) {
        // Make sure FFMPEG path is set
        $ffmpeg_path = ($this->defaultVideo)
        				? Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path
        				: Engine_Api::_()->getApi('settings', 'core')->ynvideo_ffmpeg_path;
        
        if (!$ffmpeg_path) {
            throw new Exception('Ffmpeg not configured');
        }
        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if ($return > 0) {
                throw new Exception('Ffmpeg found, but is not executable');
            }
        }

        // Check we can execute
        if (!function_exists('shell_exec')) {
            throw new Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
        }

        // Check the video temporary directory
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
                DIRECTORY_SEPARATOR . 'video';
        if (!is_dir($tmpDir)) {
            if (!mkdir($tmpDir, 0777, true)) {
                throw new Exception('Video temporary directory did not exist and could not be created.');
            }
        }
        if (!is_writable($tmpDir)) {
            throw new Exception('Video temporary directory is not writable.');
        }

        // Get the video object
        if (is_numeric($video)) {
            $video = $this->getWorkingItem('video', $video_id);
        }

        if (!($video instanceof Video_Model_Video) && !($video instanceof Ynvideo_Model_Video)) {
            throw new Exception('Argument was not a valid video');
        }

        // Update to encoding status
        $video->status = 2;
        $video->type = 3;
        $video->save();

        // Prepare information
        $owner = $video->getOwner();
        $filetype = $video->code;

        // Pull video from storage system for encoding
        $storageObject = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if (!$storageObject) {
            throw new Exception('Video storage file was missing');
        }

        $originalPath = $storageObject->temporary();
        if (!file_exists($originalPath)) {
            throw new Exception('Could not pull to temporary file');
        }
        //For player on mobile
        $outputPath_mpeg4 = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vconverted_mpg4.mp4';
        //For player on web
        $outputPath_h264 = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vconverted_h264.mp4';
        
        $thumbPathLarge = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vthumb_large.jpg';
        $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vthumb.jpg';

        //For player on mobile
        $mpeg4_videoCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($originalPath) . ' ' . '-acodec aac -strict experimental -vcodec mpeg4 -mbd 2 -cmp 2 -subcmp 2 -y ' . escapeshellarg($outputPath_mpeg4) . ' 2>&1';
        //For player on web
        $h264_videoCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($originalPath) . ' ' . '-acodec libfaac -ab 96k -vcodec libx264 -level 21 -refs 2 -bt 2000k -threads 0 -y ' . escapeshellarg($outputPath_h264) . ' ';
        //echo $mpeg4_videoCommand . "--------" . $h264_videoCommand;
        //throw new Exception($mpeg4_videoCommand . "--------" . $h264_videoCommand); exit;
        
        // Prepare output header
        $output = PHP_EOL;
        $output .= $originalPath . PHP_EOL;
        $output .= $outputPath_mpeg4 . PHP_EOL;
        $output .= $thumbPathLarge . PHP_EOL;

        // Prepare logger
        $log = null;
        //if( APPLICATION_ENV == 'development' ) {
        $log = new Zend_Log();
        $log->addWriter(new Zend_Log_Writer_Null());
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/video.log'));
        //}
        // Execute video encode command
        $videoOutput_mpeg4 = $output . $mpeg4_videoCommand . PHP_EOL . shell_exec($mpeg4_videoCommand);
  		$videoOutput_h264 = $output . $h264_videoCommand . PHP_EOL . shell_exec($h264_videoCommand);

        // Log
        if ($log) {
            $log->log($videoOutput_mpeg4, Zend_Log::INFO);
        }

        // Check for failure
        $success = true;

        // Unsupported format
        if (preg_match('/Unknown format/i', $videoOutput_mpeg4) ||
                preg_match('/Unsupported codec/i', $videoOutput_mpeg4) ||
                preg_match('/patch welcome/i', $videoOutput_mpeg4) ||
                preg_match('/Audio encoding failed/i', $videoOutput_mpeg4) ||
                !is_file($outputPath_mpeg4) ||
                filesize($outputPath_mpeg4) <= 0) {
            $success = false;
            $video->status = 3;
        }

        // This is for audio files
        else if (preg_match('/video:0kB/i', $videoOutput_mpeg4)) {
            $success = false;
            $video->status = 5;
        }

        // Failure
        if (!$success) {

            $exceptionMessage = '';

            $db = $video->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                $video->save();


                // notify the owner
                $translate = Zend_Registry::get('Zend_Translate');
                $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
                $notificationMessage = '';

                if ($video->status == 3) {
                    $exceptionMessage = 'Video format is not supported by FFMPEG.';
                    $notificationMessage = $translate->translate(sprintf(
                                            'Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '', ''
                                    ), $language);
                } else if ($video->status == 5) {
                    $exceptionMessage = 'Audio-only files are not supported.';
                    $notificationMessage = $translate->translate(sprintf(
                                            'Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '', ''
                                    ), $language);
                } else {
                    $exceptionMessage = 'Unknown encoding error.';
                }
                
				if ($this->defaultVideo)
				{
					Engine_Api::_()->getDbtable('notifications', 'activity')
					->addNotification($owner, $owner, $video, 'video_processed_failed', array(
					'message' => $notificationMessage,
					'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'video_general', true),
					));
				}
				else 
				{
					Engine_Api::_()->getDbtable('notifications', 'activity')
					->addNotification($owner, $owner, $video, 'ynvideo_processed_failed', array(
					'message' => $notificationMessage,
					'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'video_general', true),
					));
				}
				                        
				

                $db->commit();
            } catch (Exception $e) {
                $videoOutput_mpeg4 .= PHP_EOL . $e->__toString() . PHP_EOL;
                if ($log) {
                    $log->write($e->__toString(), Zend_Log::ERR);
                }
                $db->rollBack();
            }

            // Write to additional log in dev
            if (APPLICATION_ENV == 'development') {
                file_put_contents($tmpDir . '/' . $video->video_id . '.txt', $videoOutput_mpeg4);
            }

            throw new Exception($exceptionMessage);
        }

        // Success
        else {
            // Get duration of the video to caculate where to get the thumbnail
            if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput_mpeg4, $matches)) {
                list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
                $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
            } else {
                $duration = 0; // Hmm
            }

            // Log duration
            if ($log) {
                $log->log('Duration: ' . $duration, Zend_Log::INFO);
            }

            // Fetch where to take the thumbnail
            $thumb_splice = $duration / 2;

            // Thumbnail proccess command
            $thumbCommand = $ffmpeg_path . ' '
                    . '-i ' . escapeshellarg($outputPath_mpeg4) . ' '
                    . '-f image2' . ' '
                    . '-ss ' . $thumb_splice . ' '
                    . '-vframes ' . '1' . ' '
                    . '-v 2' . ' '
                    . '-y ' . escapeshellarg($thumbPathLarge) . ' '
                    . '2>&1'
            ;

            // Process thumbnail
            $thumbOutput = $output .
                    $thumbCommand . PHP_EOL .
                    shell_exec($thumbCommand);

            // Log thumb output
            if ($log) {
                $log->log($thumbOutput, Zend_Log::INFO);
            }

            // Check output message for success
            $thumbSuccess = true;
            if (preg_match('/video:0kB/i', $thumbOutput)) {
                $thumbSuccess = false;
            }

            // Resize thumbnail
            if ($thumbSuccess) {
                try {
                    $image = Engine_Image::factory();
                    
                    $image->open($thumbPathLarge)
                            //->resize(480, 360)
                    		->resize(960, 720)
                            ->write($thumbPathLarge)
                            ->destroy();
                    
                    $image->open($thumbPathLarge)
                            ->resize(120, 240)
                            ->write($thumbPath)
                            ->destroy();
                    
                } catch (Exception $e) {
                    $this->_addMessage((string) $e->__toString());
                    $thumbSuccess = false;
                }
            }

            // Save video and thumbnail to storage system
            $params = array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id
            );

            $db = $video->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $storageObject->setFromArray($params);
                $storageObject->store($outputPath_h264);
                
                $newObject = Engine_Api::_() -> storage() -> create($outputPath_mpeg4, $params);
                
                if ($thumbSuccess) {
                    $thumbFileRowLarge = Engine_Api::_()->storage()->create($thumbPathLarge, $params);
                    $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, $params);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();

                // delete the files from temp dir
                unlink($originalPath);
                unlink($outputPath_mpeg4);
                unlink($outputPath_h264);
                
                if ($thumbSuccess) {
                    unlink($thumbPathLarge);
                    unlink($thumbPath);
                }

                $video->status = 7;
                $video->save();

                // notify the owner
                $translate = Zend_Registry::get('Zend_Translate');
                $notificationMessage = '';
                $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
                if ($video->status == 7) {
                    $notificationMessage = $translate->translate(sprintf(
                                            'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '', ''
                                    ), $language);
                }
                
                if ($this->defaultVideo)
                {
                	Engine_Api::_()->getDbtable('notifications', 'activity')
                	->addNotification($owner, $owner, $video, 'video_processed_failed', array(
                	'message' => $notificationMessage,
                	'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'video_general', true),
                	));
                }
                else
                {
                	Engine_Api::_()->getDbtable('notifications', 'activity')
                	->addNotification($owner, $owner, $video, 'ynvideo_processed_failed', array(
                	'message' => $notificationMessage,
                	'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'video_general', true),
                	));
                }

                throw $e; // throw
            }

            // Video processing was a success!
            // Save the information
            if ($thumbSuccess) {
                $video->photo_id = $thumbFileRow->file_id;
                if (!$this->defaultVideo)
                	$video->large_photo_id = $thumbFileRowLarge->file_id;
                
                $video->file1_id = $newObject->file_id;
            }
            $video->duration = $duration;
            $video->status = 1;
            $video->save();

            // delete the files from temp dir
            unlink($originalPath);
            unlink($outputPath_mpeg4);
            unlink($outputPath_h264);
            unlink($thumbPathLarge);

            // insert action in a seperate transaction if video status is a success
            $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
            $db = $actionsTable->getAdapter();
            $db->beginTransaction();

            try {
            	if ($video->status_text)
            	{

            	}
            	else
            	{
            		// new action
            		$action = $actionsTable -> addActivity($owner, $video, 'video_new');
            	}
                
                if ($action) {
                    $actionsTable->attachActivity($action, $video);
                }

                // notify the owner
                if ($this->defaultVideo)
                {
                	Engine_Api::_()->getDbtable('notifications', 'activity')
                		->addNotification($owner, $owner, $video, 'video_processed');
                }
                else 
                {
                	Engine_Api::_()->getDbtable('notifications', 'activity')
                		->addNotification($owner, $owner, $video, 'ynvideo_processed');
                }
                
               

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e; // throw
            }
        }
    }

}