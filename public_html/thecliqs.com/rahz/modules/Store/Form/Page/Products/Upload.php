<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Upload.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Page_Products_Upload extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('STORE_Edit Product')
      ->setDescription('You can add photos to your product here.')
      ->setAttrib('id','form-upload')
      ->setAttrib('enctype','multipart/form-data')
      ;

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');


    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Add Photos',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'store', 'controller'=>'products'), 'admin_default', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}
