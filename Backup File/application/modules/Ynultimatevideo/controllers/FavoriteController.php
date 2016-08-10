<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_FavoriteController extends Core_Controller_Action_Standard {

    public function init() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    }

    //put your code here
    public function indexAction() {
        // Render
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
                'message' => $translate->_('The video doesn\'t exist'),
            );
            return $this->_helper->json($data);
        }

        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
            $data = array(
                'result' => 0,
                'message' => $translate->_('You do not have the authorization to view this video.'),
            );
            return $this->_helper->json($data);
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $added = $watchlaterTable = Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo')->isAdded($video_id, $viewer_id);

        if ($added) {
            if (Engine_Api::_()->ynultimatevideo()->removeVideoFromFavorite($video_id, $viewer_id)) {
                $data = array(
                    'result' => '1',
                    'added' => '0',
                    'message' => $translate->_('This video has been remove from your Favorite Videos list.')
                );
            } else {
                $data = array(
                    'result' => '0',
                    'added' => '0',
                    'message' => ''
                );
            }
        } else {
            $favorite = Engine_Api::_()->ynultimatevideo()->addVideoToFavorite($video->getIdentity(), $this->view->viewer->getIdentity());
            if ($favorite) {
                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($favorite, 'registered', 'view', true);
                $auth->setAllowed($favorite, 'registered', 'comment', true);

                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $actionTable->addActivity($viewer, $favorite, 'ynultimatevideo_add_favorite');

                if ($action != null) {
                    $actionTable->attachActivity($action, $video);
                }

                foreach ($actionTable->getActionsByObject($favorite) as $action) {
                    $actionTable->resetActivityBindings($action);
                }

                $data = array(
                    'result' => 1,
                    'added' => 1,
                    'message' => $translate->_('This video has been added to your Favorite Videos list.'),
                );
            } else {
                $data = array(
                    'result' => 0,
                    'added' => 0,
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

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Ynultimatevideo_Form_Remove(
            array(
                'remove_title' => 'Remove video',
                'remove_description' => 'Are you sure you want to remove this video from your Favorite Videos list?'
            )
        );

        if (!$video) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exist.");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }

        if (Engine_Api::_()->ynultimatevideo()->removeVideoFromFavorite($video->getIdentity(), $this->view->viewer->getIdentity())) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('The video has been removed from your Favorite videos list.');
        } else {
            $this->view->status = false;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('There is an error occurred, please try again.');
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->view->message)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

}

?>