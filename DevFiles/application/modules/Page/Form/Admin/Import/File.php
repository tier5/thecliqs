<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: File.php  16.12.11 16:41 ulan t $
 * @author     Ulan T
 */

class Page_Form_Admin_Import_File extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Admin_Import_Title');
    $this->setDescription('Admin_Import_Description');
    $this->setAttrib('enctype', 'multipart/form-data');
    $this->addElement('File', 'import_file', array(
      'label' => 'File',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'required' => true,
      'validators' => array(
        array('Count', false, 1),
        array('Extension', false, 'csv'),
      )
    ));

    $this->addElement('Radio', 'seperator', array(
      'label' => 'Seperator',
      'multiOptions' => array(
        ',' => 'Comma (,)',
        ';' => 'Semicolon (;)'
      ),
      'value' => ','
    ));

    $this->addElement('Checkbox', 'marker', array(
      'description' => "PAGE_IMPORT_MARKER_LABEL",
      'label' => 'PAGE_IMPORT_MARKER_DESC',
      'value' => 1,
    ));

    $this->addElement('Checkbox', 'activity', array(
      'description' => "PAGE_IMPORT_ACTIVITY_LABEL",
      'label' => 'PAGE_IMPORT_ACTIVITY_DESC',
      'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
    ));
  }
}