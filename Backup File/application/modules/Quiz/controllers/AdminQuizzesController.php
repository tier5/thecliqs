<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminQuizzesController.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Quiz_AdminQuizzesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('quiz_admin_main', array(), 'quiz_admin_main_quizzes');
    
    $this->view->page = $page = $this->_getParam('page', 1);
    
    $this->view->paginator = Engine_Api::_()->quiz()->getQuizzesPaginator(array('orderby' => 'quiz_id'));
  
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function approveAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
    {
      return;
    }

    $quiz_id = $this->_getParam('quiz_id');

  	//GET USER
  	$quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

   	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $quiz->approved = 1 - $quiz->approved;
      $quiz->save();
 			$db->commit();
	 	}
   	catch( Exception $e ) {
    	$db->rollBack();
     	throw $e;
   	}
  	$this->_redirect("admin/quiz/quizzes/index/page/".$this->_getParam('page'));
  }

  public function deleteAction()
  {
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->quiz_id = $id;
    
    // Check post
    if ($this->getRequest()->isPost()) {
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $quiz = Engine_Api::_()->getItem('quiz', $id);
        // delete the quiz into the database
        $quiz->delete();

        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-quizzes/delete.tpl');
  }
}