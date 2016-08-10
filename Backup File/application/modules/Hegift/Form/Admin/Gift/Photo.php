<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Photo.php 06.02.12 16:32 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Gift_Photo extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Manage Gift Photo')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'ManageGiftPhoto')
      ->setDescription('You can manage photo, edit thumbnail, or upload other photo')
    ;

    $this->addElement('Image', 'current', array(
      'label' => 'Current Photo',
      'ignore' => true,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formManageGiftPhoto.tpl',
        'class'      => 'form element'
      )))
    ));
    Engine_Form::addDefaultDecorators($this->current);

    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('Count', false, 1),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange'=>'javascript:uploadGiftPhoto();'
    ));

    $this->addElement('Hidden', 'coordinates', array(
      'filters' => array(
        'HtmlEntities',
      )
    ));
  }
}
