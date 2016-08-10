<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 09.09.11 16:28 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Audios_Create extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    // Init form
    $this
      ->setTitle('Add Audios')
      ->setDescription('Choose audios from your computer to add to this product.')
      ->setAttrib('id',      'form-upload-audio')
      ->setAttrib('name',    'playlist_create')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Init file uploader
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
               ->addDecorator('FormFancyUpload')
               ->addDecorator('viewScript', array(
                 'viewScript' => '_FancyUploadAudio.tpl',
                 'placement'  => '',
                 ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Init hidden file IDs
    $this->addElement('Hidden', 'fancyuploadfileids');


    // Init submit 
    $this->addElement('Button', 'submit', array(
      'label' => 'Save audios',
      'type'  => 'submit',
    ));
  }

  public function clearUploads()
  {
    $this->getElement('fancyuploadfileids')->setValue('');
  }


  public function saveValues($product)
  {
    $values = $this->getValues();

    // get file_id list
    $file_ids = array();
    foreach (explode(' ', $values['fancyuploadfileids']) as $file_id) {
      $file_id = trim($file_id);
      if (!empty($file_id))
        $file_ids[] = $file_id;
    }

    // Attach songs (file_ids) to playlist
    if (!empty($file_ids)){
      $storage = Engine_Api::_()->getItemTable('storage_file');
      foreach ($file_ids as $file_id){
        $file = $storage->findRow($file_id);
        if (!$file){
          continue ;
        }

        $audio = $product->addAudio($file);

        $file->setFromArray(array(
          'parent_type' => 'store_audio',
          'parent_id' => $audio->getIdentity(),
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        ));
        $file->save();
      }
    }

    return $product;
  }
}