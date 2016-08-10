<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 27.07.12
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */

class Page_Form_Admin_Import_Configure extends Engine_Form
{
  protected $_options;

  public function __construct( $options = array() )
  {
    $this->_options = $options;

    parent::__construct();
  }

  public function setOptions($options = array())
  {
    $this->_options = $options;
  }

  public function getOptions()
  {
    return $this->_options;
  }

  public function init()
  {
    $this->setTitle('Admin_Import_Configure_Title');
    $this->setDescription('Admin_Import_Configure_Description');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save-file', 'controller' => 'import', 'module' => 'page'), 'admin_default'));
    $this->setAttrib('enctype','multipart/form-data');
    $this->setAttrib('name', 'import_form');

    $this->addElement('Text', 'username', array(
      'Label' => 'Owner *',
      'description' => 'Choose Owner',
      'autocomplete' => 'off',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addCategories();

    $columns = array(
      'title' => 'Title *',
      'description' => 'Description',
      'country' => 'Country',
      'state' => 'State',
      'city' => 'City',
      'street' => 'Street',
      'website' => 'Website',
      'phone' => 'Phone',
      'creation_date' => 'Creation Date',
      'featured' => 'Featured',
      'sponsored' => 'Sponsored',
    );

    $value = 0;
    foreach( $columns as $key => $column ) {
      $this->addElement('Select', $key, array(
        'Label' => $column,
        'multiOptions' => $this->_options,
        'value' => $value
      ));

      if( count($this->_options) > $value )
        $value++;
    }

    $this->addElement('Hidden', 'file_id', array(
      'order' => 999
    ));

    $this->addElement('Hidden', 'user_id', array(
      'order' => 998
    ));

    $this->addElement('Hidden', 'seperator', array(
      'order' => 997
    ));

    $this->addElement('Hidden', 'marker', array(
      'order' => 996
    ));

    $this->addElement('Hidden', 'activity', array(
      'order' => 995
    ));

    $this->addElement('Button', 'submit_btn', array(
      'label' => 'Submit',
      //'type' => 'submit',
    ));

  }

  public function addCategories()
  {
    $multiOptions = array();
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('page', 'profile_type');
    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;

    $options = $profileTypeFields['profile_type']->getOptions();
    if( count($options) <= 1 ) {
      return;
    }

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('Select', 'option_id', array(
      'label' => 'Category *',
      'required' => true,
      'multiOptions' => $multiOptions,
    ));
  }
}