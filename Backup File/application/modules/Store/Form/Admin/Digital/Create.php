<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 21.09.11 16:05 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Digital_Create extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Upload File')
      ->setDescription('Choose file from your computer to add to this product.')
      ->setAttrib('id',      'form-upload-file')
      ->setAttrib('name',    'file_create')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Init file uploader
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
               ->addDecorator('FormFancyUpload')
               ->addDecorator('viewScript', array(
                 'viewScript' => '_FancyUploadFile.tpl',
                 'placement'  => '',
                 ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Init hidden file IDs
    $this->addElement('Hidden', 'fancyuploadfileids');


    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save File',
      'type'  => 'submit',
    ));
  }

  public function saveValues($product)
  {
    $values = $this->getValues();

    $file_id = $values['fancyuploadfileids'];
    $file_id = trim($file_id);

    if (!empty($file_id)) {
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $file = $storage->findRow($file_id);
      if (!$file){
        return 'Already exists';
      }

      $file->setFromArray(array(
        'parent_type' => 'store_product',
        'parent_id' => $product->getIdentity(),
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      ));
      $file->save();
    }

    return $product;
  }
}

