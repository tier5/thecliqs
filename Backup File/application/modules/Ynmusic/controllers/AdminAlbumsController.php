<?php
class Ynmusic_AdminAlbumsController extends Core_Controller_Action_Admin
{		
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_main_albums');
	}
	
	 public function featureAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $album = Engine_Api::_()->getItem('ynmusic_album', $id);
        if ($album) {
            $album->featured = $value;
            $album->save();
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
                $album = Engine_Api::_()->getItem('ynmusic_album', $id);
                if ($album) {
                    $album->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmusic','controller'=>'albums', 'action'=>'index'), 'admin_default' , TRUE);
        }
    }
	
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Album_Search();
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('albums', 'ynmusic');
		$params = $this ->_getAllParams();
		$form -> populate($params);
		$this->view->formValues = $params;
        $this->view->paginator = $table -> getAlbumsPaginator($params);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}
}
