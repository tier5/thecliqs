<?php
class Ynbusinesspages_TopicController extends Core_Controller_Action_Standard {
	public function init() 
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		if (Engine_Api::_()->core()->hasSubject())
			return $this -> _helper -> requireSubject -> forward();
		if (0 !== ($topic_id = (int) $this->_getParam('topic_id')) &&
				null !== ($topic = Engine_Api::_()->getItem('ynbusinesspages_topic', $topic_id))) {
			Engine_Api::_()->core()->setSubject($topic);
		} else if (0 !== ($business_id = (int) $this->_getParam('business_id')) &&
				null !== ($business = Engine_Api::_()->getItem('ynbusinesspages_business', $business_id))) {
			Engine_Api::_()->core()->setSubject($business);
		}
	}

	public function indexAction() 
	{
		if (!$this->_helper->requireSubject('ynbusinesspages_business')->isValid())
			return;

		$this->view->business = $business = Engine_Api::_()->core()->getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_topic'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
    
		$table = Engine_Api::_()->getDbtable('topics', 'ynbusinesspages');
		$select = $table->select()
		->where('business_id = ?', $business->getIdentity())
		->order('sticky DESC')
		->order('modified_date DESC');

		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->can_post = $business -> isAllowed('discussion_create');
		$paginator->setCurrentPageNumber($this->_getParam('page'));
	}

	public function viewAction() {
		
		if (!$this->_helper->requireSubject('ynbusinesspages_topic')->isValid())
			return;
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->topic = $topic = Engine_Api::_()->core()->getSubject();
		$this->view->business = $business = $topic->getParentBusiness();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_topic'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$this->view->canEdit = $canEdit = $business->isAllowed('discussion_delete', null, $topic);
		$this->view->canPost = $canPost = $business->isAllowed('discussion_create');
		$this->view->canDelete = $canDelete = $business->isAllowed('discussion_delete', null, $topic);

		if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
			$topic->view_count = new Zend_Db_Expr('view_count + 1');
			$topic->save();
		}

		$isWatching = null;
		if ($viewer->getIdentity()) {
			$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynbusinesspages');
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $business->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if (false === $isWatching) {
				$isWatching = null;
			} else {
				$isWatching = (bool) $isWatching;
			}
		}
		$this->view->isWatching = $isWatching;

		// @todo implement scan to post
		$this->view->post_id = $post_id = (int) $this->_getParam('post');

		$table = Engine_Api::_()->getDbtable('posts', 'ynbusinesspages');
		$select = $table->select()
		->where('business_id = ?', $business->getIdentity())
		->where('topic_id = ?', $topic->getIdentity())
		->order('creation_date ASC');

		$this->view->paginator = $paginator = Zend_Paginator::factory($select);

		// Skip to page of specified post
		if (0 !== ($post_id = (int) $this->_getParam('post_id')) &&
				null !== ($post = Engine_Api::_()->getItem('ynbusinesspages_post', $post_id))) {
			$icpp = $paginator->getItemCountPerPage();
			$page = ceil(($post->getPostIndex() + 1) / $icpp);
			$paginator->setCurrentPageNumber($page);
		}

		// Use specified page
		else if (0 !== ($page = (int) $this->_getParam('page'))) {
			$paginator->setCurrentPageNumber($this->_getParam('page'));
		}

		if ($canPost && !$topic->closed) {
			$this->view->form = $form = new Ynbusinesspages_Form_Post_Create();
			$form->populate(array(
					'topic_id' => $topic->getIdentity(),
					'business_id' => $business->getIdentity(),
					'ref' => $topic->getHref(),
					'watch' => ( false === $isWatching ? '0' : '1' ),
			));
		}
	}

	public function createAction() {
		if (!$this->_helper->requireUser()->isValid())
			return;
		if (!$this->_helper->requireSubject('ynbusinesspages_business')->isValid())
			return;

		$this->view->business = $business = Engine_Api::_()->core()->getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_topic'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if (!$business -> isAllowed('discussion_create'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Topic_Create();

		// Check method/data
		if (!$this->getRequest()->isPost()) {
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		// Process
		$values = $form->getValues();
		$values['user_id'] = $viewer->getIdentity();
		$values['business_id'] = $business->getIdentity();

		$topicTable = Engine_Api::_()->getDbtable('topics', 'ynbusinesspages');
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynbusinesspages');
		$postTable = Engine_Api::_()->getDbtable('posts', 'ynbusinesspages');

		$db = $business->getTable()->getAdapter();
		$db->beginTransaction();

		try {
			// Create topic
			$topic = $topicTable->createRow();
			$topic->setFromArray($values);
			$topic->save();

			// Create post
			$values['topic_id'] = $topic->topic_id;

			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();

			// Create topic watch
			$topicWatchesTable->insert(array(
					'resource_id' => $business->getIdentity(),
					'topic_id' => $topic->getIdentity(),
					'user_id' => $viewer->getIdentity(),
					'watch' => (bool) $values['watch'],
			));
			// send notification to followers
			$business -> sendNotificationToFollowers($this -> view -> translate('new topic'));
			
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_topic_create', null, array('child_id' => $topic->getIdentity()));
			if( $action ) {
                $activityApi->attachActivity($action, $topic);
			}
			
			$business -> topic_count ++;
			$business -> save();
			
			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		// Redirect to the post
		$this->_redirectCustom($post);
	}

	public function postAction() {
		if (!$this->_helper->requireUser()->isValid())
			return;
		if (!$this->_helper->requireSubject('ynbusinesspages_topic')->isValid())
			return;

		$this->view->topic = $topic = Engine_Api::_()->core()->getSubject();
		$this->view->business = $business = $topic->getParentBusiness();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_topic'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if (!$business -> isAllowed('discussion_create'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		if ($topic->closed) {
			$this->view->status = false;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('This has been closed for posting.');
			return;
		}

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Post_Create();

		// Check method/data
		if (!$this->getRequest()->isPost()) {
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$topicOwner = $topic->getOwner();
		$isOwnTopic = $viewer->isSelf($topicOwner);

		$postTable = Engine_Api::_()->getDbtable('posts', 'ynbusinesspages');
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynbusinesspages');
		$userTable = Engine_Api::_()->getItemTable('user');
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

		$values = $form->getValues();
		$values['user_id'] = $viewer->getIdentity();
		$values['business_id'] = $business->getIdentity();
		$values['topic_id'] = $topic->getIdentity();

		$watch = (bool) $values['watch'];
		$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $business->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0);

		$db = $business->getTable()->getAdapter();
		$db->beginTransaction();

		try {
			// Create post
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();

			// Watch
			if (false === $isWatching) {
				$topicWatchesTable->insert(array(
						'resource_id' => $business->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if ($watch != $isWatching) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $business->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
            
            // Activity
            $action = $activityApi -> addActivity($viewer, $business, 'ynbusinesspages_topic_reply',$topic->toString());
            if ($action) {
               $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
            }

			// Notifications
			$notifyUserIds = $topicWatchesTable->select()
			->from($topicWatchesTable->info('name'), 'user_id')
			->where('resource_id = ?', $business->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('watch = ?', 1)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN)
			;

			foreach ($userTable->find($notifyUserIds) as $notifyUser) {
				// Don't notify self
				if ($notifyUser->isSelf($viewer)) {
					continue;
				}
				if ($notifyUser->isSelf($topicOwner)) {
					$type = 'ynbusinesspages_discussion_response';
				} else {
					$type = 'ynbusinesspages_discussion_reply';
				}
				$notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
						'message' => $this->view->BBCode($post->body),
				));
			}

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		// Redirect to the post
		$this->_redirectCustom($post);
	}

	public function stickyAction() {
		$topic = Engine_Api::_()->core()->getSubject();
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $topic->business_id);
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			$topic = Engine_Api::_()->core()->getSubject();
			$topic->sticky = ( null === $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
			$topic->save();

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$this->_redirectCustom($topic);
	}

	public function closeAction() {
		$topic = Engine_Api::_()->core()->getSubject();
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $topic->business_id);
		if (!$business -> isAllowed('discussion_delete', null, $topic))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			$topic = Engine_Api::_()->core()->getSubject();
			$topic->closed = ( null === $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
			$topic->save();

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$this->_redirectCustom($topic);
	}

	public function renameAction() {

		$topic = Engine_Api::_()->core()->getSubject();
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $topic->business_id);
		if (!$business -> isAllowed('discussion_delete', null, $topic))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$this->view->form = $form = new Ynbusinesspages_Form_Topic_Rename();

		if (!$this->getRequest()->isPost()) {
			$form->title->setValue(htmlspecialchars_decode($topic->title));
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			$title = $form->getValue('title');

			$topic = Engine_Api::_()->core()->getSubject();
			$topic->title = htmlspecialchars($title);
			$topic->save();

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic renamed.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
		));
	}

	public function deleteAction() {
		$topic = Engine_Api::_()->core()->getSubject();
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $topic->business_id);
		if (!$business -> isAllowed('discussion_delete', null, $topic))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$this->view->form = $form = new Ynbusinesspages_Form_Topic_Delete();

		if (!$this->getRequest()->isPost()) {
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			$topic = Engine_Api::_()->core()->getSubject();
			$business = $topic->getParent('ynbusinesspages_business');
			$topic->delete();

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		return $this->_forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic deleted.')),
				'layout' => 'default-simple',
				'parentRedirect' => $business->getHref(),
		));
	}

	public function watchAction() {
		$topic = Engine_Api::_()->core()->getSubject();
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $topic->business_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$watch = $this->_getParam('watch', true);
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynbusinesspages');
		$db = $topicWatchesTable->getAdapter();
		$db->beginTransaction();

		try {
			$isWatching = $topicWatchesTable
				->select()
				->from($topicWatchesTable->info('name'), 'watch')
				->where('resource_id = ?', $business->getIdentity())
				->where('topic_id = ?', $topic->getIdentity())
				->where('user_id = ?', $viewer->getIdentity())
				->limit(1)
				->query()
				->fetchColumn(0);

			if (false === $isWatching) {
				$topicWatchesTable->insert(array(
						'resource_id' => $business->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if ($watch != $isWatching) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $business->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$this->_redirectCustom($topic);
	}

}