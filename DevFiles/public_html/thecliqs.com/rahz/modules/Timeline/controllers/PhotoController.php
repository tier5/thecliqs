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

class Timeline_PhotoController extends Core_Controller_Action_Standard
{
    protected $_type;

    public function init()
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            // Can specifiy custom id
            $id = $this->_getParam('id', null);
            $subject = null;
            if (null === $id) {
                $subject = Engine_Api::_()->user()->getViewer();
            } else {
                $subject = Engine_Api::_()->getItem('user', $id);
            }

            /**
             * @var $subject Timeline_Model_User
             */
            $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
            Engine_Api::_()->core()->setSubject($subject);
        }

        // Set up require's
        $this->_helper->requireUser();
        $this->_helper->requireSubject('user');
        $this->_helper->requireAuth()->setAuthParams(
            null,
            null,
            'edit'
        );

        $this->_type = $this->_getParam('type', 'cover');

        if (!$subject->isPhotoTypeSupported($this->_type)) {
            return $this->_helper->content->setNoRender();
        }

        $this->view->type = $this->_type;

        $this->_helper->contextSwitch
//      ->addActionContext('albums', 'smoothbox')
//      ->addActionContext('photos', 'smoothbox')
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

    public function photosAction()
    {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return $this->_helper->content->setNoRender();
        }

        $this->view->page = $page = $this->_getParam('page', 1);

        $this->view->album_id = $album_id = $this->_getParam('album_id');
        $this->view->album = $album = Engine_Api::_()->getItem('album', $album_id);
        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->isValid()) {
            return $this->_helper->content->setNoRender();
        }

        $this->view->user = $subject = Engine_Api::_()->core()->getSubject('user');

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

    public function getAction()
    {
        /**
         * @var $subject Timeline_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject('user');
        if (!$subject->hasTimelinePhoto($this->_type)) {
            return $this->_helper->content->setNoRender();
        }

        $this->view->albumPhoto = $subject->getTimelineAlbumPhoto($this->_type);
    }

    public function setAction()
    {
        /**
         * @var $subject Timeline_Model_User
         * @var $table Timeline_Model_DbTable_Settings
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');

        if (
            !$this->_getParam('photo_id', false) ||
            null == ($photo = Engine_Api::_()->getItem('album_photo', $this->_getParam('photo_id'))) ||
            !$subject->setTimelinePhoto($photo, $this->_type)
        ) {
            $this->view->status = false;
            return;
        }

        $table->setSetting($subject, 'timeline-' . $this->_type . '-photo-id', $photo->getIdentity());

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
        $subject = Engine_Api::_()->core()->getSubject('user');
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');
        $this->view->status = (boolean)$table->setSetting($subject, 'timeline-' . $this->_type . '-position', serialize($position));
    }

    public function uploadAction()
    {
        /**
         * @var $subject Timeline_Model_User
         * @var $table Timeline_Model_DbTable_Settings
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');
        $row_name = trim($this->_type . '_id');

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

                $subject->setTimelinePhoto($fileElement, $this->_type);
                $photo_id = (int)$subject->$row_name;

                $iMain = Engine_Api::_()->getItem('storage_file', $photo_id);

                // Insert activity
                $activity_type = 'post_self';
                $body = '';

                if ($this->_type == 'cover') {
                    $activity_type = 'cover_photo_update';
                    $body = '{item:$subject} added a new cover photo.';
                } elseif ($this->_type == 'born') {
                    $activity_type = 'birth_photo_update';
                    $body = '{item:$subject} added a new birth photo.';
                }

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($subject, $subject, $activity_type, $body);

                // Hooks to enable albums to work
                if ($action) {
                    $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onUser' . ucfirst($this->_type) . 'PhotoUpload', array(
                        'user' => $subject,
                        'file' => $iMain,
                    ));

                    $attachment = $event->getResponse();
                }

                if (!$attachment)
                    $attachment = $iMain;
                else
                    $table->setSetting($subject, 'timeline-' . $this->_type . '-photo-id', $attachment->getIdentity());

                // We have to attach the user himself w/o album plugin
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);

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

        $user = Engine_Api::_()->core()->getSubject();
        $row_name = $this->_type . '_id';
        $user->$row_name = 0;
        if ($this->_type == 'cover') {
            $user->mini_cover_id = 0;
        }
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
