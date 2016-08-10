<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Edit.php 3/22/12 5:55 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Locations_Edit extends Engine_Form
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
    $this->setTitle('STORE_Edit Location')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
    ;

    $this->addElement('text', 'location', array(
      'Label'     => 'STORE_Location Name',
      'allowNull' => false,
      'required'  => true,
      'value'     => $this->_location->location,
    ));
    if (isset($this->_location->location_code)) {
      $this->addElement('text', 'location_code', array(
        'Label'     => 'STORE_Location Code',
        'allowNull' => false,
        'required'  => true,
        'value'     => $this->_location->location_code,
      ));
    }
    $this->addElement('text', 'shipping_amt', array(
      'label'     => 'STORE_Shipping Price',
      'allowNull' => true,
      'value'     => $this->_location->shipping_amt,
    ));
    $this->addElement('text', 'shipping_days', array(
      'label'     => 'STORE_Shipping Days',
      'allowNull' => true,
      'value'     => $this->_location->shipping_days,
    ));
    $this->addElement('hidden', 'location_id', array(
      'value' => $this->_location->location_id
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label'      => 'Save Changes',
      'type'       => 'submit',
      'ignore'     => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label'       => 'cancel',
      'link'        => true,
      'prependText' => ' or ',
      'href'        => '',
      'onclick'     => 'parent.Smoothbox.close();',
      'decorators'  => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
