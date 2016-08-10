<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 11.10.12
 * Time: 9:50
 * To change this template use File | Settings | File Templates.
 */

class Timeline_Form_Admin_Settings_ThumbIcons extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'EditPhoto');

    $this->addElement('Image', 'current', array(
      'label' => 'Current Photo',
      'ignore' => true,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formEditIcon.tpl',
        //'viewScript' => '_formImageCrop.tpl',
        'class'      => 'form element',
        'testing' => 'testing'
      )))
    ));
    Engine_Form::addDefaultDecorators($this->current);

    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH .'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange'=>'javascript:uploadCoverPhoto();'
    ));

    $this->addElement('Hidden', 'coordinates', array(
      'filters' => array(
        'HtmlEntities',
      )
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'Save Photo',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

  }
}