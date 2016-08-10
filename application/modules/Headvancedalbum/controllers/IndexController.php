<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2013-01-17 15:23:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;


    $owner = null;
    if ($this->_getParam('owner')){
      $owner = Engine_Api::_()->getItemByGuid($this->_getParam('owner'));
    }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'headvancedalbum')->getPhotosPaginator(array(
      'category' => $this->_getParam('category_id', 'recent'),
      'search_photos' => $this->_getParam('search'),
      'tagged' => $this->_getParam('tagged'),
      'owner' => $owner,
      'search' => 1,
      'type' => $this->_getParam('type')
    ));

    $paginator->setItemCountPerPage(15);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $this->view->photos = $photos = Engine_Api::_()->getDbTable('photos', 'headvancedalbum')->getPhotosByPaginator($paginator);

    $categories = array();
    foreach (Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc() as $key => $item) {
      if ($key == 0) {
        continue;
      }
      $categories[$key] = $item;
    }
    $this->view->categories = $categories;

    $this->view->is_next = (int)(isset($paginator->getPages()->next));

    if ($this->_getParam('page') > $paginator->count()){ // the page is not exists
      $this->view->body = '';
      $this->view->is_next = 0;
      return ;
    }

    if ($this->_getParam('format') == 'json') {

      $this->view->body = $this->view->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');
      $this->view->item_count = $paginator->getCurrentItemCount();

    } else {
      $this->_helper->content
        ->setEnabled();
    }
  }

  public function browseAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) return;

    $owner = null;
    if ($this->_getParam('owner')){
      $owner = Engine_Api::_()->getItemByGuid($this->_getParam('owner'));
    }

    $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumPaginator(array(
      'category' => $this->_getParam('category_id', 'recent'),
      'search_albums' => $this->_getParam('search'),
      'tagged' => $this->_getParam('tagged'),
      'owner' => $owner,
      'search' => 1,
      'type' => $this->_getParam('type')
    ));
    $paginator->setItemCountPerPage(24);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $this->view->albums = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumsByPaginator($paginator);

    $categories = array();
    foreach (Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc() as $key => $item) {
      if ($key == 0) {
        continue;
      }
      $categories[$key] = $item;
    }
    $this->view->categories = $categories;

    $this->view->is_next = (int)(isset($paginator->getPages()->next));
    if ($this->_getParam('page') > $paginator->count()){ // the page is not exists
      $this->view->body = '';
      $this->view->is_next = 0;
      return ;
    }

    if ($this->_getParam('format') == 'json') {

      $this->view->body = $this->view->render('application/modules/Headvancedalbum/views/scripts/_albumItems.tpl');
      $this->view->item_count = $paginator->getCurrentItemCount();

    } else {
      $this->_helper->content
        ->setEnabled();
    }

  }

  public function manageAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('headvancedalbum_main');
  }

  public function uploadAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('headvancedalbum_main');


    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) return $this->_forward('upload-photo', null, null, array('format' => 'json'));

    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) return;


    // Get form
    $this->view->form = $form = new Headvancedalbum_Form_Album();

    if (!$this->getRequest()->isPost()) {
      if (null !== ($album_id = $this->_getParam('album_id'))) {
        $form->populate(array(
          'album' => $album_id
        ));
      }
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Api::_()->getItemTable('album')->getAdapter();
    $db->beginTransaction();

    try {
      $album = $form->saveValues();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'album_id' => $album->album_id), 'album_specific', true);

  }


  public function uploadPhotoAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      ));
      $photo->save();

      $photo->order = $photo->photo_id;
      $photo->setPhoto($_FILES['Filedata']);
      $photo->save();

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;

      $db->commit();

    } catch( Album_Model_Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      throw $e;
      return;

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }


  public function resizeAction()
  {
    if($this->getRequest()->isPost()) {
      $width = $this->_getParam('width', 0);
      $sample = $this->_getParam('sample', 0);

      if(!$width || !$sample)
        return false;
    }
  }



  // Album View
  public function viewAction()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;

    if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id)) )
    {
      Engine_Api::_()->core()->setSubject($photo);
    }

    else if( 0 !== ($album_id = (int) $this->_getParam('album_id')) &&
        null !== ($album = Engine_Api::_()->getItem('album', $album_id)) )
    {
      Engine_Api::_()->core()->setSubject($album);
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    if( !$this->_helper->requireSubject('album')->isValid() ) return;

    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid() ) return;

    // Prepare params
    $this->view->page = $page = $this->_getParam('page');

    // Prepare data
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
      'album' => $album,
    ));
    $paginator->setItemCountPerPage(8);
    $paginator->setCurrentPageNumber($page);

    // Do other stuff
    $this->view->mine = true;
    $this->view->canEdit = $this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->checkRequire();
    if( !$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer()) ) {
      $album->getTable()->update(array(
        'view_count' => new Zend_Db_Expr('view_count + 1'),
      ), array(
        'album_id = ?' => $album->getIdentity(),
      ));
      $this->view->mine = false;
    }

    $this->view->is_next = (int)(isset($paginator->getPages()->next));
    if ($this->_getParam('page') > $paginator->count()){ // the page is not exists
      $this->view->body = '';
      $this->view->is_next = 0;
      return ;
    }

    if ($this->_getParam('format') == 'json'){

      // render only part
      $this->view->body = $this->view->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');

    } else {
      // Render
      $this->_helper->content
          //->setNoRender()
              ->setEnabled();
    }


  }



}