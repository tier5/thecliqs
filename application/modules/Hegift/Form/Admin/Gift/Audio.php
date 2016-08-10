<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Audio.php 22.02.12 18:21 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Gift_Audio extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Change Audio for Gift')
      ->setDescription('Choose audio from your computer.')
      ->setAttrib('id',      'form-upload')
      ->setAttrib('class', 'global_form_box hegift_create_gift_form')
      ->setAttrib('name',    'gift_create')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    // Init file uploader
    $fancyUpload = new Engine_Form_Element_FancyUpload('audio');
    $fancyUpload->clearDecorators()
               ->addDecorator('FormFancyUpload')
               ->addDecorator('viewScript', array(
                 'viewScript' => '_FancyUploadAudio.tpl',
                 'placement'  => '',
                 ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Init submit
    $this->addElement('Button', 'upload', array(
      'label' => 'Change Audio',
      'type'  => 'submit',
    ));
  }
}
