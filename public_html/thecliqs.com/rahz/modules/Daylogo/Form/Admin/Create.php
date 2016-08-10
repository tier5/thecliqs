<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2012-08-16 16:33 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Daylogo_Form_Admin_Create extends Engine_Form
{

  public function init()
  {
    $module_path = Engine_Api::_()->getModuleBootstrap('daylogo')->getModulePath();
    $this->addPrefixPath('Engine_Form_Element_', $module_path . '/Form/Element/', 'element');
    // Init form
    $this
      ->setTitle('DAYLOGO_CREATE_TITLE')
      ->setDescription('DAYLOGO_CREATE_DESCRIPTION')
      ->setAttrib('onsubmit', 'return Daylogo.formSubmit(this);')
      ->setAttrib('enctype','multipart/form-data')
      ->setAttrib('id', 'daylogo-form');

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'DAYLOGO_Logo Title',
      'required' => true,
      'allowEmpty' => false,
      'autofocus' => 'autofocus',
    ));

    $fancyUpload2 = new Engine_Form_Element_FancyUpload('logo_photo');
    $fancyUpload2->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
      'viewScript' => 'fancy_upload_photo.tpl',
      'placement' => '',
    ));

    Engine_Form::addDefaultDecorators($fancyUpload2);
    $this->addElement($fancyUpload2);

    $this->addElement('Logodatepicker', 'starttime', array(
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->starttime->setLabel('DAYLOGO_FORM_STARTDATE');

    $this->addElement('Logodatepicker', 'endtime', array(
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->endtime->setLabel('DAYLOGO_FORM_ENDDATE');

    $this->addElement('Button', 'submit', array(
      'label' => 'DAYLOGO_Save_Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'DAYLOGO_Cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
      'onclick' => 'return Daylogo.formCancel();'
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
    $this->addElement('Hidden', 'id', array('value' => 0));
  }
}