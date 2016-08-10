<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
  }

  public function indexAction()
  {
    $this->view->isPageOn = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_settings');
    $this->view->form = $form = new Timeline_Form_Admin_Settings_Global();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    /**
     * @var $settings Core_Api_Settings
     */
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->__set('timeline.usage', $form->getValue('usage'));
    $settings->__set('timeline.usageonpage', $form->getValue('usage_on_page'));
    $settings->__set('timeline.menuitems', $form->getValue('menuitems'));

    $form->addNotice('TIMELINE_Settings have been successfully saved');
  }

  public function thumbIconsAction()
  {
    $this->view->isPageOn = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page');
    /**
     * @var $timeline  Timeline_Api_Core
     * @var $thumbsTable Timeline_Model_DbTable_Thumbs
     */
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_thumbIcons');

    $contentTb = Engine_Api::_()->getDbTable('content', 'core');
    $pagesTb = Engine_Api::_()->getDbTable('pages', 'core');

    $select = $pagesTb->select()->where('name=?', 'timeline_profile_index')->limit(1);
    $page = $pagesTb->fetchRow($select);

    $select = $contentTb->select()->where('page_id=?', $page->getIdentity())->where('name=?', 'core.container-tabs')->limit(1);
    if (null != ($tabs = $contentTb->fetchRow($select))) {
      $select = $contentTb->select()
        ->from($contentTb->info('name'), array('name', 'params'))
        ->where('page_id=?', $page->getIdentity())
        ->where('parent_content_id=?', $tabs->content_id)
        ->where('type=?', 'widget');

      $widgets =  $contentTb->fetchAll($select);
    }

    $thumbsTable = Engine_Api::_()->getDbTable('thumbs', 'timeline');

    foreach($widgets as $widget) {
      $check = $thumbsTable->fetchRow($thumbsTable->select()
        ->where('type = ?', $widget->name)
        ->limit(1));
      if (!$check) {
        $thumbsTable->insert(array('type' => $widget->name, 'title' => $widget->params['title']));
      }
    }
    $this->view->widgets = $thumbsTable->fetchAll($thumbsTable->select()->where('page = ?', 0));
  }

  public function editAction()
  {
    $this->view->isPageOn = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page');
    $this->view->page = $page = $this->_getParam('p');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('timeline_admin_main', array(), $page ? 'timeline_admin_main_pageIcons' : 'timeline_admin_main_thumbIcons');

    $this->view->type = $type = $this->_getParam('type');
    $thumbsTable = Engine_Api::_()->getDbTable('thumbs', 'timeline');

    $thumb = $thumbsTable->fetchRow($thumbsTable->select()
      ->where('type = ?', $type)
      ->limit(1));


    Engine_Api::_()->core()->setSubject($thumb);


    $this->view->form = $form = new Timeline_Form_Admin_Settings_ThumbIcons();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    if( $form->Filedata->getValue() !== null ) {
      $thumb->setPhoto($form->Filedata);
    }

    // Resizing a photo
    else if( $form->getValue('coordinates') !== '' ) {
      $storage = Engine_Api::_()->storage();

      $iProfile = $storage->get($thumb->photo_id, 'thumb.profile');
      $iThumb = $storage->get($thumb->photo_id, 'thumb.icon');

      // Read into tmp file
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);

      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
      $image = Engine_Image::factory();
      $image->open($pName)
        ->resample($x, $y, $w, $h, 115, 76)
        ->write($iName)
        ->destroy();

      $iThumb->store($iName);

      // Remove temp files
      @unlink($iName);
    }

  }

  public function removePhotoAction()
  {
    $type = $this->_getParam('type');
    $this->view->form = $form = new Timeline_Form_Admin_Settings_RemovePhoto();

    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $thumbTable = Engine_Api::_()->getDbTable('thumbs', 'timeline');
    $thumb = $thumbTable->fetchRow($thumbTable->select()
      ->where('type = ?', $type)
      ->limit(1));

    $thumb->photo_id = 0;
    $thumb->save();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.');

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.'))
    ));

  }

  public function pageIconsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_pageIcons');

    $dbAdapter = Zend_Db_Table::getDefaultAdapter();
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')) {
      $this->_helper->content
        ->setNoRender()
        ->setEnabled();
    }
    else {
      $widgets = $dbAdapter->fetchAll($dbAdapter->select()
        ->from('engine4_page_modules', array('widget'))
      );
      $thumbsTable = Engine_Api::_()->getDbTable('thumbs', 'timeline');

      foreach($widgets as $widget) {
        $check = $thumbsTable->fetchRow($thumbsTable->select()
          ->where('type = ?', $widget['widget'])
          ->limit(1));
        if (!$check) {
          $thumbsTable->insert(array('type' => $widget['widget'], 'title' => $this->getTitle($widget), 'page' => 1));
        }
      }
      $this->view->widgets = $thumbsTable->fetchAll($thumbsTable->select()->where('page = ?', 1));
    }
  }
  private function getTitle($widget)
  {
    $title = explode('-', $widget['widget']);
    $title[1][0] = strtoupper($title[1][0]);
    return $title[1].'s';

  }

}