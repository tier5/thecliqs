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

class Headvancedalbum_AdminManageController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('headvancedalbum_admin_main', array(), 'headvancedalbum_admin_main_manage');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $album = Engine_Api::_()->getItem('album', $value);
                    $album->delete();
                }
            }
        }

        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->getItemTable('album')->getAlbumPaginator(array(
            'orderby' => 'admin_id',
        ));
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function photosAction()
    {
        //        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
        //            return $this->_helper->content->setNoRender();
        //        }
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->album_id = $album_id = $this->_getParam('album_id');
        $this->view->album = $album = Engine_Api::_()->getItem('album', $album_id);
        $this->view->owner_id = $album->owner_id;
        //        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->isValid()) {
        //            return $this->_helper->content->setNoRender();
        //        }
        // Prepare data
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);

        $this->view->just_items = $this->_getParam('just_items', false);

        if ($this->_getParam('format') != 'html') {
            $this->_helper->layout->setLayout('default-simple');
        }
    }

    public function setPhotoAction()
    {
        if (!$this->_getParam('photo_id', false) || null == ($photo = Engine_Api::_()->getItem('album_photo', $this->_getParam('photo_id')))) {
            $this->view->status = false;
            return;
        }

        $photo->he_featured = !$photo->he_featured;
        $photo->save();

        $this->view->status = true;
        $this->view->result = $photo->he_featured;
    }

    public function setAlbumAction()
    {
        if (!$this->_getParam('album_id', false) || null == ($album = Engine_Api::_()->getItem('album', $this->_getParam('album_id')))) {
            $this->view->status = false;
            return;
        }

        $album->he_featured = !$album->he_featured;
        $album->save();

        $this->view->status = true;
        $this->view->result = $album->he_featured;
    }

  public function albumsAction()
  {
      if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
          return $this->_helper->content->setNoRender();
      }

      if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) {
          return $this->_helper->content->setNoRender();
      }

      $this->view->page = $page = $this->_getParam('page', 1);

      $subject = Engine_Api::_()->core()->getSubject('user');
      $table = Engine_Api::_()->getItemTable('album');

      $select = $table->select()
          ->where('owner_id = ?', $subject->getIdentity())
          ->order('view_count DESC');

      $this->view->user = $subject;
      $this->view->just_items = $this->_getParam('just_items', false);
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage(9);
      $paginator->setCurrentPageNumber($page);

      if ($this->_getParam('format') != 'html') {
          $this->_helper->layout->setLayout('default-simple');
      }
  }



}
