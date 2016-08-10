<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */


/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;
  protected $_quiz_navigation;
  protected $_quiz_tabs;
  protected $_quiz_options;
  protected $_quiz_results;
  protected $_quiz;

  public function init()
  {
    // only show to member_level if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('quiz', null, 'view')->isValid() ) return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('delete-result', 'json')
      ->addActionContext('delete-question', 'json')
      ->initContext();
  }

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Quiz_Form_Search();
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('quiz', null, 'create')->checkRequire();

    if (!$viewer->getIdentity()) {
      $form->removeElement('show');
    }

    $form->removeElement('publish');

    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->quiz()->getCategories();

    foreach ($categories as $category) {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }

    // Process form
    $form->isValid($this->getRequest()->getPost());
    $values = $form->getValues();

    // Populate form data
    if (!$values && $form->isValid($this->_getAllParams())) {
      $values = $this->_getAllParams();
      $form->populate($values);
    }

    // Do the show thingy
    if (@$values['show'] == 2) {

      // Get an array of friend ids to pass to getQuizzesPaginator
      $table = Engine_Api::_()->getDbtable('membership', 'user');
      $select = $table->select()
        ->from($table->info('name'), array('user_id'))
        ->where('resource_id = ?', $viewer->getIdentity())
        ->where('active = ?', true);

      $friends = $table->getAdapter()->fetchCol($select);
      $values['users'] = $friends;

    }

    $values['publish'] = 1;
    $values['approved'] = 1;

    $this->view->assign($values);
    $paginator = Engine_Api::_()->quiz()->getQuizzesPaginator($values);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $items_per_page = $settings->getSetting('quizzes.items.onpage', 10);

    $paginator->setItemCountPerPage($items_per_page);

    $this->view->browse_paginator = $paginator->setCurrentPageNumber( $values['page'] );

    if (!empty($values['category'])) {
      $this->view->categoryObject = Engine_Api::_()->quiz()->getCategory($values['category']);
    }

    $this->view->rateEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate');
    $this->view->theme_name = Engine_Api::_()->quiz()->getCurrentTheme();
  }

  public function createAction()
  {
    if (!$this->_helper->requireUser->isValid()) {
      return;
    }

    // check if user has create rights
    if (!$this->_helper->requireAuth()->setAuthParams('quiz', null, 'create')->isValid()) {
      return;
    }

    // Create navigation menu
    $this->view->navigation = $this->getNavigation();

    // Create form
    $this->view->form = $form = new Quiz_Form_Create();

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'quiz')->fetchAll();

    foreach ($categories as $row) {
      $form->category_id->addMultiOption($row->category_id, $row->category_name);
    }

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

    // Check method/data validity
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();

    $values['user_id'] = $viewer->getIdentity();

    $table = Engine_Api::_()->getDbtable('quizs', 'quiz');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create quiz
      $quiz = $table->createRow();
      $quiz->setFromArray($values);
      $quiz->approved = Engine_Api::_()->getApi('settings', 'core')->getSetting('quizzes.approve', 1);
      $quiz->save();

      // Set photo
      if (!empty($values['photo'])) {
        $quiz->setPhoto($form->photo);
      }

      // Process privacy
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

      $auth = Engine_Api::_()->authorization()->context;

      $auth_view = ($values['auth_view']) ? $values['auth_view'] : 'everyone';
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($quiz, $role, 'view', ($i <= $viewMax));
      }

      $auth_comment = ($values['auth_comment']) ? $values['auth_comment'] : 'everyone';
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($quiz, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $quiz->tags()->addTagMaps($viewer, $tags);

      // Commit
      $db->commit();

      $urlOptions = array('action' => 'create-result', 'quiz_id' => $quiz->getIdentity());

      // Redirect
      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_specific', true);
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('quiz_The image you selected was too large.'));
    }

    catch (Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function editAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    $quiz_id = $this->_getParam('quiz_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->quiz = $this->_quiz =  $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if ($quiz && !Engine_Api::_()->core()->hasSubject('quiz')) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($viewer->getIdentity() != $quiz->user_id && !$canCreate) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateQuizNavigation('edit');

    $this->view->form = $form = new Quiz_Form_Edit();

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'quiz')->fetchAll();
    foreach ($categories as $row) {
      $form->category_id->addMultiOption($row->category_id, $row->category_name);
    }

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

    if (!$this->getRequest()->isPost() || $this->_getParam('saved'))
    {
      // prepare tags
      $quizTags = $quiz->tags()->getTagMaps();

      $tagString = '';
      foreach ($quizTags as $tagmap)
      {
        if ($tagString !== '') {
          $tagString .= ', ';
        }

        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

      $form->populate($quiz->toArray());

      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

      foreach ($roles as $role)
      {
        if (1 === $auth->isAllowed($quiz, $role, 'view')) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($quiz, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
      }

      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['tags']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      // Set quiz info
      $quiz->setFromArray($values);
      $quiz->modified_date = new Zend_Db_Expr('NOW()');
      $quiz->save();

      if (!empty($values['photo'])) {
        $quiz->setPhoto($form->photo);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

      $auth_view = ($values['auth_view']) ? $values['auth_view'] : 'everyone';
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($quiz, $role, 'view', ($i <= $viewMax));
      }

      $auth_comment = ($values['auth_comment']) ? $values['auth_comment'] : 'everyone';
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($quiz, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $quiz->tags()->setTagMaps($viewer, $tags);

      if ($quiz->published == 1) {
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $actionsTable->getActionsByObject($quiz);

        if (count($action->toArray()) > 0) {
          // Rebuild privacy
          foreach ($actionsTable->getActionsByObject($quiz) as $action) {
            $actionsTable->resetActivityBindings($action);
          }
        }
      }

      $db->commit();

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('You changes has been saved successfully'));
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('quiz_The image you selected was too large.'));
    }

    catch (Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function createResultAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $quiz_id = $this->_getParam('quiz_id');

    $this->view->quiz = $this->_quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if (!Engine_Api::_()->core()->hasSubject('quiz')) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($viewer->getIdentity() != $quiz->user_id && !$canCreate) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateQuizNavigation('create-result');

    $quizResults = $quiz->getResultList();
    $this->view->assign('quizResults', $quizResults);

    // Assign URLS
    $urlOptions = array('quiz_id' => $quiz_id, 'result_id' => 'result_id');
    $this->view->edit_url = $this->_helper->url->url($urlOptions, 'quiz_edit_result');

    $urlOptions = array('quiz_id' => $quiz_id, 'result_id' => 'result_id', 'format' => 'json');
    $this->view->delete_url = $this->_helper->url->url($urlOptions, 'quiz_delete_result');

    $this->view->form = $form = new Quiz_Form_CreateResult();

    // Save quiz entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->quiz_id->setValue($quiz_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $quiz->modified_date = new Zend_Db_Expr('NOW()');
      $quiz->published = ($quiz->published == 1) ? (int)$quiz->isCompleted() : $quiz->published;
      $quiz->save();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('results', 'quiz');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $result = $table->createRow();
      $result->setFromArray($values);
      $result->save();

      if (!empty($values['photo'])) {
        $result->setPhoto($form->photo);
      }

      $db->commit();

      $urlOptions = array('action' => 'create-result', 'quiz_id' => $quiz->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_specific', true);
    }
    catch( Exception $e )
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function deleteResultAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->quiz_id = $quiz_id = $this->_getParam('quiz_id');
    $this->view->result_id = $result_id = $this->_getParam('result_id');

    // Send to view script if not POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);
    $result = Engine_Api::_()->getDbtable('results', 'quiz')->findRow($result_id);

    $canCreate =  $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($quiz->user_id == $viewer->getIdentity() || $canCreate) {
      $db = Engine_Api::_()->getDbtable('results', 'quiz')->getAdapter();
      $db->beginTransaction();

      try {
        $this->view->result_id = $result->result_id;
        $result->delete();
        $db->commit();

        if (!$quiz->isCompleted() && $quiz->published) {
          $quiz->published = 0;
          $quiz->save();
        }

        // tell smoothbox to close
        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('quiz_This result has been removed.');

      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_helper->redirector->gotoRoute(array(), 'core_home');
    }

    return;
  }

  public function editResultAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $quiz_id = $this->_getParam('quiz_id');
    $result_id = $this->_getParam('result_id');

    $this->_quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);
    $result = Engine_Api::_()->getDbTable('results', 'quiz')->findRow($result_id);

    if (!Engine_Api::_()->core()->hasSubject('quiz')) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($viewer->getIdentity() != $quiz->user_id && !$canCreate) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateQuizNavigation('create-result');

    $this->view->form = $form = new Quiz_Form_EditResult();

    // Save quiz entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->populate($result->toArray());
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $quiz->modified_date = new Zend_Db_Expr('NOW()');
      $quiz->published = ($quiz->published == 1) ? (int)$quiz->isCompleted() : $quiz->published;
      $quiz->save();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('results', 'quiz');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $result->setFromArray($values);
      $result->save();

      if (!empty($values['photo'])) {
        $result->setPhoto($form->photo);
      }

      $db->commit();

      $urlOptions = array('action' => 'create-result', 'quiz_id' => $quiz->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_specific', true);
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function createQuestionAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $quiz_id = $this->_getParam('quiz_id');

    $this->view->quiz = $this->_quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if (!Engine_Api::_()->core()->hasSubject('quiz')) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($viewer->getIdentity() != $quiz->user_id && !$canCreate) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateQuizNavigation('create-question');

    $quizResults = $quiz->getResultList();
    $this->view->assign('quizResults', $quizResults);

    $quizQuestions = $quiz->getQuestionList(true);
    $this->view->assign('quizQuestions', $quizQuestions);

    //Assign URLS
    $urlOptions = array('quiz_id' => $quiz_id, 'question_id' => 'question_id');
    $this->view->edit_url = $this->_helper->url->url($urlOptions, 'quiz_edit_question');

    $urlOptions = array('quiz_id' => $quiz_id, 'question_id' => 'question_id', 'format' => 'json');
    $this->view->delete_url = $this->_helper->url->url($urlOptions, 'quiz_delete_question');

    $this->view->form = $form = new Quiz_Form_CreateQuestion();

    // array of result titles
    $result_list = array();

    // Add Answers fields
    $order = 2;
    $question_answers = array();
    foreach ($quizResults as $quizResult) {
      $result_list[$quizResult->getIdentity()] = $quizResult->title;

      $form->addElement('Text', 'answer_' . $quizResult->getIdentity(), array(
        'label' => $quizResult->title . ' -> ',
        'allowEmpty' => false,
        'required' => true,
        'order' => $order,
        'class' => 'result_answer',
        'filters' => array(
        new Engine_Filter_Censor(),
          'StripTags',
          new Engine_Filter_StringLength(array('max' => '255'))
      )));

      $question_answers[] = 'answer_' . $quizResult->getIdentity();

      $order++;
    }

    $form->addDisplayGroup($question_answers, 'question_answers', array('order' => $order));

    $this->view->assign('result_list', $result_list);

    // Save quiz entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->quiz_id->setValue($quiz_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $quiz->modified_date = new Zend_Db_Expr('NOW()');
      $quiz->published = ($quiz->published == 1) ? (int)$quiz->isCompleted() : $quiz->published;
      $quiz->save();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('questions', 'quiz');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $question = $table->createRow();
      $question->setFromArray($values);
      $question->save();

      if (!empty($values['photo'])) {
        $question->setPhoto($form->photo);
      }

      // Save answers
      $answerTable = Engine_Api::_()->getDbtable('answers', 'quiz');
      foreach ($quizResults as $result) {
        $answer_info = array();
        $answer_info['question_id'] = $question->getIdentity();
        $answer_info['result_id'] = $result->getIdentity();
        $answer_info['label'] = $values['answer_' . $answer_info['result_id']];

        $answer = $answerTable->createRow();
        $answer->setFromArray($answer_info);
        $answer->save();
      }

      $db->commit();

      $urlOptions = array('action' => 'create-question', 'quiz_id' => $quiz->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_specific', true);
    }
    catch (Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function deleteQuestionAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->quiz_id = $quiz_id = $this->_getParam('quiz_id');
    $this->view->question_id = $question_id = $this->_getParam('question_id');

    // Send to view script if not POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);
    $question = Engine_Api::_()->getDbtable('questions', 'quiz')->findRow($question_id);

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($quiz->user_id == $viewer->getIdentity() || $canCreate) {
      $db = Engine_Api::_()->getDbtable('questions', 'quiz')->getAdapter();
      $db->beginTransaction();

      try {
        $this->view->question_id = $question->question_id;
        $question->delete();

        if (!$quiz->isCompleted() && $quiz->published) {
          $quiz->published = 0;
          $quiz->save();
        }

        $db->commit();

        // tell smoothbox to close
        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('quiz_This question has been removed.');

      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_helper->redirector->gotoRoute(array(), 'core_home');
    }

    return;
  }

  public function editQuestionAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $quiz_id = $this->_getParam('quiz_id');
    $question_id = $this->_getParam('question_id');

    $this->_quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);
    $question = Engine_Api::_()->getDbtable('questions', 'quiz')->findRow($question_id);

    if (!Engine_Api::_()->core()->hasSubject('quiz')) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($viewer->getIdentity() != $quiz->user_id && !$canCreate) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->assign('question', $question);

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateQuizNavigation('create-question');

    $quizResults = $quiz->getResultList();
    $this->view->assign('quizResults', $quizResults);

    $this->view->form = $form = new Quiz_Form_EditQuestion();

    $answers = $question->getAnswers();

    $answerList = array();
    foreach ($answers as $answer) {
      $answerList[$answer->result_id] = $answer;
    }

    // Add Answers fields
    $order = 2;
    foreach ($quizResults as $quizResult) {
      $result_id = $quizResult->getIdentity();
      $answer = isset($answerList[$result_id]) ? $answerList[$result_id] : array();

      $form->addElement('Text', 'answer_' . $result_id, array(
        'label' => $quizResult->title,
        'value' => ($answer) ? $answer->label : '',
        'allowEmpty' => false,
        'required' => true,
        'order' => $order++,
        'filters' => array(
          new Engine_Filter_Censor(),
          'StripTags',
          new Engine_Filter_StringLength(array('max' => '255'))
      )));

      $form->addElement('Hidden', 'answer_id_' . $result_id, array(
        'value' => ($answer) ? $answer->answer_id : '',
        'allowEmpty' => false,
        'required' => ($answer) ? true : false,
        'order' => $order++,
        'filters' => array(
        new Engine_Filter_Censor(),
          'StripTags',
          new Engine_Filter_StringLength(array('max' => '255'))
      )));
    }

    // Save quiz entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->populate($question->toArray());

      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $quiz->modified_date = new Zend_Db_Expr('NOW()');
      $quiz->published = ($quiz->published == 1) ? (int)$quiz->isCompleted() : $quiz->published;
      $quiz->save();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('questions', 'quiz');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $question->setFromArray($values);
      $question->save();

      if (!empty($values['photo'])) {
        $question->setPhoto($form->photo);
      }

      // Save answers
      $answerTable = Engine_Api::_()->getDbtable('answers', 'quiz');
      foreach ($quizResults as $result) {
        $answer_info = array();
        $answer_info['question_id'] = $question->getIdentity();
        $answer_info['result_id'] = $result->getIdentity();
        $answer_info['label'] = $values['answer_' . $answer_info['result_id']];

        $answer_key = 'answer_id_' . $answer_info['result_id'];

        if (isset($values[$answer_key]) && $values[$answer_key]) {
          $answer = $answerTable->findRow($values[$answer_key]);
        } else {
          $answer = $answerTable->createRow();
        }

        $answer->setFromArray($answer_info);
        $answer->save();
      }

      $db->commit();

      $urlOptions = array('action' => 'create-question', 'quiz_id' => $quiz->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_specific', true);
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function publishAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $quiz_id = $this->_getParam('quiz_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->quiz = $this->_quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if (!Engine_Api::_()->core()->hasSubject('quiz')) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }

    $canCreate = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'create')->isValid();

    if ($viewer->getIdentity() != $quiz->user_id && !$canCreate) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateQuizNavigation('publish');

    $this->view->form = $form = new Quiz_Form_Publish();

    if (!$this->getRequest()->isPost() || $this->_getParam('saved'))
    {
      $form->quiz_id->setValue($quiz_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $quiz->published = $values['published'];
      $quiz->modified_date = new Zend_Db_Expr('NOW()');
      $quiz->save();

      if ($quiz->published == 1)
      {
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

        $select = $actionsTable->select()
          ->where('type = ?', 'quiz_new')
          ->where('subject_id = ?', $viewer->getIdentity())
          ->where('object_id = ?', $quiz->getIdentity());

        $action = $actionsTable->fetchRow($select);

        if ($action != null) {
          $action->deleteItem();
        }

        $action = $actionsTable->addActivity($viewer, $quiz, 'quiz_new');

        // make sure action exists before attaching the quiz to the activity
        if ($action != null)
        {
          $actionsTable->attachActivity($action, $quiz);
        }
      }

      $db->commit();

      return $this->_helper->redirector->gotoRoute(array('quiz_id' => $quiz->getIdentity()), 'quiz_manage', true);
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function manageAction()
  {
  	if (!$this->_helper->requireUser()->isValid()) {
  	  return;
  	}

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->level_id = Engine_Api::_()->user()->getViewer()->level_id;
    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Quiz_Form_Search();
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('quiz', null, 'create')->checkRequire();

    $form->removeElement('show');

    $this->view->edit_url = $this->_helper->url->url(array('action' => 'edit', 'quiz_id' => 'quiz_id'), 'quiz_specific');
    $this->view->delete_url = $this->_helper->url->url(array('action' => 'delete', 'quiz_id' => 'quiz_id'), 'quiz_specific');

    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->quiz()->getCategories();
    foreach ($categories as $category)
    {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }

    // Process form
    $form->isValid($this->getRequest()->getPost());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->quiz()->getQuizzesPaginator($values);
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $items_per_page = $settings->getSetting('quizzes.items.onpage', 5);
    $paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->theme_name = Engine_Api::_()->quiz()->getCurrentTheme();
  }

  public function deleteAction()
  {
    $quiz_id = $this->_getParam('quiz_id');
    $this->view->quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if (!$this->_helper->requireAuth()->setAuthParams($quiz, null, 'delete')->isValid()) {
      return;
    };

    // Make form
    $this->view->form = $form = new Quiz_Form_Delete();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process form
    $db = Engine_Api::_()->getDbtable('quizs', 'quiz')->getAdapter();
    $db->beginTransaction();

    try
    {
      $quiz->delete();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_redirectCustom(array('route' => 'quiz_manage'));
  }

  public function takeAction()
  {
    $quiz_id = $this->_getParam('quiz_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if (!$quiz || $quiz->published == 0) {
      $this->_helper->redirector->gotoRoute(array(), 'quiz_browse');
    }

    if (!$this->_helper->requireAuth()->setAuthParams('quiz', null, 'take')->isValid()) {
      return;
    }

    if ($viewer->getIdentity() == 0) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $quizQuestions = $quiz->getQuestionList(true);
    $this->view->assign('question_count', $quizQuestions->count());

    $this->view->form = $form = new Quiz_Form_Take();

    $number = 1;
    foreach ($quizQuestions as $question)
    {
      $options = array();
      $option_ids = array();
      foreach ($question->answers as $answer) {
        $options[$answer->answer_id] = $answer->label;
        $option_ids[] = $answer->answer_id;
      }

      shuffle($option_ids);

      $multiOptions = array();
      foreach ($option_ids as $option_id) {
      	$multiOptions[$option_id] = $options[$option_id];
      }

      $form->addElement('Radio', 'question_' . $question->question_id, array(
        'class' => 'quiz_answer',
        'required' => true,
        'multiOptions' => $multiOptions
      ));

      $photo_src = $question->getPhotoUrl();

      if ($photo_src) {
        $photo_options = array('title' => Zend_Registry::get('Zend_Translate')
          ->_('View fullsize'), 'onclick' => "he_show_image('$photo_src', $(this).getElement('img'))");
        $photo = $this->view->htmlLink('javascript://', $this->view->itemPhoto($question, 'thumb.normal'), $photo_options);
      } else {
        $photo = '';
      }

      $form->getElement('question_' . $question->question_id)
        ->addDecorator('QuizQuestion', array('number' => $number++, 'label' => $question->text, 'photo' => $photo));
    }

    // Save quiz entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->quiz_id->setValue($quiz_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

     // Process form
    $values = $form->getValues();
    unset($values['quiz_id']);
    $answer_ids = array_values($values);

    $table = Engine_Api::_()->getDbtable('takes', 'quiz');
    $db = $table->getAdapter();

    try
    {
      $result_id = $quiz->fetchTakeResult($answer_ids);
      $take = $quiz->getUserResult($viewer->getIdentity());

      if (!$take) {
        $take = $table->createRow();

        $quiz->take_count++;
        $quiz->save();
      }

      $take_info = array();
      $take_info['quiz_id'] = $quiz_id;
      $take_info['user_id'] = $viewer->getIdentity();
      $take_info['result_id'] = $result_id;
      $take_info['took_date'] = new Zend_Db_Expr('NOW()');

      $take->setFromArray($take_info);
      $take->save();

      $choiceTable = Engine_Api::_()->getDbtable('choices', 'quiz');
      $choiceTable->deleteUserChoices($quiz_id, $viewer->getIdentity());

      foreach ($answer_ids as $answer_id) {
        $choice = $choiceTable->createRow();
        $choice->setFromArray(array(
          'quiz_id' => $quiz_id,
          'user_id' => $viewer->getIdentity(),
          'answer_id' => $answer_id));

        $choice->save();
      }

      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

      $select = $actionsTable->select()
        ->where('type = ?', 'quiz_take')
        ->where('subject_id = ?', $viewer->getIdentity())
        ->where('object_id = ?', $quiz->getIdentity());

      $action = $actionsTable->fetchRow($select);

      if ($action != null) {
        $action->deleteItem();
      }

      $action = $actionsTable->addActivity($viewer, $quiz, 'quiz_take');

      // make sure action exists before attaching the quiz to the activity
      if ($action != null) {
        $actionsTable->attachActivity($action, $quiz);
      }

      $db->commit();

      $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($quiz->getTitle()))), '-');

      $urlOptions = array('quiz_id' => $quiz->getIdentity(), 'slug' => $slug);

      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_view', true);
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function viewAction()
  {
    $quiz_id = $this->_getParam('quiz_id');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->quiz = $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    if (!empty($quiz)) {
      Engine_Api::_()->core()->setSubject($quiz);
    }

    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }

    if (!$this->_helper->requireAuth()->setAuthParams($quiz, null, 'view')->isValid()) {
      return;
    }

    $is_owner = ($quiz->getOwner()->getIdentity() == $viewer->getIdentity());

    if (!$quiz->published && $is_owner) {
      $urlOptions = array('action' => 'create-result', 'quiz_id' => $quiz->getIdentity());
      return $this->_helper->redirector->gotoRoute($urlOptions, 'quiz_specific', true);
    } else if (!$quiz->published) {
      return $this->_helper->redirector->gotoRoute(array(), 'quiz_browse', true);
    }

    if ($quiz->getOwner()->getIdentity() != $viewer->getIdentity()) {
      $quiz->view_count++;
      $quiz->save();
    }

    $this->view->userTake = $userTake = $quiz->getUserResult($viewer->getIdentity());
    $this->view->quizResults = $quizResults = $quiz->getResultList();
    $this->view->takeResults = $tookResults = $quiz->getTakerList();

    if ($userTake) {
      $this->view->userResult = $quizResults->getRowMatching('result_id', $userTake->result_id);

      $firstMatches = $quiz->userMatches($viewer->getIdentity(), 1);
      $this->view->firstMatchCount = $firstMatches['count'];
      $this->view->firstMatches = $firstMatches['users'];

      $secondMatches = $quiz->userMatches($viewer->getIdentity(), 2);
      $this->view->secondMatchCount = $secondMatches['count'];
      $this->view->secondMatches = $secondMatches['users'];
    }

    $quiz_results = $quizResults->toArray();
    $result_list = array();

    foreach ($quiz_results as $quiz_result) {
      $quiz_result['tooks'] = $tookResults->getRowsMatching('result_id', $quiz_result['result_id']);
      $quiz_result['took_count'] = count($quiz_result['tooks']);

      $result_list[$quiz_result['result_id']] = $quiz_result;
    }

    $this->view->assign('result_list', $result_list);

    $this->view->can_take = $this->_helper->requireAuth()->setAuthParams('quiz', null, 'take')->checkRequire();
    $this->view->can_comment = $this->_helper->requireAuth()->setAuthParams($quiz, null, 'comment')->checkRequire();
    //$this->view->can_comment = Engine_Api::_()->authorization()->context->isAllowed($quiz, $viewer, 'comment');

    $this->view->quiz_tabs = $this->getQuizTabs();
    $this->view->quiz_options = $this->getQuizOptions();
    $this->view->take_url = $this->_helper->url->url(array(
      'action' => 'take',
      'quiz_id' => $quiz_id), 'quiz_specific');

    $this->view->chart_data_url = $this->_helper->url->url(array(
      'module' => 'quiz',
      'controller' => 'index',
      'action' => 'chart-data',
      'quiz_id' => $quiz_id,
      'bg_color' => 'bg_color_value',
      'color' => 'color_value',
      'no_cache' => uniqid('')), 'default', false);

    $this->view->rateEnabled = $rateEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate');

    if ($rateEnabled) {
      $this->view->can_rate = (boolean)$userTake;
      $this->view->error_msg = ($is_owner)
        ? Zend_Registry::get('Zend_Translate')->_('Sorry, you cannot rate own content.')
        : Zend_Registry::get('Zend_Translate')->_('Please take this quiz to continue.');
    }

    $this->view->maxShowUsers = Engine_Api::_()->getApi('settings', 'core')->getSetting('quizzes.max.showusers', 14);
    $this->view->theme_name = Engine_Api::_()->quiz()->getCurrentTheme();
  }

  public function chartDataAction()
  {
    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

  	$quiz_id = $this->_getParam('quiz_id');
  	$bg_color = $this->_getParam('bg_color');
  	$color = $this->_getParam('color');

    $quiz = Engine_Api::_()->getItem('quiz', $quiz_id);

    $quizResults = $quiz->getResultList();
    $quiz_results = $quizResults->toArray();

    $tookResults = $quiz->getTakerList();
    $chartData = array();
    foreach ($quiz_results as $quiz_result) {
      $resultTooks = $tookResults->getRowsMatching('result_id', $quiz_result['result_id']);
      $quiz_result['took_count'] = count($resultTooks);

      if ($quiz_result['took_count'] > 0) {
        $chartData[] = array(
          'value' => $quiz_result['took_count'],
          'label' => $this->view->string()->truncate($quiz_result['title'], 10, '...')
        );
      }
    }

    $options = array('tip' => Zend_Registry::get('Zend_Translate')->_('{$val} of {$total} results<br>{$percent}'));

    if ($bg_color && strlen($bg_color) > 0) {
      $options['bg_colour'] = "#$bg_color";
    }

    $color = ($color && strlen($color) == 3)
      ? '#' . $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2]
      : $color;
    $color = ($color && strlen($color) > 0) ? "#$color" : '#5F5F5F';

    if ($chartData) {
      $title = array('text' => $quiz->getTitle(), 'style' => "color: $color; font-weight: bold; font-size: 17px;");
    } else {
      $title = array('text' => Zend_Registry::get('Zend_Translate')->_('quiz_There are no results'), 'style' => "color: $color; font-size: 13px; padding-top: 100px;");
    }

    $chartDataJS = $this->generateOFC_Chart($title, $chartData, $options);

    $this->getResponse()->setBody($chartDataJS);
  }

  // Utility

  public function getNavigation($active = false)
  {
    if (is_null($this->_navigation))
    {
      $translate = Zend_Registry::get('Zend_Translate');
      $navigation = $this->_navigation = new Zend_Navigation();

      if (Engine_Api::_()->user()->getViewer()->getIdentity())
      {
        $navigation->addPage(array(
          'label' => $translate->_('Browse Quizzes'),
          'route' => 'quiz_browse',
          'module' => 'quiz',
          'controller' => 'index',
          'action' => 'index'
        ));

        $navigation->addPage(array(
          'label' => $translate->_('My Quizzes'),
          'route' => 'quiz_manage',
          'module' => 'quiz',
          'controller' => 'index',
          'action' => 'manage',
          'active' => $active
        ));

        if ($this->_helper->requireAuth()->setAuthParams('quiz', null, 'create')->checkRequire()) {
          $navigation->addPage(array(
            'label' => $translate->_('Create new quiz'),
            'route' => 'quiz_create',
            'module' => 'quiz',
            'controller' => 'index',
            'action' => 'create'
          ));
        }
      }
    }
    return $this->_navigation;
  }

  public function getQuizNavigation($quiz_id, $active_tab, $available_step)
  {
    if (!is_null($this->_quiz_navigation)) {
      return $this->_quiz_navigation;
    }

    $translate = Zend_Registry::get('Zend_Translate');
    $navigation = $this->_quiz_navigation = new Zend_Navigation();

    if (Engine_Api::_()->user()->getViewer()->getIdentity())
    {
      $navigation->addPage(array(
        'label' => 'quiz_Basics',
        'route' => 'quiz_specific',
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'edit',
        'active' => ($active_tab == 'edit'),
        'params' => array('quiz_id' => $quiz_id)
      ));

      $navigation->addPage(array(
        'label' => 'quiz_Results',
        'route' => 'quiz_specific',
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'create-result',
        'active' => ($active_tab == 'create-result'),
        'class' => ($available_step > 1) ? '' : 'disabled',
        'params' => array('quiz_id' => $quiz_id)

      ));

      $navigation->addPage(array(
        'label' => 'quiz_Questions',
        'route' => 'quiz_specific',
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'create-question',
        'active' => ($active_tab == 'create-question'),
        'class' => ($available_step > 2) ? '' : 'disabled',
        'params' => array('quiz_id' => $quiz_id)
      ));

      $navigation->addPage(array(
        'label' => 'quiz_Publish',
        'route' => 'quiz_specific',
        'module' => 'quiz',
        'controller' => 'index',
        'action' => 'publish',
        'active' => ($active_tab == 'publish'),
        'class' => ($available_step > 3) ? '' : 'disabled',
        'params' => array('quiz_id' => $quiz_id)
      ));
    }

    return $this->_quiz_navigation;
  }

  public function generateQuizNavigation($active_tab = 'edit')
  {
    $quizStatus = $this->_quiz->getQuizStatus();

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->minResultCount = $minResultCount = (int)$settings->getSetting('quizzes.min.result.count', 2);
    $this->view->minQuestionCount = $minQuestionCount = (int)$settings->getSetting('quizzes.min.question.count', 1);

    $step_info = array('error' => 0, 'message' => '', 'redirect' => '');

    $step = 4;

    if ($quizStatus['result_count'] < $minResultCount)
    {
      $step = 2;
      $step_info['message'] = $this->view->translate(array(
        'quiz_You need to create at least %s result', 'You need to create at least %s results', $minResultCount),
      $minResultCount);
    }
    elseif ($quizStatus['question_count'] < $minQuestionCount)
    {
      $step = 3;
      $step_info['message'] = $this->view->translate(array(
        'quiz_You need to create at least %s question', 'You need to create at least %s questions', $minQuestionCount),
      $minQuestionCount);
    }
    elseif ($quizStatus['result_count'] * $quizStatus['question_count'] > $quizStatus['answer_count'])
    {
      $step = 3;
      $step_info['message'] = $this->view->translate('quiz_You need to fill out all answers');
    }

    $step_info['next_error'] = 1;

    switch ($active_tab) {
      case 'edit':
        if ($step < 1) {
          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url(array(), 'quiz_create');
        }
        if ($step > 1) {
        $urlOptions = array('action' => 'create-result', 'quiz_id' => $this->_quiz->quiz_id);

        $step_info['next_error'] = 0;
        $step_info['next'] = $this->_helper->url->url($urlOptions, 'quiz_specific');
        }
        break;

      case 'create-result':
        if ($step < 2) {
          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url(array('quiz_id' => $this->_quiz->quiz_id), 'edit');
        }
        if ($step > 2) {
          $urlOptions = array('action' => 'create-question', 'quiz_id' => $this->_quiz->quiz_id);

          $step_info['next_error'] = 0;
          $step_info['next'] = $this->_helper->url->url($urlOptions, 'quiz_specific');
        }
        break;

      case 'create-question':
        if ($step < 3) {
          $urlOptions = array('action' => 'create-result', 'quiz_id' => $this->_quiz->quiz_id);

          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url($urlOptions, 'quiz_specific');
        }
        if ($step > 3) {
          $urlOptions = array('action' => 'publish', 'quiz_id' => $this->_quiz->quiz_id);

          $step_info['next_error'] = 0;
          $step_info['next'] = $this->_helper->url->url($urlOptions, 'quiz_specific');
        }
        break;

      case 'publish':
        if ($step < 3) {
          $urlOptions = array('action' => 'create-result', 'quiz_id' => $this->_quiz->quiz_id);

          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url($urlOptions, 'quiz_specific');
        }
        else if ($step < 4) {
          $urlOptions = array('action' => 'create-question', 'quiz_id' => $this->_quiz->quiz_id);

          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url($urlOptions, 'quiz_specific');
        }
        break;

      default:
        break;
    }

    $this->view->assign('step_info', Zend_Json::encode($step_info));

    $quiz_navigation = $this->getQuizNavigation($this->_quiz->quiz_id, $active_tab, $step);

    $this->view->quiz_navigation = $quiz_navigation;
  }

  public function getQuizTabs()
  {
    if (!is_null($this->_quiz_tabs)) {
      return $this->_quiz_tabs;
    }

    $navigation = $this->_quiz_tabs = new Zend_Navigation();
    $translate = Zend_Registry::get('Zend_Translate');

    if (Engine_Api::_()->user()->getViewer()->getIdentity() && $this->view->userTake)
    {
      $navigation->addPage(array(
        'label' => $translate->_('quiz_My Matches'),
        'uri' => 'javascript://',
        'id' => 'matches',
        'class' => 'quiz_tab',
        'active' => true
      ));
    }

    $navigation->addPage(array(
      'label' => $translate->_('Quiz Results'),
      'uri' => 'javascript://',
      'id' => 'results',
      'class' => 'quiz_tab',
      'active' => !(Engine_Api::_()->user()->getViewer()->getIdentity() && $this->view->userTake)
    ));

    $navigation->addPage(array(
      'label' => $translate->_('quiz_Who Took This Quiz'),
      'uri' => 'javascript://',
      'id' => 'tooks',
      'class' => 'quiz_tab',
    ));

    if ($this->view->can_comment) {
      $navigation->addPage(array(
        'label' => $translate->_('Comments'),
        'uri' => 'javascript://',
        'id' => 'comments',
        'class' => 'quiz_tab',
      ));
    }

    return $this->_quiz_tabs;
  }

  public function getQuizOptions()
  {
    if (!is_null($this->_quiz_options)) {
      return $this->_quiz_options;
    }

    $quiz = $this->view->quiz;
    $viewer = Engine_Api::_()->user()->getViewer();
    $navigation = $this->_quiz_options = new Zend_Navigation();

    if ($viewer->getIdentity() && $viewer->getIdentity() == $quiz->user_id) {
      $navigation->addPage(array(
        'label' => 'Edit Quiz',
        'icon' => 'application/modules/Quiz/externals/images/edit.png',
        'uri' => $this->_helper->url->url(array('action' => 'edit', 'quiz_id' => $quiz->getIdentity()), 'quiz_specific'),
      ));

      $navigation->addPage(array(
        'label' => 'Delete Quiz',
        'icon' => 'application/modules/Quiz/externals/images/delete.png',
        'uri' => $this->_helper->url->url(array('action' => 'delete', 'quiz_id' => $quiz->getIdentity()), 'quiz_specific'),
      ));
    }

    if ($viewer->getIdentity() && $quiz->approved == 1) {
      $navigation->addPage(array(
        'label' => 'quiz_Share Quiz',
        'icon' => 'application/modules/Quiz/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
          'module' => 'activity',
          'controller' => 'index',
          'action' => 'share',
          'type' => $quiz->getType(),
          'id' => $quiz->getIdentity(),
          'format' => 'smoothbox',
        ),
      ));
    }

    return $this->_quiz_options;
  }

  public function generateOFC_Chart($title, $values = array(), $options = array())
  {
    $elements = array();

    if ($options && isset($options['tip'])) {
      $elements['tip'] = $options['tip'];
    }

    $elements['colours'] = isset($options['colours'])
      ? $options['colours']
      : array('#385D8A', '#8C3836', '#71893F', '#357D91', '#B66D31', '#426DA1', '#A44340',
        '#849F4B', '#6C548A', '#3F92A9', '#D37F3A', '#4B7BB4', '#B74C49', '#94B255', '#7A5F9A', '#47A4BD',
        '#A1B4D4', '#D6A1A0');

    $elements['alpha'] = isset($options['alpha']) ? $options['alpha'] : 0.8;
    $elements['start_angle'] = isset($options['start_angle']) ? $options['start_angle'] : 135;
    $elements['border'] = isset($options['border']) ? $options['border'] : 2;
    $elements['animate'] = isset($options['border']) ? $options['border'] : true;
    $elements['values'] = $values;
    $elements['type'] = 'pie';

    $chart = array();

    $chart['elements'][] = $elements;
    $chart['bg_colour'] = isset($options['bg_colour']) ? $options['bg_colour'] : "#E9F4FA";
    $chart['title'] = ($title && is_array($title))
      ? array('text' => $title['text'], 'style' => $title['style'])
      : array('text' => $title);

    return Zend_Json::encode($chart);
  }
}