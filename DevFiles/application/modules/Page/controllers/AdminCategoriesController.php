<?php
//todo: 8aa: to move logic to Model and Service

class Page_AdminCategoriesController extends Core_Controller_Action_Admin {
  public function init()
  {
    parent::init();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_categories');
  }

  public function indexAction()
  {
    $this->view->form = $form = $this->getNewSetForm();
    if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->populate($this->getRequest()->getPost());
      $values = $form->getValues();

      $prefix = Engine_Api::_()->getItemTable('page')->getTablePrefix();
      $q = "INSERT INTO `{$prefix}page_category_set` SET  `caption` = '{$values['setName']}'";
      Engine_Db_Table::getDefaultAdapter()->query($q);

      $this->_helper->redirector->gotoRoute();
    }

    $rows = Engine_Api::_()->getItemApi('page')->getSetCategories();
    $set = array();

    foreach($rows as $row) {
      if(!isset($set[$row['set_id']])) {
        $set[$row['set_id']] = array('id' => $row['set_id'], 'caption' => $row['set'], 'items' => array());
      }
      if(empty($row['cat_id']))
        continue;
      $set[$row['set_id']]['items'][] = $row;
    }

    $this->view->set = $set;
  }


  public function reorderAction(){
    $sets = $this->getRequest()->getParam('sets');
    if(empty($sets))
      exit('{error:1, message:"empty list"}');

    $prefix = Engine_Api::_()->getItemTable('page')->getTablePrefix();
    $q = "DELETE FROM {$prefix}page_category_set_category ";
    Engine_Db_Table::getDefaultAdapter()->query($q);
    if($this->getRequest()->getParam('isWithinSet') == 'false') {

      $cat_id = intval($this->getRequest()->getParam('cat_id'));
      $q = "SELECT `item_id` FROM `{$prefix}page_fields_search` where `profile_type` = '{$cat_id}'";
      $rows = Engine_Db_Table::getDefaultAdapter()->query($q)->fetchAll();
      $newSetId = $this->getRequest()->getParam('new_set_id');

      foreach($rows as $row) {
        $q = "UPDATE `{$prefix}page_pages` SET `set_id` = {$newSetId} WHERE `page_id`={$row['item_id']}";
        Engine_Db_Table::getDefaultAdapter()->query($q);
      }

    }

    foreach($sets as $setId=>$cats) {
      foreach($cats as $index=>$cat) {
        list( , $catId) = explode('-', $cat);
        $order = $index + 1;
        $q = "INSERT INTO `{$prefix}page_category_set_category` SET `set_id` = {$setId}, `cat_id` = {$catId}, `order` = {$order}";
        Engine_Db_Table::getDefaultAdapter()->query($q);
      }
    }

    exit();
  }

  public function renamesetAction()
  {
    $setId = $this->getRequest()->getParam('setId');
    $caption = $this->getRequest()->getParam('caption');
    $prefix = Engine_Api::_()->getItemTable('page')->getTablePrefix();
    $q = "UPDATE {$prefix}page_category_set SET `caption`='{$caption}' WHERE `id`={$setId}";

    Engine_Db_Table::getDefaultAdapter()->query($q);

    exit();
  }

  public function deletesetAction()
  {
    $setId = $this->getRequest()->getParam('setId');

    if($setId == 1)
      exit('{}');

    $dba = Engine_Db_Table::getDefaultAdapter();
    $prefix = Engine_Api::_()->getItemTable('page')->getTablePrefix();
    $q = "UPDATE `{$prefix}page_category_set_category` SET `set_id` = 1 WHERE `set_id` = {$setId}";
    $dba->query($q);
    $q = "DELETE FROM `{$prefix}page_category_set` WHERE id = {$setId}";
    $dba->query($q);
    $q = "UPDATE `{$prefix}page_pages` SET `set_id` = 1 WHERE `set_id` = {$setId}";
    $dba->query($q);

    $this->_helper->redirector->gotoRoute(array('action'=>'index', 'module'=>'page', 'controller'=>'categories'), 'admin_default', true);

  }

  private function getNewSetForm(){
    $form = new Engine_Form();
    $form->setMethod('post');
    $form->setAttrib('class', 'global_form_smoothbox');
    $form->setAttrib('id', 'new-set-form');
    $form->addElement('text', 'setName',
      array(
        'label' => 'Title *',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
          array('NotEmpty', true),
          array('StringLength', false, array(1, 64)),
        ),
        'filters' => array(
          'StripTags',
          new Engine_Filter_Censor(),
          new Engine_Filter_EnableLinks(),
        ),
      ));

    // Element: execute
    $form->addElement('Button', 'execute', array(
      'label' => 'Add Category Set',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $form->addElement('hidden', 'action', array('value'=>'add-new-category-set'));

    // Element: cancel
    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => 'javascript://',
      'onclick' => 'parent.Smoothbox.close();',
      'style' => 'color: #5BA1CD',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    // DisplayGroup: buttons
    $form->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));

    return $form;
  }
}