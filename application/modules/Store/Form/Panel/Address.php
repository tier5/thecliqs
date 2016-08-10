<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Address.php 4/18/12 5:25 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Panel_Address extends Engine_Form
{
  /**
   * @var $_details Store_Model_DbTable_Details
   */
  protected $_details;

  /**
   * @var $_viewer User_Model_User
   */
  protected $_viewer;

  /**
   * @var $_table Store_Model_DbTable_Locations
   */
  protected $_table;

  public function init()
  {
    // Init form
    $this
      ->setTitle('STORE_Default Shipping Details')
      ->setDescription('STORE_MY_PANEL_DEFAULT_SHIPPING_DETAILS');

    /**
     * Get protected properties
     *
     * @var $details Store_Model_DbTable_Details
     * @var $viewer  User_Model_User
     */
    $this->_details = $details = Engine_Api::_()->getDbtable('details', 'store');
    $this->_viewer  = $viewer = Engine_Api::_()->user()->getViewer();

    $this->addElement('text', 'first_name', array(
      'label'    => 'First Name',
      'required' => true,
      'value'    => $this->_getDetail('first_name'),
      'validators' => array(
        new Zend_Validate_Alpha(true),
        new Zend_Validate_StringLength(2)
      )
    ));
    $this->addElement('text', 'last_name', array(
      'label'    => 'Last Name',
      'required' => true,
      'value'    => $this->_getDetail('last_name'),
      'validators' => array(
        new Zend_Validate_Alpha(true),
        new Zend_Validate_StringLength(2)
      )
    ));
    $this->addElement('text', 'email', array(
      'label'    => 'Email',
      'required' => true,
      'value'    => $this->_getDetail('email'),
      'validators' => array(
        array('EmailAddress', true),
      )
    ));
    $this->addElement('text', 'phone', array(
      'label'    => 'Phone',
      'required' => true,
      'value'    => $this->_getDetail('phone'),
    ));
    $this->addElement('text', 'phone_extension', array(
      'label'    => 'Phone Ext',
      'required' => false,
      'value'    => $this->_getDetail('phone_extension'),
    ));

    /**
     * Set Countries
     *
     * @var $table Store_Model_DbTable_Locations
     */
    $this->_table = $table = Engine_Api::_()->getDbTable('locations', 'store');

    $select = $table->select()
      ->from(array('l1' => $table->info('name')), array('l1.location_id', 'l1.location'))
      ->from(array('l2' => $table->info('name')), array())
      ->where('l2.parent_id = l1.location_id')
      ->group('l1.location')
      ->order('l1.location ASC');

    $countries = array();
    foreach ($table->fetchAll($select) as $location) {
      $countries[$location->location_id] = $location->location;
    }

    $country = new Engine_Form_Element_Select('country', array(
      'label'        => 'Country',
      'required'     => true,
      'multiOptions' => $countries,
      "onchange"     => "getLocations($(this));"
    ));

    if (($country_value = (int)$this->_getDetail('country')) && array_key_exists($country_value, $countries)) {
      $country->setValue($country_value);
    }

    if (!$country->getValue()) {
      $ids = array_keys($countries);
      $country->setValue($ids[0]);
    }

    $this->addElement($country);


    //Set states/regions
    $states = array();
    if ((int)$country->getValue()) {
      $select->reset('where');
      $select = $select->where('l1.parent_id = ?', $country->getValue());
      foreach ($table->fetchAll($select) as $st) {
        $states[$st->location_id] = $st->location;
      }
    }

    $state = new Engine_Form_Element_Select('state', array(
      'label'        => 'STORE_State/Region',
      'required'     => false,
      'multiOptions' => $states,
    ));

    if (($state_value = (int)$this->_getDetail('state')) && array_key_exists($state_value, $states)) {
      $state->setValue($state_value);
    }

    $this->addElement($state);

    $this->addElement('text', 'zip', array(
      'label'    => 'Zip Code',
      'required' => true,
      'value'    => $this->_getDetail('zip'),
    ));

    $this->addElement('text', 'city', array(
      'label'    => 'City',
      'required' => true,
      'value'    => $this->_getDetail('city'),
      'validators' => array(
        new Zend_Validate_StringLength(2)
      )
    ));

    $this->addElement('text', 'address_line_1', array(
      'label'    => 'STORE_Address Line',
      'required' => true,
      'value'    => $this->_getDetail('address_line_1'),
      'validators' => array(
        new Zend_Validate_StringLength(5)
      )
    ));
    $this->addElement('text', 'address_line_2', array(
      'label'    => 'STORE_Address Line 2',
      'required' => false,
      'value'    => $this->_getDetail('address_line_2'),
      'validators' => array(
        new Zend_Validate_StringLength(5)
      )
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label'      => 'Save Settings',
      'type'       => 'submit',
      'decorators' => array('ViewHelper', 'DivDivDivWrapper')
    ));
  }

  protected function _getDetail($name)
  {
    if (($value = $this->_details->getDetail($this->_viewer, $name))) {
      return $value;
    }

    if (isset($this->_viewer->$name)) {
      return $this->$name;
    }

    if ($name == 'first_name') {
      $names = explode(' ', $this->_viewer->displayname);
      return (array_key_exists(0, $names)) ? $names[0] : '';
    }

    if ($name == 'last_name') {
      $names = explode(' ', $this->_viewer->displayname);
      return (array_key_exists(1, $names)) ? $names[1] : '';
    }

    return false;
  }

  public function isValid($data)
  {
    if (array_key_exists('country', $data) && $data['country']) {
      $countryValidate = new Zend_Validate_Db_RecordExists($this->_table->info('name'), 'location_id', 'parent_id = 0');
      $this->getElement('country')->addValidator($countryValidate);

      if (array_key_exists('state', $data) && $data['state']) {

        $states = array();
        $select = $this->_table->select()
          ->from($this->_table, array('location_id', 'location'))
          ->where('parent_id = ?', (int)$data['country'])
          ->order('location_id DESC');

        foreach ($this->_table->fetchAll($select) as $st) {
          $states[$st->location_id] = $st->location;
        }

        $this->getElement('state')->setMultiOptions($states);
        $stateValidate = new Zend_Validate_Db_RecordExists($this->_table->info('name'), 'location_id', 'parent_id = ' . (int)$data['country']);
        $this->getElement('state')->addValidator($stateValidate);
      }
    }

    return parent::isValid($data);
  }
}

