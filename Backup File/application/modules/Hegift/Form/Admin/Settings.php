<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 20.02.12 10:58 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->setTitle('Global Settings')
      ->setDescription('HEGIFT_Global Settings Description');

    $this->addElement('Text', 'hegift_photo_credits', array(
      'label' => 'Photo Gift Price',
      'description' => 'This price for "Create Own Photo Gift"',
      'value' => $settings->getSetting('hegift.photo.credits', 50)
    ));

    $this->addElement('Text', 'hegift_audio_credits', array(
      'label' => 'Audio Gift Price',
      'description' => 'This price for "Create Own Audio Gift"',
      'value' => $settings->getSetting('hegift.audio.credits', 80)
    ));

    $this->addElement('Text', 'hegift_video_credits', array(
      'label' => 'Video Gift Price',
      'description' => 'This price for "Create Own Video Gift"',
      'value' => $settings->getSetting('hegift.video.credits', 100)
    ));

    $this->addElement('Text', 'gift_expiration_date', array(
      'label' => 'Quantity of days',
      'description' => 'All gifts in the temporary being removed after the creation',
      'value' => $settings->getSetting('gift.expiration.date', 2)
    ));

    $this->addElement('Text', 'video_ffmpeg_path', array(
      'label' => 'Path to FFMPEG',
      'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ffmpeg.path', '')
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
