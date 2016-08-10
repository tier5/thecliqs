<?php
class Ynmusic_AdminSongsController extends Core_Controller_Action_Admin
{		
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_main_songs');
	}
	
	 public function featureAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $song = Engine_Api::_()->getItem('ynmusic_song', $id);
        if ($song) {
            $song->is_featured = $value;
            $song->save();
        }
    }
	
	public function multideleteAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE)
        {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id)
            {
                $song = Engine_Api::_()->getItem('ynmusic_song', $id);
                if ($song) {
                    $song->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmusic','controller'=>'songs', 'action'=>'index'), 'admin_default' , TRUE);
        }
    }
	
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Song_Search();
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('songs', 'ynmusic');
		$params = $this ->_getAllParams();
		$form -> populate($params);
		$this->view->formValues = $params;
        $this->view->paginator = $table -> getSongsPaginator($params);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}
}
