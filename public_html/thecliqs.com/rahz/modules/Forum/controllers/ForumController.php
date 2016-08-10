<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ForumController.php 9889 2013-02-13 22:24:58Z shaun $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Forum_ForumController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( 0 !== ($forum_id = (int) $this->_getParam('forum_id')) &&
        null !== ($forum = Engine_Api::_()->getItem('forum_forum', $forum_id)) )
    {
      Engine_Api::_()->core()->setSubject($forum);
    }

    else if( 0 !== ($category_id = (int) $this->_getParam('category_id')) &&
        null !== ($category = Engine_Api::_()->getItem('forum_category', $category_id)) )
    {
      Engine_Api::_()->core()->setSubject($category);
    }
  }
  
  public function viewAction()
  {
    if( !$this->_helper->requireSubject('forum')->isValid() ) {
      return;
    }
    $forum = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth->setAuthParams($forum, null, 'view')->isValid() ) {
      return;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->forum = $forum = Engine_Api::_()->core()->getSubject();

    // Increment view count
    $forum->view_count = new Zend_Db_Expr('view_count + 1');
    $forum->save();

    $this->view->canPost = $canPost = $forum->authorization()->isAllowed(null, 'topic.create');

     // Get params
    switch($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }
    
    // Make paginator
    $table = Engine_Api::_()->getItemTable('forum_topic');
    $select = $table->select()
      ->where('forum_id = ?', $forum->getIdentity())
      ->order('sticky DESC')
      ->order($order . ' DESC');
      ;
      
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage($settings->getSetting('forum_forum_pagelength'));
    $this->view->forum_topic_pagelength = $settings->getSetting('forum_topic_pagelength');
    
    $list = $forum->getModeratorList();
    $moderators = $this->view->moderators = $list->getAllChildren();
    
    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }

  public function topicCreateAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject('forum')->isValid() ) {
      return;
    }
    
    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
        
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->forum = $forum = Engine_Api::_()->core()->getSubject();
    if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.create')->isValid() ) {
      return;
    }
    
    $this->view->form = $form = new Forum_Form_Topic_Create();

    // Remove the file element if there is no file being posted
    if( $this->getRequest()->isPost() && empty($_FILES['photo']) ) {
      $form->removeElement('photo');
    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['forum_id'] = $forum->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
    $postTable = Engine_Api::_()->getDbtable('posts', 'forum');
    
    $db = $topicTable->getAdapter();
    $db->beginTransaction();

    try {

      // Create topic
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->title = $values['title'];
      $topic->description = $values['body'];
      $topic->save();

      // Create post
      $values['topic_id'] = $topic->getIdentity();

      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      if( !empty($values['photo']) ) {
        $post->setPhoto($form->photo);
      }

      $auth = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($topic, 'registered', 'create', true);

      // Create topic watch
      $topicWatchesTable->insert(array(
        'resource_id' => $forum->getIdentity(),
        'topic_id' => $topic->getIdentity(),
        'user_id' => $viewer->getIdentity(),
        'watch' => (bool) $values['watch'],
      ));

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $topic, 'forum_topic_create');
      if( $action ) {
        $action->attach($topic);
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_redirectCustom($post);
  }
}
