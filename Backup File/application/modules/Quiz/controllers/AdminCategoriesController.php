<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminCategoriesController.php 2010-07-02 19:25 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Quiz_AdminCategoriesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('quiz_admin_main', array(), 'quiz_admin_main_categories');

    $this->view->categories = Engine_Api::_()->quiz()->getCategories();
  }
  
  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Quiz_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    // Check post
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('categories', 'quiz');

        // insert the quiz into the database
        $row = $table->createRow();
        $row->category_name = $values["label"];
        $row->save();

        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-categories/form.tpl');
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->category_id = $id;

    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $row = Engine_Api::_()->quiz()->getCategory($id);
        // delete the quiz into the database
        $row->delete();

        // go through logs and see which quiz used this $id and set it to ZERO

        $table = Engine_Api::_()->getDbtable('quizs', 'quiz');
        $select = $table->select()->where('category_id = ?', $id);
        $quizzes = $table->fetchAll($select);

        // create permissions
        foreach ($quizzes as $quiz) {
          //this is not working
          $quiz->category_id = 0;
          $quiz->save();
        }

        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-categories/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Quiz_Form_Admin_Category();

    // Check post
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // edit category in the database
        // Transaction
        $row = Engine_Api::_()->quiz()->getCategory($values["id"]);

        $row->category_name = $values["label"];
        $row->save();
        
        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }

    // Must have an id
    if (!($id = $this->_getParam('id'))) {
      die('No identifier specified');
    }

    // Generate and assign form
    $category = Engine_Api::_()->quiz()->getCategory($id);

    $form->setField($category);

    // Output
    $this->renderScript('admin-categories/form.tpl');
  }
}