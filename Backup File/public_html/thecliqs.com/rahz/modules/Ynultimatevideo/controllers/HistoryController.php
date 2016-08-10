<?php
class Ynultimatevideo_HistoryController extends Core_Controller_Action_Standard {
	public function init() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	}
	
	public function indexAction() {
		$this->_helper->content->setEnabled()->setNoRender();
	}
	
	public function removeAction() {
        // In smoothbox
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
                'description' => 'Are you sure you want to remove this video from your History?'
            )
        );

        // Check post
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

        if (Engine_Api::_()->ynultimatevideo()->removeItemFromHistory($video->getIdentity(), $this->view->viewer->getIdentity(), 'ynultimatevideo_video')) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('The video has been removed from your your History.');
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

	public function removeplaylistAction() {
        // In smoothbox
        if (0 !== ($playlist_id = (int) $this->_getParam('playlist_id')) &&
            null !== ($playlist = Engine_Api::_()->getItem('ynultimatevideo_playlist', $playlist_id)) &&
            $playlist instanceof Ynultimatevideo_Model_Playlist) {
            Engine_Api::_()->core()->setSubject($playlist);
        }

        if (!$this->_helper->requireSubject('ynultimatevideo_playlist')->isValid()) {
            return;
        }

        // In smooth-box
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Ynultimatevideo_Form_Remove(
            array(
                'title' => 'Remove playlist',
                'description' => 'Are you sure you want to remove this playlist from your History?'
            )
        );

        // Check post
        if (!$playlist) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Playlist doesn't exist.");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }

        if (Engine_Api::_()->ynultimatevideo()->removeItemFromHistory($playlist->getIdentity(), $this->view->viewer->getIdentity(), 'ynultimatevideo_playlist')) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Playlist has been removed from your your History.');
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

	public function multiremoveAction() {
		$this->_helper->layout->setLayout('default-simple');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $table = Engine_Api::_()->getDbTable('history','ynultimatevideo');
            $ids_array = explode(",", $ids);
			$table->removeHistory($viewer, $ids_array);

			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('Remove history items successfully.'))
            ));

        }
    }

	public function removeallvideoAction() {
		$this->_helper->layout->setLayout('default-simple');
        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Check post
        if ($this -> getRequest() -> isPost()) {
            //Process delete
            $table = Engine_Api::_()->getDbTable('history','ynultimatevideo');
			$table->removeHistory($viewer, array());
			
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('Clear History successfully.'))
            ));
			
        }
    }

    public function removeallAction() {
        $this->_helper->layout->setLayout('default-simple');

        $type = $this->_getParam('type');

        $this->view->form = $form = new Ynultimatevideo_Form_Remove(
            array(
                'title' => 'Remove from History',
                'description' => 'Are you sure you want to remove all items from your History?'
            )
        );

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }

        if (Engine_Api::_()->ynultimatevideo()->removeAllItemsFromHistory($type)) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('All items have been cleared.');
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