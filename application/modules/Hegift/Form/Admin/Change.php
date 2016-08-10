<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Change.php 21.04.12 12:44 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Change extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Change Type of Gift')
      ->setDescription('HEGIFT_VIEWS_SCRIPTS_ADMININDEX_CHANGE_TYPE_DESCRIPTION')
      ->setAttrib('class', 'global_form_box')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $types = array(
      '1' => 'Photo Gift',
      '2' => 'Audio Gift'
    );

    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if (!empty($ffmpeg_path)) {
      $types['3'] = 'Video Gift';
    }

    $this->addElement('Select', 'type', array(
      'label' => 'Type of Gift',
      'multiOptions' => $types,
      'allowEmpty' => false,
      'required' => true
    ));

    // Element: execute
    $this->addElement('Button', 'change', array(
      'label' => 'Change Type',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}