<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminFieldsController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_moduleName = 'Page';

  protected $_fieldType = 'page';

  protected $_requireProfileType = true;

  protected $_fieldTypeInfo = array();

  public function init()
  {
    $this->view->option_id = $option_id = $this->_getParam('option_id');


    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_fields');

    parent::init();
  }

  public function indexAction()
  {
    parent::indexAction();

    $rows = Engine_Api::_()->getItemApi('page')->getSetCategories();
    $set = array();

    foreach($rows as $row) {
      if(!isset($set[$row['set_id']])) {
        $set[$row['set_id']] = array('id' => $row['set_id'], 'caption' => $row['set'], 'items' => array());
      }
      $set[$row['set_id']]['items'][] = $row;
    }
    $this->view->set = $set;



    $option_id = $this->_getParam('option_id');
    if(!$option_id)
    {
      $option_id = Engine_Api::_()->getDbTable('fieldsOptions','page')->getFirstOption()->option_id;
    }
    $this->view->option_id = $option_id;
    $this->view->term = $term = Engine_Api::_()->getDbTable('terms','page')->getTerm($option_id);

    if($term)
    {
      $form = new Page_Form_Admin_EditTerm();
      $form->populate($term->toArray());
    }
    else
    {
      $form = new Page_Form_Admin_CreateTerm();
      $form->enabled->setValue('1');
    }
    $this->view->form = $form;

    // If not post or form not valid, return
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }
    $values = array_merge($form->getValues(),array(
       'option_id' => $option_id,
    ));
    if($term)
    {
      //Edit terms
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $terms = Engine_Api::_()->getItemTable('term')->getTerm($option_id);

        $terms->setFromArray($values);
        $terms->save();
        $db->commit();
      }
      catch(Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
    }
    else
    {
      //Add terms
      $table = Engine_Api::_()->getItemTable('term');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try
      {
        $terms = $table->createRow();
        $terms->setFromArray($values);
        $terms->save();
        $db->commit();
      }
      catch(Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
    }
    $this->view->term = $term = Engine_Api::_()->getDbTable('terms','page')->getTerm($option_id);
    if($term)
    {
      $form = new Page_Form_Admin_EditTerm();
      $form->populate($term->toArray());
    }
    else
    {
      $form = new Page_Form_Admin_CreateTerm();
    }
    $this->view->form = $form;
  }

  public function typeCreateAction()
  {

    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Validate input
    if( $field->type !== 'profile_type' ) {
      throw new Exception(sprintf('invalid input, type is "%s", expected "profile_type"', $field->type));
    }

    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Type();

    $db = Engine_Db_Table::getDefaultAdapter();
    $page_type_result = $db->select('option_id, label')
      ->from('engine4_page_fields_options')
      ->where('field_id = 1')
      ->query()
      ->fetchAll();
    $page_type_count = count($page_type_result);
    $page_type_array = array( 'null' => 'No, Create Blank Profile Type' );
    for( $i = 0; $i < $page_type_count; $i++) {
      $page_type_array[$page_type_result[$i]['option_id']] = $page_type_result[$i]['label'];
    }


    $form->duplicate->setMultiOptions($page_type_array);
    $form->duplicate->setLabel('Duplicate Existing Page Type?');
    $form->label->setLabel('Page Type Label');
    $form->submit->setLabel('Add Page Type');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Create New Profile Type from Duplicate of Existing
    if( $form->getValue('duplicate') != 'null' ) {
      // Create New Option in engine4_user_fields_options
      $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
        'field_id' => $field->field_id,
        'label' => $form->getValue('label'),
      ));
      // Get New Option ID
      $db = Engine_Db_Table::getDefaultAdapter();
      $new_option_id = $db->select('option_id')
        ->from('engine4_page_fields_options')
        ->where('label = ?', $form->getValue('label'))
        ->query()
        ->fetchColumn();
      // Get list of Field IDs From Duplicated member Type
      $field_map_array =  $db->select()
        ->from('engine4_page_fields_maps')
        ->where('option_id = ?', $form->getValue('duplicate'))
        ->query()
        ->fetchAll();

      $field_map_array_count = count($field_map_array);
      // Check if the Member type is blank
      if($field_map_array_count == 0) {
        // Create new blank option
        $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
          'field_id' => $field->field_id,
          'label' => $form->getValue('label'),
        ));
        $this->view->option = $option->toArray();
        $this->view->form = null;
        return;
      }

      for($c = 0; $c < $field_map_array_count; $c++) {
        $child_id_array[] = $field_map_array[$c]['child_id'];
      }
      unset($c);

      $field_meta_array = $db->select()
        ->from('engine4_page_fields_meta')
        ->where('field_id IN (' . implode(', ', $child_id_array) . ')')
        ->query()
        ->fetchAll();

      // Copy each row
      for($c = 0; $c < $field_map_array_count; $c++){
        $db->insert('engine4_page_fields_meta',
          array(
            'type'  => $field_meta_array[$c]['type'],
            'label' => $field_meta_array[$c]['label'],
            'description' => $field_meta_array[$c]['description'],
            'alias' => $field_meta_array[$c]['alias'],
            'required'  => $field_meta_array[$c]['required'],
            'display' => $field_meta_array[$c]['display'],
            'publish' => $field_meta_array[$c]['publish'],
            'search' => $field_meta_array[$c]['search'],
            'show' => $field_meta_array[$c]['show'],
            'order' => $field_meta_array[$c]['order'],
            'config' => $field_meta_array[$c]['config'],
            'validators' => $field_meta_array[$c]['validators'],
            'filters' => $field_meta_array[$c]['filters'],
            'style' => $field_meta_array[$c]['style'],
            'error' => $field_meta_array[$c]['error'],
          )
        );
        // Add original field_id to array => new field_id to new corresponding row
        $child_id_reference[$field_meta_array[$c]['field_id']] = $db->lastInsertId();
      }
      unset($c);

      // Create new map from array using new field_id values and new Option ID
      $map_count = count($field_map_array);
      for($i = 0; $i < $map_count; $i++) {
        $db->insert('engine4_page_fields_maps',
          array(
            'field_id' => $field_map_array[$i]['field_id'],
            'option_id' => $new_option_id,
            'child_id' => $child_id_reference[$field_map_array[$i]['child_id']],
            'order' => $field_map_array[$i]['order'],
          )
        );
      }

    }
    else{
      // Create new blank option
      $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
        'field_id' => $field->field_id,
        'label' => $form->getValue('label'),
      ));
    }
    $this->view->option = $option->toArray();
    $this->view->form = null;

    // Get data
    $mapData = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType);
    $optionData = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType);

    // Flush cache
    $mapData->getTable()->flushCache();
    $metaData->getTable()->flushCache();
    $optionData->getTable()->flushCache();

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('typeCreate', array(
      'option' => $this->view->option,
    ));
  }

  public function typeEditAction()
  {
    parent::typeEditAction();

    if( !$this->getRequest()->isPost() ) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $page_type_result = $db->select('option_id, label')
        ->from('engine4_page_fields_options')
        ->where('field_id = 1')
        ->query()
        ->fetchAll();
      $page_type_count = count($page_type_result);
      $page_type_array = array( 'null' => 'No, Create Blank Profile Type' );
      for( $i = 0; $i < $page_type_count; $i++) {
        $page_type_array[$page_type_result[$i]['option_id']] = $page_type_result[$i]['label'];
      }


      $this->view->form->duplicate->setMultiOptions($page_type_array);
      $this->view->form->duplicate->setLabel('Duplicate Existing Page Type?');
      $this->view->form->label->setLabel('Page Type Label');
      $this->view->form->submit->setLabel('Add Page Type');
      return;
    }
  }

  public function fieldCreateAction()
  {
    parent::fieldCreateAction();

    if (!$this->getRequest()->isPost()) {
      $this->modifyFieldForm();
    }
  }

  public function fieldEditAction()
  {
    parent::fieldEditAction();

    if (!$this->getRequest()->isPost()){
      $this->modifyFieldForm();
    }
  }

  public function typeDeleteAction()
  {
    $option_id = $this->_getParam('option_id');
    if ($option_id == 1){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $db = $table->getAdapter();
    $prefix = $table->getTablePrefix();

    $db->update($prefix.'page_fields_values', array('value' => 1), "value = $option_id AND field_id = 1");

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('typeDelete', array(
      'option_id' => $option_id
    ));

    parent::typeDeleteAction();
  }

  protected function modifyFieldForm()
  {
    $form = $this->view->form;
    $form->getElement('search')->setmultiOptions(array('Hide on Browse Pages', 'Show on Browse Pages'));
    $form->getElement('display')->setmultiOptions(array('Hide on Page Profile', 'Show on Page Profile'));

    $form->setTitle('Edit Page Field');
    $form->getElement('type')->setLabel('Page Type');
    $form->getElement('label')->setLabel('Field Label');
//    $options = $form->getElement('type')->getmultiOptions();
//    unset($options['Specific']);
//    $form->getElement('type')->setmultiOptions($options);
  }

  public function headingEditAction()
  {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Heading();
    $form->display->setmultiOptions(array('Hide on Browse Pages', 'Show on Browse Pages'));
    $form->submit->setLabel('Edit Heading');

    // Get sync notice
    $linkCount = count(Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)
        ->getRowsMatching('child_id', $field->field_id));
    if( $linkCount >= 2 ) {
      $form->addNotice($this->view->translate(array(
        'This question is synced. Changes you make here will be applied in %1$s other place.',
        'This question is synced. Changes you make here will be applied in %1$s other places.',
        $linkCount - 1), $this->view->locale()->toNumber($linkCount - 1)));
    }

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $fieldFixed = $field->toArray();
      $fieldFixed['show'] = $field->config['show'];
      $form->populate($fieldFixed);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    Engine_Api::_()->fields()->editField($this->_fieldType, $field, $form->getValues());

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }

  public function headingCreateAction()
  {
    $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);

    // Create form
    $this->view->form = $form = new Fields_Form_Admin_Heading();
    $form->display->setmultiOptions(array('Hide on Browse Pages', 'Show on Browse Pages'));

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
      'option_id' => $option->option_id,
      'type' => 'heading',
      'display' => 1
    ), $form->getValues()));

    $this->view->status = true;
    $this->view->field = $field->toArray();
    $this->view->option = $option->toArray();
    $this->view->form = null;

    // Re-render all maps that have this field as a parent or child
    $maps = array_merge(
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
      Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
    );
    $html = array();
    foreach( $maps as $map ) {
      $html[$map->getKey()] = $this->view->adminFieldMeta($map);
    }
    $this->view->htmlArr = $html;
  }
}