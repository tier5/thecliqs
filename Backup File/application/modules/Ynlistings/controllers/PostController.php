<?php
class Ynlistings_PostController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( Engine_Api::_()->core()->hasSubject() ) return;

    if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
        null !== ($post = Engine_Api::_()->getItem('ynlistings_post', $post_id)) )
    {
      Engine_Api::_()->core()->setSubject($post);
    }

    else if( 0 !== ($topic_id = (int) $this->_getParam('topic_id')) &&
        null !== ($topic = Engine_Api::_()->getItem('ynlistings_topic', $topic_id)) )
    {
      Engine_Api::_()->core()->setSubject($topic);
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'edit',
      'delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'edit' => 'ynlistings_post',
      'delete' => 'ynlistings_post',
    ));
  }
  
  public function editAction()
  {
    $post = Engine_Api::_()->core()->getSubject('ynlistings_post');
    $listing = $post->getParent('ynlistings_listing');
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$listing->isOwner($viewer) && !$post->isOwner($viewer) && !$listing->authorization()->isAllowed($user, 'topic.edit') )
    {
      return $this->_helper->requireAuth->forward();
    }

    $this->view->form = $form = new Ynlistings_Form_Post_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($post->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $post->setFromArray($form->getValues());
      $post->modified_date = date('Y-m-d H:i:s');
      $post->save();
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Try to get topic
    return $this->_forward('success', 'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRefresh' => true,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('The changes to your post have been saved.')),
    ));
  }

  public function deleteAction()
  {
    $post = Engine_Api::_()->core()->getSubject('ynlistings_post');
    $listing = $post->getParent('ynlistings_listing');
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$listing->isOwner($viewer) && !$post->isOwner($viewer) && !$listing->authorization()->isAllowed($user, 'topic.edit') )
    {
      return $this->_helper->requireAuth->forward();
    }

    $this->view->form = $form = new Ynlistings_Form_Post_Delete();

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    $topic_id = $post->topic_id;

    try
    {
      $post->delete();
     
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Try to get topic
    $topic = Engine_Api::_()->getItem('ynlistings_topic', $topic_id);
    $href = ( null === $topic ? $listing->getHref() : $topic->getHref() );
    return $this->_forward('success', 'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRedirect' => $href,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Post deleted.')),
    ));
  }
  
  public function reportAction()
  {
  	$post = Engine_Api::_()->core()->getSubject('ynlistings_post');
    $listing = $post->getParent('ynlistings_listing');
    $viewer = Engine_Api::_()->user()->getViewer();
    
  	$this -> view -> form = $form = new Ynlistings_Form_Post_Report();
  	if (!$this -> getRequest() -> isPost()) {
  		return;
  	}
  	if (!$form -> isValid($this -> getRequest() -> getPost())) {
  		return;
  	}
  	$table = Engine_Api::_()->getItemTable('ynlistings_report');
  	$db = $table->getAdapter();
  	$db->beginTransaction();
  	try
  	{
  		$values = array('user_id'=>$viewer->getIdentity(), 'listing_id' =>$this->_getParam('listing_id',0),
  				'topic_id'=>$this->_getParam('topic_id',0),'post_id'=>$this->_getParam('post_id',0),
  				'content'=>$form->getValue('body'));
  			
  		$report = $table->createRow();
  		$report->setFromArray($values);
  		$report->save();
  		$db->commit();
  	}
  	catch( Exception $e ) {
  		$db->rollBack();
  		throw $e; // This should be caught by error handler
  	}
  
  	return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('The report will be sent to admin')), 'layout' => 'default-simple','smoothboxClose' => true, 'parentRefresh' => false, ));
  
  
  }
}