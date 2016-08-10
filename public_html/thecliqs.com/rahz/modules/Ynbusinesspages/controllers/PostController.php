<?php
class Ynbusinesspages_PostController extends Core_Controller_Action_Standard
{
  public function init()
  {
  	$this -> view -> tab = $this->_getParam('tab', null);
    if( Engine_Api::_()->core()->hasSubject() ) 
    	return $this -> _helper -> requireSubject -> forward();

    if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
        null !== ($post = Engine_Api::_()->getItem('ynbusinesspages_post', $post_id)) )
    {
      Engine_Api::_()->core()->setSubject($post);
    }
	
    $this->_helper->requireUser->addActionRequires(array(
      'edit',
      'delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'edit' => 'ynbusinesspages_post',
      'delete' => 'ynbusinesspages_post',
    ));
  }
  
  public function editAction()
  {
    $post = Engine_Api::_()->core()->getSubject('ynbusinesspages_post');
    $business = $post->getParentBusiness();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$business -> isAllowed('discussion_delete', null, $post))
	{
		return $this -> _helper -> requireAuth -> forward();
	}

    $this->view->form = $form = new Ynbusinesspages_Form_Post_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($post->toArray());
      $form->body->setValue(html_entity_decode($post->body));      
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
      $post->body = htmlspecialchars($post->body, ENT_NOQUOTES, 'UTF-8');
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
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
    ));

  }

  public function deleteAction()
  {
    $post = Engine_Api::_()->core()->getSubject('ynbusinesspages_post');
    $business = $post->getParentBusiness();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$business -> isAllowed('discussion_delete', null, $post))
	{
		return $this -> _helper -> requireAuth -> forward();
	}

    $this->view->form = $form = new Ynbusinesspages_Form_Post_Delete();

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

    try
    {
      $topic_id = $post->topic_id;
      $post->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Try to get topic
    $topic = Engine_Api::_()->getItem('ynbusinesspages_topic', $topic_id);
    $href = ( null === $topic ? $business->getHref() : $topic->getHref() );
    return $this->_forward('success', 'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRedirect' => $href,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Post deleted.')),
    ));
  }
}