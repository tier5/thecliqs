<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Delete.php 3/22/12 5:36 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Locations_Delete extends Engine_Form
{
  /**
   * @var $_location Store_Model_Location
   */
  protected $_location;

  public function setLocation(Store_Model_Location $location)
  {
    $this->_location = $location;
  }

  public function init()
  {
    $this->setTitle('STORE_Delete Location')
      ->setDescription(Zend_Registry::get('Zend_View')->translate('STORE_Are you sure you want to delete %s including all its children?', $this->_location->getTitle()))
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;

    $this->addElement('hidden', 'location_id', array(

    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'STORE_Delete Location',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}
