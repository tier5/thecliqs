<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_WatchLaterController extends Core_Controller_Action_Standard {
    public function init() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
    }

    public function indexAction() {
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function addToAction() {

        // Disable layout and view renderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
            null !== ($video = Engine_Api::_()->getItem('ynultimatevideo_video', $video_id)) &&
            $video instanceof Ynultimatevideo_Model_Video) {
            Engine_Api::_()->core()->setSubject($video);
        }

        $translate = Zend_Registry::get('Zend_Translate');

        if (!$this->_helper->requireSubject('ynultimatevideo_video')->isValid()) {
            $data = array(
                'result' => 0,
                'added' => 0,
                'message' => $translate->_('The video doesn\'t exist'),
            );
            return $this->_helper->json($data);
        }

        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
            $data = array(
                'result' => 0,
                'added' => 0,
                'message' => $translate->_('You do not have the authorization to view this video'),
            );
            return $this->_helper->json($data);
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $added = $watchlaterTable = Engine_Api::_() -> getDbTable('watchlaters', 'ynultimatevideo')->isAdded($video_id, $viewer_id);

        if ($added) {
            if (Engine_Api::_()->ynultimatevideo()->removeVideoFromWatchLater($video_id, $viewer_id)) {
                $data = array(
                    'result' => '1',
                    'added' => '0',
                    'message' => $translate->_('This video has been remove from your Watch Later list.')
                );
            } else {
                $data = array(
                    'result' => '0',
                    'added' => '0',
                    'message' => ''
                );
            }
        } else {
            if (Engine_Api::_()->ynultimatevideo()->addVideoToWatchLater($video_id, $this->view->viewer->getIdentity())) {
//                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
//                $action = $actionTable->addActivity($viewer, $video, 'ynultimatevideo_add_watchlater');
//                
//                if ($action != null) {
//                    $actionTable->attachActivity($action, $video);
//                }
//                
//                foreach ($actionTable->getActionsByObject($video) as $action) {
//                    $actionTable->resetActivityBindings($action);
//                }
                
                $data = array(
                    'result' => '1',
                    'added' => '1',
                    'message' => $translate->_('This video has been added to Watch Later list successfully.')
                );
            } else {
                $data = array(
                    'result' => '0',
                    'added' => '0',
                    'message' => ''
                );
            }
        }
        return $this->_helper->json($data);
    }

    public function removeAction() {
        if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
            null !== ($video = Engine_Api::_()->getItem('ynultimatevideo_video', $video_id)) &&
            $video instanceof Ynultimatevideo_Model_Video) {
            Engine_Api::_()->core()->setSubject($video);
        }

        if (!$this->_helper->requireSubject('ynultimatevideo_video')->isValid()) {
            return;
        }

        // In smooth-box
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Ynultimatevideo_Form_Remove(
            array(
                'title' => 'Remove video',
                'description' => 'Are you sure you want to remove this video from your Watch Later list?'
            )
        );

        if (!$video) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("The video doesn't exist.");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }

        if (Engine_Api::_()->ynultimatevideo()->removeVideoFromWatchLater($video->getIdentity(), $this->view->viewer->getIdentity())) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('The video has been removed from your Watch Later list.');
        } else {
            $this->view->status = false;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('There is an error occurred, please try again !!!');
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->view->message)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }
}