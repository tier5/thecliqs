<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Encode.php 21.02.12 17:23 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Plugin_Job_Encode extends Core_Plugin_Job_Abstract
{
  protected function _execute()
  {
    // Get job and params
    $job = $this->getJob();

    // No gift id?
    if( !($gift_id = $this->getParam('gift_id')) ) {
      $this->_setState('failed', 'No video identity provided.');
      $this->_setWasIdle();
      return;
    }

    // Get gift object
    $gift = Engine_Api::_()->getItem('gift', $gift_id);
    if( !$gift || !($gift instanceof Hegift_Model_Gift) ) {
      $this->_setState('failed', 'Gift is missing.');
      $this->_setWasIdle();
      return;
    }

    // Check video status
    if( 0 != $gift->status ) {
      $this->_setState('failed', 'Video has already been encoded, or has already failed encoding.');
      $this->_setWasIdle();
      return;
    }

    // Process
    try {
      $this->_process($gift);
      $this->_setIsComplete(true);
    } catch( Exception $e ) {
      $this->_setState('failed', 'Exception: ' . $e->getMessage());

      // Attempt to set video state to failed
      try {
        if( 1 != $gift->status ) {
          $gift->status = 3;
          $gift->save();
        }
      } catch( Exception $e ) {
        $this->_addMessage($e->getMessage());
      }
    }
  }

  protected function _process($gift)
  {
    // Make sure FFMPEG path is set
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if( !$ffmpeg_path ) {
      throw new Core_Model_Exception('Ffmpeg not configured');
    }
    // Make sure FFMPEG can be run
    if( !@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path) ) {
      $output = null;
      $return = null;
      exec($ffmpeg_path . ' -version', $output, $return);
      if( $return > 0 ) {
        throw new Core_Model_Exception('Ffmpeg found, but is not executable');
      }
    }

    // Check we can execute
    if( !function_exists('shell_exec') ) {
      throw new Core_Model_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
    }

    // Check the video temporary directory
    $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
      DIRECTORY_SEPARATOR . 'video';
    if( !is_dir($tmpDir) ) {
      if( !mkdir($tmpDir, 0777, true) ) {
        throw new Core_Model_Exception('Video temporary directory did not exist and could not be created.');
      }
    }
    if( !is_writable($tmpDir) ) {
      throw new Core_Model_Exception('Video temporary directory is not writable.');
    }

    // Get the video object
    if( is_numeric($gift) ) {
      $gift = Engine_Api::_()->getItem('gift', $gift);
    }

    if( !($gift instanceof Hegift_Model_Gift) ) {
      throw new Core_Model_Exception('Argument was not a valid video');
    }

    // Update to encoding status
    $gift->status = 2;
    $gift->save();

    // Pull video from storage system for encoding
    $storageObject = Engine_Api::_()->getItem('storage_file', $gift->file_id);
    if( !$storageObject ) {
      throw new Core_Model_Exception('Gift storage file was missing');
    }
    $originalPath = $storageObject->temporary();
    if( !file_exists($originalPath) ) {
      throw new Core_Model_Exception('Could not pull to temporary file');
    }
    $outputPath   = $tmpDir . DIRECTORY_SEPARATOR . $gift->getIdentity() . '_vconverted.flv';
    $thumbPath    = $tmpDir . DIRECTORY_SEPARATOR . $gift->getIdentity() . '_vthumb.jpg';

    $videoCommand = $ffmpeg_path . ' '
      . '-i ' . escapeshellarg($originalPath) . ' '
      . '-ab 64k' . ' '
      . '-ar 44100' . ' '
      . '-qscale 5' . ' '
      . '-vcodec flv' . ' '
      . '-f flv' . ' '
      . '-r 25' . ' '
      . '-s 480x386' . ' '
      . '-v 2' . ' '
      . '-y ' . escapeshellarg($outputPath) . ' '
      . '2>&1'
      ;

    // Prepare output header
    $output  = PHP_EOL;
    $output .= $originalPath . PHP_EOL;
    $output .= $outputPath . PHP_EOL;
    $output .= $thumbPath . PHP_EOL;

    // Prepare logger
    $log = null;
    //if( APPLICATION_ENV == 'development' ) {
      $log = new Zend_Log();
      $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/video.log'));
    //}

    // Execute video encode command
    $videoOutput = $output .
      $videoCommand . PHP_EOL .
      shell_exec($videoCommand);

    // Log
    if( $log ) {
      $log->log($videoOutput, Zend_Log::INFO);
    }

    // Check for failure
    $success = true;

    // Unsupported format
    if( preg_match('/Unknown format/i', $videoOutput) ||
        preg_match('/Unsupported codec/i', $videoOutput) ||
        preg_match('/patch welcome/i', $videoOutput) ||
        preg_match('/Audio encoding failed/i', $videoOutput) ||
        !is_file($outputPath) ||
        filesize($outputPath) <= 0 ) {
      $success = false;
      $gift->status = 3;
    }

    // This is for audio files
    else if( preg_match('/video:0kB/i', $videoOutput) ) {
      $success = false;
      $gift->status = 5;
    }

    // Failure
    if( !$success ) {
      // Write to additional log in dev
      if( APPLICATION_ENV == 'development' ) {
        file_put_contents($tmpDir . '/' . $gift->getIdentity() . '.txt', $videoOutput);
      }

      $this->returnCredits($gift);
    }

    // Success
    else
    {
      // Get duration of the video to caculate where to get the thumbnail
      if( preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches) ) {
        list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
        $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
      } else {
        $duration = 0; // Hmm
      }

      // Log duration
      if( $log ) {
        $log->log('Duration: ' . $duration, Zend_Log::INFO);
      }

      // Fetch where to take the thumbnail
      $thumb_splice = $duration / 2;

      // Thumbnail proccess command
      $thumbCommand = $ffmpeg_path . ' '
      . '-i ' . escapeshellarg($outputPath) . ' '
      . '-f image2' . ' '
      . '-ss '. $thumb_splice . ' '
      . '-v 2' . ' '
      . '-y ' . escapeshellarg($thumbPath) . ' '
      . '2>&1'
      ;

      // Process thumbnail
      $thumbOutput = $output .
        $thumbCommand . PHP_EOL .
        shell_exec($thumbCommand);

      // Log thumb output
      if( $log ) {
        $log->log($thumbOutput, Zend_Log::INFO);
      }

      // Check output message for success
      $thumbSuccess = true;
      if( preg_match('/video:0kB/i', $thumbOutput) ) {
        $thumbSuccess = false;
      }

      // Resize thumbnail
      if( $thumbSuccess ) {
        $image = Engine_Image::factory();
        $image->open($thumbPath)
          ->resize(120, 240)
          ->write($thumbPath)
          ->destroy();
      }

      // Save video and thumbnail to storage system
      $params = array(
        'parent_id' => $gift->getIdentity(),
        'parent_type' => $gift->getType(),
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'storage_path' => $storageObject->store($outputPath)->storage_path
      );

      $db = $storageObject->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $storageObject->setFromArray($params);
        $storageObject->save();
        $db->commit();

      } catch( Exception $e ) {
        $db->rollBack();

        // delete the files from temp dir
        unlink($originalPath);
        unlink($outputPath);
        if( $thumbSuccess ) {
          unlink($thumbPath);
        }

        $gift->status = 7;
        $this->returnCredits($gift);
        $gift->save();
      }

      $gift->status = 1;
      $gift->save();
      $this->returnCredits($gift);
      $gift->sendVideoGift();
    }
  }

  protected function returnCredits($gift)
  {
    if ($gift->owner_id) {
      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      Engine_Api::_()->getItem('credit_balance', $gift->owner_id)->returnCredits($gift->credits);
      $gift->credits = $settings->getSetting('hegift.video.credits', 100);
      $gift->save();
    } else {
      return 0;
    }
  }
}
