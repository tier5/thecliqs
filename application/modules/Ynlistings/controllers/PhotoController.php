<?php
class Ynlistings_PhotoController extends Core_Controller_Action_Standard {
	public function init() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('ynlistings_photo', $photo_id))) {
				Engine_Api::_() -> core() -> setSubject($photo);
			} else if (0 !== ($listing_id = (int)$this -> _getParam('listing_id')) && null !== ($listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id))) {
				Engine_Api::_() -> core() -> setSubject($listing);
			}
		}
	}

	public function indexAction() {
		//Check viewer and subject requirement
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $this -> _getParam('listing_id'));
		if (!$listing)
			return $this -> _helper -> requireSubject -> forward();
		if (!Engine_Api::_() -> core() -> hasSubject('ynlistings_listing')) {
			Engine_Api::_() -> core() -> setSubject($listing);
		}
		if(!$listing -> isOwner($viewer))
		{
			return $this -> _helper -> requireSubject -> forward();		
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid()) {
			return;
		}
		// Prepare form
		$this -> view -> form = $form = new Ynlistings_Form_Photo_Manage();
		$this->view->can_select_theme = $can_select_theme = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'select_theme') -> checkRequire();
		$this -> view -> album = $album = $listing -> getSingletonAlbum();
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage(100);

		foreach ($paginator as $photo) {
			$subform = new Ynlistings_Form_Photo_Edit( array('elementsBelongTo' => $photo -> getGuid()));
			if ($photo -> file_id == $listing -> photo_id)
				$subform -> removeElement('delete');
			$subform -> populate($photo -> toArray());
			$form -> addSubForm($subform, $photo -> getGuid());
		}
		$this -> view -> listing = $listing;

		// Check post/form
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;
		$cover = $this -> _getParam('cover');
		// Process
		foreach ($paginator as $photo) {
			$subform = $form -> getSubForm($photo -> getGuid());
			$subValues = $subform -> getValues();
			$subValues = $subValues[$photo -> getGuid()];
			unset($subValues['photo_id']);
			if (isset($cover) && $cover == $photo -> photo_id) {
				$listing -> photo_id = $photo -> file_id;
				$listing -> save();
			}

			if (isset($subValues['delete']) && $subValues['delete'] == '1') {
				if ($listing -> photo_id == $photo -> file_id) {
					$listing -> photo_id = 0;
					$listing -> save();
				}
				$photo -> delete();
			} else {
				$photo -> setFromArray($subValues);
				$photo -> save();
			}
		}
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'ynlistings_general', true);
	}

	public function viewAction() {
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $photo -> getCollection();
		$this -> view -> listing = $listing = $photo -> getListing();
		if($album -> getOwner() -> getIdentity() == $viewer -> getIdentity() || ($listing && $listing -> isOwner($viewer))) {
			$canEdit = 1;
		}
		else {
			$canEdit = 0;
		}
		$this -> view -> canEdit = $canEdit;
	}

	public function uploadAction() {
		if (!Engine_Api::_() -> core() -> hasSubject())
			return $this -> _helper -> requireAuth -> forward();
		$listing = Engine_Api::_() -> core() -> getSubject();
		if (isset($_GET['ul']) || isset($_FILES['Filedata']))
			return $this -> _forward('upload-photo', null, null, array('format' => 'json', 'listing_id' => (int)$listing -> getIdentity()));

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> listing = $listing = Engine_Api::_() -> getItem('ynlistings_listing', (int)$listing -> getIdentity());
		if (!$listing)
			return $this -> _helper -> requireAuth -> forward();

		$album_id = $this -> _getParam('album_id');
		if (!empty($album_id)) {
			$album = Engine_Api::_() -> getItem('ynlistings_album', $album_id);
			$this -> view -> canUpload = $listing -> canUploadPhotos();
		} else {
			if ($listing -> isOwner($viewer)) {
				$this -> view -> canUpload = true;
			}
			$album = $listing -> getSingletonAlbum();
		}
		$this -> view -> listing_id = $listing -> listing_id;
		$this -> view -> form = $form = new Ynlistings_Form_Photo_Upload();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynlistings_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$values = $form -> getValues();
			$params = array('listing_id' => $listing -> getIdentity(), 'user_id' => $viewer -> getIdentity(), );

			if (isset($values['html5uploadfileids'])) {
				$values['file'] = explode(' ', trim($values['html5uploadfileids']));
			}
			
			// Add action and attachments
			$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $listing, 'ynlistings_photo_upload', null, array('count' => count($values['file'])));
			
			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id) {
				$photo = Engine_Api::_() -> getItem("ynlistings_photo", $photo_id);

				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo -> save();

				if ($listing -> photo_id == 0) {
					$listing -> photo_id = $photo -> file_id;
					$listing -> save();
				}

				if ($action instanceof Activity_Model_Action && $count < 8) {
					$api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		$profile_photo = $this ->_getParam('profile');
		if(!empty($profile_photo))
		{
			return $this -> _helper -> redirector -> gotoRoute(array('controller' => 'photo', 'action' => 'index', 'listing_id' => $listing -> getIdentity()), 'ynlistings_extended', true);
		}
		else 
		{
			return $this -> _helper -> redirector -> gotoRoute(array('controller' => 'album', 'action' => 'list', 'subject' => $listing -> getGuid()), 'ynlistings_extended', true);
		}
	}

	public function uploadPhotoAction() {
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}

		if (!$this -> getRequest() -> isPost()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $this -> _getParam('listing_id'));
		if (!$listing -> canUploadPhotos()) {
			return $this -> _helper -> requireAuth -> forward();
		}
		// @todo check auth
		//$listing

		if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $error)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();

		try {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if (!$album_id = $this -> _getParam('album_id')) {
				$album = $listing -> getSingletonAlbum();
			} else
				$album = Engine_Api::_() -> getItem('ynlistings_album', $album_id);

			$params = array(
			// We can set them now since only one album is allowed
			'listing_id' => $listing ->getIdentity(),
			'user_id' => $viewer -> getIdentity(), );

			$photoTable = Engine_Api::_() -> getItemTable('ynlistings_photo');
			$photo = $photoTable -> createRow();
			$photo -> setFromArray($params);
			$photo -> save();

			$temp_file = array('type' => $_FILES['files']['type'][0], 'tmp_name' => $_FILES['files']['tmp_name'][0], 'name' => $_FILES['files']['name'][0]);

			$photo -> setPhoto($temp_file);
			$db -> commit();

			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo -> photo_id;
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $name, 'photo_id' => $photo_id)))));
		} catch( Exception $e ) {
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}
	}

	public function editAction() {
		$photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynlistings_Form_Photo_EditDetail();
		
		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> setFromArray($form -> getValues()) -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array('Changes saved'), 'layout' => 'default-simple', 'parentRefresh' => true, 'closeSmoothbox' => true, ));
	}

	public function deleteAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Ynlistings_Form_Photo_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$photo_id = (int)$this -> _getParam('photo_id');
		$photo = Engine_Api::_() -> getItem('ynlistings_photo', $photo_id);
		$link_redirect = $photo -> getNextCollectible() -> getHref();
		$db = $photo -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> delete();
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Photo deleted')), 'layout' => 'default-simple', 'parentRedirect' => $link_redirect, 'closeSmoothbox' => true, ));
	}

}
