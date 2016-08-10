<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Create.php 27.02.12 16:13 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Create extends Engine_Form
{
  protected $type;

  public function __construct($options)
  {
    $this->type = $options['type'];
    parent::__construct($options);
  }

  public function init()
  {
    $this
      ->setTitle('Create Gift')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setDescription('DESC_Create '.ucfirst($this->type).' Gift and Send')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'gift_create')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('type' => $this->type)))
    ;

    $this->addElement('Text', 'title', array(
      'label' => 'Gift Title',
      'allowEmpty' => false,
      'required' => true,
    ));

    if ($this->type == 'audio' || $this->type == 'video') {
      $this->addElement('File', 'photo', array(
        'label' => 'Gift photo',
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    }

    $fancyUpload = new Engine_Form_Element_FancyUpload($this->type);
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => '_FancyUpload'.ucfirst($this->type).'.tpl',
        'placement'  => '',
      ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

     // Element: upload
    $this->addElement('Button', 'upload', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
