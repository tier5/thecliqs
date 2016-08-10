<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CoverController.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_PagePhotoController extends Core_Controller_Action_Standard
{
    protected $_type;

    public function init()
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            // Can specifiy custom id
            $id = $this->_getParam('id', null);
            $subject = null;
            if (null === $id) {
                return $this->_helper->content->setNoRender();
            } else {
                $subject = Engine_Api::_()->getItem('page', $id);
            }

            Engine_Api::_()->core()->setSubject($subject);
        }

        $this->_type = $this->_getParam('type', 'cover');

        $this->view->type = $this->_type;

        $this->_helper->contextSwitch
            ->addActionContext('get', 'json')
            ->addActionContext('set', 'json')
            ->addActionContext('position', 'json')
            ->initContext();
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

        $subject = Engine_Api::_()->core()->getSubject('page');

        $table = Engine_Api::_()->getItemTable('album');

        $select = $table->select()
            ->where('owner_id = ?', $subject->getOwner()->getIdentity())
            ->order('view_count DESC');

        $albums = $table->fetchAll($select);
        $albums = $albums->toArray();

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('pagealbum')) {
            $table = Engine_Api::_()->getItemTable('pagealbum');
            $select = $table->select()
                ->where('page_id = ?', $subject->getIdentity())
                ->order('view_count DESC');

            $page_albums = $table->fetchAll($select);
            $page_albums = $page_albums->toArray();
            if (!empty($page_albums)) {
                $albums = array_merge($albums, $page_albums);
            }
        }

        $this->view->user = $subject->getOwner();
        $this->view->just_items = $this->_getParam('just_items', false);
        $this->view->paginator = $paginator = Zend_Paginator::factory($albums);
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);

        if ($this->_getParam('format') != 'html') {
            $this->_helper->layout->setLayout('default-simple');
        }
    }

    public function photosAction()
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return $this->_helper->content->setNoRender();
        }

        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->album_type = $album_type = $this->_getParam('album_type', 'album');
        $this->view->album_id = $album_id = $this->_getParam('album_id');

        $this->view->album = $album = Engine_Api::_()->getItem($album_type, $album_id);
        if ($album_type != 'pagealbum' && !$this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->isValid()) {
            return $this->_helper->content->setNoRender();
        }

        $subject = Engine_Api::_()->core()->getSubject('page');
        $this->view->user = $subject->getOwner();
        $this->view->subject = $subject;

        // Prepare data
        if ($album_type == 'album') {
            $photoTable = Engine_Api::_()->getItemTable('album_photo');
            $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
                'album' => $album,
            ));
        } else {
            $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        }

        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);

        $this->view->just_items = $this->_getParam('just_items', false);

        if ($this->_getParam('format') != 'html') {
            $this->_helper->layout->setLayout('default-simple');
        }
    }

    public function getAction()
    {
        /**
         * @var $subject Timeline_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject('page');
        //        if (!$subject->hasTimelinePhoto($this->_type)) {
        //            return $this->_helper->content->setNoRender();
        //        }

        $this->view->albumPhoto = $subject->getTimelineAlbumPhoto($this->_type);
    }

    public function setAction()
    {
        $subject = Engine_Api::_()->core()->getSubject('page');
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');

        $album_type = $this->_getParam('album_type', 'album');
        $photo_id = $this->_getParam('photo_id', false);

        if(!$photo_id) {
            $this->view->status = false;
            $this->view->code = 1;
            return;
        }

        if($album_type == 'album') {
            $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
        } else {
            $photo = Engine_Api::_()->getItem('pagealbumphoto', $photo_id);
        }

        if (!$photo || !$subject->setTimelinePhoto($photo, $this->_type)) {
            $this->view->status = false;
            $this->view->code = 2;
            return;
        }

        $table->setSetting($subject->getOwner(), 'timeline-page' . $this->_type . '-photo-id', $photo->getIdentity());

        $this->view->status = true;
        $row_name = $this->_type . '_id';
        $this->view->photo_id = $subject->$row_name;
    }

    public function positionAction()
    {
        $position_tmp = $this->_getParam('position', array());

        $position = array('top' => 0, 'left' => 0);

        if (isset($position_tmp['top'])) {
            $position['top'] = (int)$position_tmp['top'];
        }

        if (isset($position_tmp['left'])) {
            $position['left'] = (int)$position_tmp['left'];
        }

        /**
         * @var $subject Timeline_Model_User
         * @var $table Timeline_Model_DbTable_Settings
         */
        $subject = Engine_Api::_()->core()->getSubject('page');
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');
        $this->view->status = (boolean)$table->setSetting($subject->getOwner(), 'timeline-page-' . $this->_type . '-position-'.$subject->getIdentity(), serialize($position));
    }

    public function uploadAction()
    {
        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');
        $row_name = trim($this->_type . '_id');

        $action = null;
        $attachment = null;

        // Get form
        $this->view->form = $form = new Timeline_Form_Photo_Upload();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Uploading a new photo
        if ($form->Filedata->getValue() !== null) {
            $db = $subject->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $fileElement = $form->Filedata;

                /**
                 * var $subject Page_Model_Page
                 */
                $subject->setTimelinePhoto($fileElement, $this->_type);
                $photo_id = (int)$subject->$row_name;

                $iMain = Engine_Api::_()->getItem('storage_file', $photo_id);

                // Insert activity
                $activity_type = 'post_self';
                $body = '';

                if ($this->_type == 'cover') {
                    $activity_type = 'page_cover_photo_update';
                    $body = '{item:$subject} added a new cover photo.';
                }

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $activity_type, $body);

                // Hooks to enable albums to work
                if ($action) {
                    $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onUser' . ucfirst($this->_type) . 'PagePhotoUpload', array(
                        'page' => $subject,
                        'file' => $iMain,
                    ));
                    $attachment = $event->getResponse();
                }

                if (!$attachment) {
                    $attachment = $iMain;
                }
                else {
                    $table->setSetting($subject->getOwner(), 'timeline-page-' . $this->_type . '-photo-id', $attachment->getIdentity());
                }

                // We have to attach the user himself w/o album plugin
                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                }

                $db->commit();
                $this->view->photo_id = $attachment->getIdentity();
            }

                // If an exception occurred within the image adapter, it's probably an invalid image
            catch (Engine_Image_Adapter_Exception $e)
            {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            }

                // Otherwise it's probably a problem with the database or the storage system (just throw it)
            catch (Exception $e)
            {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function removeAction()
    {
        // Get form
        $this->view->form = $form = new Timeline_Form_Photo_Remove();

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $user = Engine_Api::_()->core()->getSubject('page');
        $row_name = $this->_type . '_id';
        $user->$row_name = 0;
        $user->save();

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.');

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.'))
        ));
    }
}
