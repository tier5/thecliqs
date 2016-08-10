<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_IndexController extends Core_Controller_Action_Standard
{
	protected $_package_id;

  public function init()
  {
    $content = $this->_getParam('content', '');
    $content_id = $this->_getParam('content_id', 0);

    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() ) {
      $page_url = $this->_getParam('page_id', '');
      if( null !== $page_url ) {
        $subject = Engine_Api::_()->page()->getPageByUrl($page_url);
        if( $subject && $subject->getIdentity() ) {
          $subject->setContentInfo($content, $content_id);
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }
    $request = $this->getRequest();
    if ($request->getActionName() == 'view' || $request->getActionName() == 'index') {
      if ($subject && $subject->isTimeline()) {
        if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline')) {
          $request->setActionName('timeline-view');
        }
      }
    }
  }

  public function createAction()
  {
		if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid() ) return;

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if ($settings->getSetting('page.package.enabled', 0)) {
      $subscription_id = $this->_getParam('id', 0);
      $subscription = Engine_Api::_()->getItem('page_subscription', $subscription_id);
      if( !$subscription || $subscription->page_id != 0 )
        return $this->_helper->redirector->gotoRoute(array('page_id' => 0), 'page_package_choose');
    }

    $this->_createDefaultContent();

		/**
		 * Declare variables
		 *
		 * @var $viewer User_Model_User
		 * @var $table Page_Model_DbTable_Pages
		 * @var $navigation Zend_Navigation
		 */
    $viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_()->getDbTable('pages', 'page');

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_main');

		$this->view->err = false;

    if (!$settings->getSetting('page.package.enabled', 0)) {
			$allowed_pages_arr = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'allowed_pages');
			$allowed_pages = $allowed_pages_arr[0] - 6;
			$existing_pages_count = $table->select()->from(array($table->info('name')), array('count' => new Zend_Db_Expr("count('page_id')")))->where('user_id = ?', $viewer->getIdentity())->query()->fetch();
			if( $existing_pages_count['count'] >= $allowed_pages) {
				$this->view->err = true;
				return;
			}
		}

    $this->view->form = $form = new Page_Form_Create();
    $this->view->setInfoJSON = json_encode($form->getSetInfo());
    $this->view->isMultiMode = count($form->getSetInfo()) > 1;
    $this->view->form->getSubForm('fields')->getElement('0_0_1')->setLabel('Category');
    $form->getDecorator('description')->setOption('escape', false);


    $js_terms = array();
    foreach (Engine_Api::_()->getDbTable('terms','page')->getTerms() as $item){
      $js_terms[$item->option_id] = $item->toArray();
    }
    $this->view->terms = $js_terms;
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($values = $this->getRequest()->getPost()) ) {
      $form->populate($values);
      return;
    }

    if ($table->checkUrl($values['url'])) {
    	$form->populate($values);
      $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL is already taken by other page.'));
      return ;
    }

    $values['url'] = strtolower(trim($values['url']));
    $values['url'] = preg_replace('/[^a-z0-9-]/', '-', $values['url']);
    $values['url'] = preg_replace('/-+/', "-", $values['url']);

    $db = $table->getAdapter();
    $db->beginTransaction();

    $page = $table->createRow();

    try
    {
      $values['user_id'] = $viewer->getIdentity();
      $values['parent_type'] = $this->getRequest()->getParam('parent_type', 'user');
      $values['parent_id'] =  $this->getRequest()->getParam('subject_id', $viewer->getIdentity());

    	$page->setFromArray($values);
      $page->set_id = $values['category'];
      $page->displayname = $page->title;
      $page->name = $page->url;
      $page->save();

      $raw_tags = preg_split('/[,]+/', $values['tags']);
      $tags = array();
      foreach ($raw_tags as $tag){
        $tag = trim(strip_tags($tag));
        if ($tag == "") {
          continue ;
        }
        $tags[] = $tag;
      }
      $page->tags()->addTagMaps($viewer, $tags);
      unset($values['tags']);

      $page->keywords = implode(",", $tags);

      $page->membership()->addMember($viewer)->setUserApproved($viewer)->setResourceApproved($viewer)->setUserTypeAdmin($viewer);
      $page->setAdmin($viewer);
      $page->getTeamList()->add($viewer);

    	if( $form->photo->getValue() ) {
        $page->setPhoto($form->photo);
      }

			$page->createContent();

      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($page);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');

      $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'likes' => 'Likes, Admins and Owner',
        'team' => 'Admins and Owner Only'
      );

      /**
       * @var $package Page_Model_Package
       * @var $authTb Authorization_Model_DbTable_Permissions
       */
      if ($settings->getSetting('page.package.enabled', 0)) {
        if ($subscription && $subscription->page_id == 0) {
          $package = $subscription->getPackage();
        } else {
          $package = Engine_Api::_()->getItemTable('page_package')->getDefaultPackage();
        }

        $page->package_id = $package->getIdentity();
        $page->featured = $package->featured;
        $page->sponsored = $package->sponsored;
        $page->approved = $package->autoapprove;
        $page->enabled = true;

        $view_options = array_intersect_key($availableLabels, array_flip($package->auth_view));
        $comment_options = array_intersect_key($availableLabels, array_flip($package->auth_comment));
        $posting_options = array_intersect_key($availableLabels, array_flip($package->auth_posting));
			} else {
        $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
        $page->approved = (int) $authTb->getAllowed('page', $viewer, 'auto_approve');
        $page->featured = (int) $authTb->getAllowed('page', $viewer, 'featured');
        $page->sponsored = (int) $authTb->getAllowed('page', $viewer, 'sponsored');
        $page->enabled = 1;

        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_comment');
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

        $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_posting');
        $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));
      }

      if ( $page->save() ) {
        $values = array(
          'auth_view' => key($view_options),
          'auth_comment' => key($comment_options),
          'auth_album_posting' => key($posting_options),
          'auth_blog_posting' => key($posting_options),
          'auth_disc_posting' => key($posting_options),
          'auth_doc_posting' => key($posting_options),
          'auth_event_posting' => key($posting_options),
          'auth_music_posting' => key($posting_options),
          'auth_video_posting' => key($posting_options)
        );
        $page->setPrivacy($values);

        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $page, 'page_create');
        if ($action) {
          $activityApi->attachActivity($action, $page);
        }
      }

      if ($settings->getSetting('page.package.enabled', 0) && $subscription->page_id == 0) {
        $subscription->page_id = $page->page_id;
        $subscription->save();
      }

      $db->commit();
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
      throw $e;
    }	catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->_redirectCustom(array('route' => 'page_team', 'action' => 'get-started', 'page_id' => $page->page_id));
  }

  public function validateAction()
  {
  	if( !$this->_helper->requireUser()->isValid() || !$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->checkRequire() ) {
  		return;
  	}

  	$url = $this->_getParam('url');

  	$url = strtolower(trim($url));
		$url = preg_replace('/[^a-z0-9-]/', '-', $url);
		$url = preg_replace('/-+/', "-", $url);

  	$table = Engine_Api::_()->getDbTable('pages', 'page');

    if( !strlen($url) ) {
      $this->view->success = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Url is Empty");
    } elseif ($table->checkUrl($url)){
  		$this->view->success = 0;
  		$this->view->message = Zend_Registry::get('Zend_Translate')->_("Page with this url is already exists.");
  	}else{
  		$this->view->success = 1;
  		$this->view->message = Zend_Registry::get('Zend_Translate')->_("This url is free.");
  	}

  	return;
  }

  public function indexAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid() ) return;

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }

  public function manageAction()
  {
  	if ( !$this->_helper->requireUser->isValid() ) return ;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_main');
    $viewer = $this->_helper->api()->user()->getViewer();
    $table = $this->_helper->api()->getDbtable('pages', 'page');
    $membershipTbl = $this->_helper->api()->getDbtable('membership', 'page');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $table->info('name')))
      ->joinLeft(array('m' => $membershipTbl->info('name')), "p.page_id = m.resource_id AND m.type = 'ADMIN'", array())
      ->joinLeft(array('fv' => 'engine4_page_fields_values'), "fv.item_id = p.page_id", array())
      ->joinLeft(array('fo' => 'engine4_page_fields_options'), "fo.option_id = fv.value", array('category' => 'label', 'category_id' => 'option_id'))
      ->where('m.user_id = ?', $viewer->getIdentity())
			->order('p.creation_date DESC')
      ->group('p.page_id')
    ;

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->owner = Engine_Api::_()->user()->getViewer();

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $ipp = $settings->getSetting('page.browse_count', 10);

    $paginator->setItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $page_ids = array();
    foreach ($paginator as $page) {
      $page_ids[] = $page->getIdentity();
    }
    $this->view->page_tags = Engine_Api::_()->page()->getPageTags($page_ids);
    $this->view->page_likes = Engine_Api::_()->like()->getLikesCount('page', $page_ids);
  }

  public function timelineViewAction()
  {
    /**
     * @var $pageObject Page_Model_Page
     */
    $pageObject = null;

    if (Engine_Api::_()->core()->hasSubject('page')) {
      $pageObject = Engine_Api::_()->core()->getSubject('page');
    }


    if (null == $pageObject) {
      $this->_forward('success', 'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'page_browse', true),
        'messages' => array($this->view->translate('Pages does not exists.'))
      ));
    }

    $viewer = $this->_helper->api()->user()->getViewer();

    if (!($pageObject->isOwner($viewer) || $pageObject->isEnabled())) {
      $this->_redirectCustom(array('route' => 'page_package_choose', 'page_id' => $pageObject->page_id));
    }

    if (!($pageObject->isOwner($viewer) || $pageObject->approved)) {
      $this->_redirectCustom(array('route' => 'page_browse'));
    }

    if (!$this->_helper->requireSubject()->isValid()) return 0;
    if (!$this->_helper->requireAuth()->setAuthParams($pageObject, $viewer, 'view')->isValid()) return 0;

    $pageObject->viewPage();
    $pageObject->description = stripslashes($pageObject->description);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('page');
    $path = dirname($path) . '/layouts';

    $layout = Zend_Layout::startMvc();
    $layout->setViewBasePath($path);

    $content = Engine_Content::getInstance();
    $contentTable = Engine_Api::_()->getDbtable('pages', 'page');
    $content->setStorage($contentTable);

    $this->view->headTitle()->setAutoEscape(false);
    $this->view->headMeta()->setAutoEscape(false);

    $tags = $pageObject->tags()->getTagMaps();
    $tagString = '';
    foreach ($tags as $tagmap) {
      if ($tagString !== '') $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    if (!empty($tagString)) {
      $this->view->headMeta()->appendName('keywords', $tagString);
    }

    if (!empty($pageObject->layout)) {
      $this->_helper->layout->setLayout($pageObject->layout);
    }

    $this->_helper->content->setContentName($pageObject->getIdentity())->setEnabled();
    return 0;
  }

	public function viewAction()
	{
		/**
		 * @var $pageObject Page_Model_Page
		 */
    $pageObject = null;

    if (Engine_Api::_()->core()->hasSubject('page')) {
      $pageObject = Engine_Api::_()->core()->getSubject('page');
    }

    if (null == $pageObject) {
      return $this->_forward('success', 'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'page_browse', true),
        'messages' => array($this->view->translate('Pages does not exists.'))
      ));
    }

		$viewer = Engine_Api::_()->user()->getViewer();
    if( !$pageObject->isDefaultPackageEnabled() ) {
      if ( $pageObject->isOwner($viewer) ) {
        $this->_redirectCustom(array('route' => 'page_package_choose', 'page_id' => $pageObject->page_id));
      } else {
        $this->_redirectCustom(array('route' => 'page_browse'));
      }
    }

		if ( !$pageObject->isOwner($viewer) && !$pageObject->approved ) {
			$this->_redirectCustom(array('route' => 'page_browse'));
		}

		if( !$this->_helper->requireSubject()->isValid() ) return 0;
    //if( !$this->_helper->requireAuth()->setAuthParams($pageObject, $viewer, 'view')->isValid() ) return 0;

		$pageObject->viewPage();
		$pageObject->description = stripslashes($pageObject->description);

		$path = Zend_Controller_Front::getInstance()->getControllerDirectory('page');
    $path = dirname($path) . '/layouts';

		$layout = Zend_Layout::startMvc();
		$layout->setViewBasePath($path);

		$content = Engine_Content::getInstance();
		$contentTable = Engine_Api::_()->getDbtable('pages', 'page');
    $content->setStorage($contentTable);

    $this->view->headTitle()->setAutoEscape(false);
    $this->view->headMeta()->setAutoEscape(false);

    $tags = $pageObject->tags()->getTagMaps();
    $tagString = '';
    foreach( $tags as $tagmap ) {
      if( $tagString !== '' ) $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    if( !empty($tagString) ) {
      $this->view->headMeta()->appendName('keywords', $tagString);
    }

    if( !empty($pageObject->layout) ) {
      $this->_helper->layout->setLayout($pageObject->layout);
    }

    $this->_helper->content->setContentName($pageObject->getIdentity())->setEnabled();

    if (!$pageObject->isAllowStyle()) {
      return 0;
    }
    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $pageObject->getType())
      ->where('id = ?', $pageObject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);
    if (null !== $row && !empty($row->style)) {
      $this->view->headStyle()->appendStyle($row->style);
    }

    return 0;
	}

	public function largeMapAction()
	{
		$page_id = (int)$this->_getParam('page_id');

		if ($page_id === null){
			$this->view->result = 0;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_("Undefined page.");
			return ;
		}

		$pageObject = Engine_Api::_()->getItem('page', $page_id);

		if(!$this->_helper->requireAuth()->setNoForward()->setAuthParams($pageObject, Engine_Api::_()->user()->getViewer(),'view')->checkRequire()){
			$this->view->result = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("No rights");
      return;
		}

		$table = Engine_Api::_()->getDbTable('pages', 'page');

    $select = $table
      ->select()->setIntegrityCheck(false)
      ->from(array('page' => 'engine4_page_pages'))
      ->joinLeft(array('marker' => 'engine4_page_markers'), 'marker.page_id = page.page_id', array('marker_id', 'latitude', 'longitude'))
      ->where('page.page_id = ?', $page_id);

    $this->view->page = $page = $table->fetchRow($select);
    $markers = array();

    if ($page->marker_id > 0){
	    $markers[0] = array(
				'marker_id' => $page->marker_id,
				'lat' => $page->latitude,
				'lng' => $page->longitude,
				'pages_id' => $page->page_id,
				'pages_photo' => $page->getPhotoUrl('thumb.normal'),
				'title' => $page->getTitle(),
				'desc' => substr($page->getDescription(),0,200),
	      'url' => $page->getHref()
			);

			$this->view->markers = Zend_Json_Encoder::encode($markers);
			$this->view->bounds = Zend_Json_Encoder::encode(Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers));
    }
	}

  public function printAction()
  {
    $page_id = (int)$this->_getParam('page_id', 0);
    $this->view->page = $page = Engine_Api::_()->getItem('page', $page_id);
    Engine_Api::_()->core()->setSubject($page);

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($page);

    /**
     * @var $table Page_Model_DbTable_Pages
     */
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $prefix = $table->getTablePrefix();
    $params = array('page_id' => $page_id);
    $select = $table->getSelect($params);
    $select->joinLeft($prefix.'page_markers', $prefix.'page_markers.page_id = '.$prefix.'page_pages.page_id', array('marker_id', 'longitude', 'latitude'));
    $page = $table->fetchRow($select);

    $markers = array();
    if ($page->marker_id > 0) {
      $markers[0] = array(
        'marker_id' => $page->marker_id,
        'lat' => $page->latitude,
        'lng' => $page->longitude,
        'pages_id' => $page->page_id,
        'pages_photo' => $page->getPhotoUrl('thumb.normal'),
        'title' => $page->getTitle(),
        'desc' => Engine_String::substr($page->getDescription(),0,200),
        'url' => $page->getHref()
      );

      $this->view->markers = Zend_Json_Encoder::encode($markers);
      $this->view->bounds = Zend_Json_Encoder::encode(Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers));
    }
  }

  public function claimAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_main');

    $page_id = $this->getRequest()->getParam('id');
    $form = new Page_Form_ClaimPage();

    if(intval($page_id) > 0) {
      $row = Engine_Api::_()->getItemTable('page')->findRow($page_id);
      $form->getElement('title')->setDescription('');
      $form->getElement('title')->setValue($row->getTitle());
      $form->getElement('page_id')->setValue($page_id);
    }

    $this->view->form = $form;

    $translate = Zend_Registry::get('Zend_Translate');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($values = $this->getRequest()->getPost()) ) {
      $form->populate($values);
      return;
    }
    /**
     * @var $table Page_Model_DbTable_Claims
     **/
    $table = Engine_Api::_()->getDbTable('claims', 'page');

    $page_id = $values['page_id'];
    $claimed = $table->checkClaim($page_id);

    if ($claimed) {
      $status = $claimed->status;
      if ($status == 'pending') {
        $form->addError($translate->_('You have already claimed this page, your claim is under consideration of Administrator.'));
        return ;
      } elseif ($status == 'declined') {
        $form->addError($translate->_('You have already claimed this page, your claim has been declined by Administrator.'));
        return ;
      }
    }

    $page = Engine_Api::_()->getItem('page', $page_id);
    if ($page === null) {
      $form->addError($translate->_('This page not found, please choose page from drop down choice list.'));
      return;
    } elseif ($page->title != $values['title']) {
      $form->addError($translate->_('This page has not been found, please choose page from drop down choice list.'));
      return;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    $claiming = $table->createRow();

    try {
      $values['user_id'] = $viewer->getIdentity();
      $claiming->setFromArray($values);
      $claiming->save();
      $db->commit();
      $values['page_id'] = 0;
      $values['title'] = '';
      $form->populate($values);
      $form->addNotice($translate->_('Your Notice successfully sent.'));
    } catch(Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function suggestAction()
  {
    $pages = $this->getPagesByText($this->_getParam('text'), $this->_getParam('limit', 40));

    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' ) {
      foreach( $pages as $page ) {
        $data[] = $page->title;
      }
    } else {
      foreach( $pages as $page ) {
        $pagePhoto = $this->view->itemPhoto($page, 'thumb.icon');
        $data[] = array(
          'id' => $page->page_id,
          'label' => $page->title,
          'photo' => $pagePhoto
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  public function termsAction()
  {
    if ( !$this->_helper->requireUser->isValid() ) return ;
    $this->_helper->layout->setLayout('default-simple');
  }

  private function getPagesByText($text = null, $limit = 10)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    /**
     * @var $table Page_Model_DbTable_Pages
     **/
    $table = Engine_Api::_()->getDbtable('pages', 'page');
    $claimersTbl = Engine_Api::_()->getDbtable('settings', 'user');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $table->info('name')))
      ->joinLeft(array('c' => $claimersTbl->info('name')), 'p.user_id = c.user_id', array())
      ->where('c.name = ?', 'claimable_page_creator')
      ->where('p.user_id <> ?', $viewer->getIdentity())
      ->order('p.title ASC')
      ->limit($limit);

    if( $text ) {
      $select->where('p.title LIKE ?', '%'.$text.'%');
    }

    return $table->fetchAll($select);
  }

  private function _createDefaultContent()
  {
    $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $page = "default";

    $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
		$contentTable = Engine_Api::_()->getDbtable('content', 'page');

    $contentDefault = $contentTable->fetchAll($contentTable->select()->where('page_id=?', $pageObject->getIdentity()));

    if(count($contentDefault) == 0) {
      $pageTable->createContentFirstTime($pageObject->getIdentity());
    }
  }

  public function browsereviewsAction()
  {
    //Render Layout Reviews
    $this->_helper->content
      ->setNoRender()
      ->setEnabled()
    ;
  }
}