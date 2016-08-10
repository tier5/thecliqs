<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminSettingsController.php 20.02.12 10:54 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_admin_main', array(), 'hegift_admin_main_settings');
  }

  public function indexAction()
  {
    // Check ffmpeg path for correctness
    if( function_exists('exec') ) {
      $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;

      $output = null;
      $return = null;
      if( !empty($ffmpeg_path) ) {
        exec($ffmpeg_path . ' -version', $output, $return);
      }
      // Try to auto-guess ffmpeg path if it is not set correctly
      $ffmpeg_path_original = $ffmpeg_path;
      if( empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false ) {
        $ffmpeg_path = null;
        // Windows
        if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
          // @todo
        }
        // Not windows
        else {
          $output = null;
          $return = null;
          @exec('which ffmpeg', $output, $return);
          if( 0 == $return ) {
            $ffmpeg_path = array_shift($output);
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version 2>&1', $output, $return);
            //if( 0 != $return ) {
            //  $ffmpeg_path = null;
            //}
            if ($output == null) {
                $ffmpeg_path = null;
            }
          }
        }
      }
      if( $ffmpeg_path != $ffmpeg_path_original ) {
        Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path = $ffmpeg_path;
      }
    }

    // Make form
    $this->view->form = $form = new Hegift_Form_Admin_Settings();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    // Check ffmpeg path
    if( !empty($values['video_ffmpeg_path']) ) {
      if( function_exists('exec') ) {
        $ffmpeg_path = $values['video_ffmpeg_path'];
        $output = null;
        $return = null;
        exec($ffmpeg_path . ' -version', $output, $return);
        if( $return > 0 && $output != NULL ) {
          $form->video_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
          $values['video_ffmpeg_path'] = '';
        }
      } else {
        $form->video_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
        $values['video_ffmpeg_path'] = '';
      }
    }

    // Okay, save
    foreach( $values as $key => $value ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }
}
