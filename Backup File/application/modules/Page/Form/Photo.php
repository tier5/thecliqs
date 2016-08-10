<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photo.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Photo extends Engine_Form
{
	public function init()
  {
    $this->setTitle('Change Photo')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setDescription('Edit your Page photo.')
      ->setAttrib('name', 'EditPagePhoto')
    ;

    $this->addElement('Image', 'current', array(
      'label' => 'Current Photo',
      'ignore' => true,
      'decorators' => array(
        array('ViewScript',
          array(
            'viewScript' => '_formEditImage.tpl',
            'class'      => 'form element',
            'testing' => 'testing'
          )
        )
      )
    ));

    Engine_Form::addDefaultDecorators($this->current);

    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange'=>'javascript:uploadPhoto();'
    ));
    
    $this->addElement('Hidden', 'coordinates', array(
      'filters' => array(
        'HtmlEntities',
      )
    ));

    $this->addElement('Cancel', 'remove', array(
      'label' => 'delete photo',
      'prependText' => ' or ',
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'delete-photo',
      )),
      'onclick' => null,
      'class' => 'smoothbox',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
    $this->addDisplayGroup(array('Filedata', 'remove'), 'buttons');
  }
}
