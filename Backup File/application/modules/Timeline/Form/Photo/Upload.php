<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Upload.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Timeline_Form_Photo_Upload extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'UploadTimelinePhoto');


    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH . '/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange' => 'javascript:uploadTimelinePhoto();'
    ));
  }
}