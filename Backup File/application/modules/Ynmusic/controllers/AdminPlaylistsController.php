<?php
class Ynmusic_AdminPlaylistsController extends Core_Controller_Action_Admin {
	protected $_paginate_params = array();
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_main_playlists');
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmusic.playlistsPerPage', 10);
		$this -> _paginate_params['sort'] = $this -> getRequest() -> getParam('sort', 'recent');
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
	}

	public function indexAction() {
		$params = array_merge($this -> _paginate_params, array('user' => $this -> view -> viewer_id, 'admin' => "admin"));
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Playlists_Search();
		$values = array();
		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
		}
		if (!empty($values['title'])) {
			$params['title'] = $values['title'];
		}
		if (!empty($values['owner'])) {
			$params['owner'] = $values['owner'];
		}
		if (!empty($values['genre'])) {
			$params['genre'] = $values['genre'];
		}		
		$params['page'] = $this -> _getParam('page', 1);
		$this -> view -> formValues = $values;
		$this -> view -> paginator = Engine_Api::_()->getDbTable('playlists', 'ynmusic')->getPaginator($params);
		$this -> view -> params = $params;
	}
	
	public function multideleteAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id) {
                $playlist = Engine_Api::_()->getItem('ynmusic_playlist', $id);
                if ($playlist && $playlist->isDeletable()) {
                	Engine_Api::_()->getDbTable('genremappings', 'ynmusic') -> deleteGenresByItem($playlist);
                    $playlist->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmusic','controller'=>'playlists', 'action'=>'index'), 'admin_default', TRUE);
        }
    }
}
