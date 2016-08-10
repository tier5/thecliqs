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

class Store_Form_Admin_Locations_Remove extends Engine_Form
{
  /**
   * @var $_location Engine_Db_Table_Row
   */
  protected $_location;

  public function setLocation(Engine_Db_Table_Row $location)
  {
      $this->_location = $location;
  }

  public function init()
  {
    $this->setTitle('STORE_Remove Location')
      ->setDescription(Zend_Registry::get('Zend_View')->translate("STORE_Are you sure you want to remove %s from the list including all its sub-locations?", $this->_location->location))
      ->setAttrib('class', 'global_form_popup')
    ;

    $this->addElement('hidden', 'location_id', array(
      'value' => $this->_location->location_id,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'STORE_Remove Location',
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
  }
}
