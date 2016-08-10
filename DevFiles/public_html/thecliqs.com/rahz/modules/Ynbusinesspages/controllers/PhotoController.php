<?php
class Ynbusinesspages_PhotoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('ynbusinesspages_photo', $photo_id)))
			{
				Engine_Api::_() -> core() -> setSubject($photo);
			}

			elseif (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
			{
				Engine_Api::_() -> core() -> setSubject($business);
			}
		}
		
		$this -> _helper -> requireUser -> addActionRequires(array(
			'upload',
			'upload-photo', // Not sure if this is the right
			'edit',
		));

		$this -> _helper -> requireSubject -> setActionRequireTypes(array(
			'list' => 'ynbusinesspages_business',
			'upload' => 'ynbusinesspages_business',
			'view' => 'ynbusinesspages_photo',
			'edit' => 'ynbusinesspages_photo',
		));
	}

	public function listAction()
	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_album'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		
		$this -> view -> album = $album = $business -> getSingletonAlbum();

		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		$this -> view -> canUpload = $business -> isAllowed('album_create');
	}

	public function viewAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $photo -> getCollection();
		$this -> view -> business = $business = $photo -> getBusiness();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_album'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> canEdit = $business -> isAllowed('album_edit');

		if (!$viewer || !$viewer -> getIdentity() || $photo -> user_id != $viewer -> getIdentity())
		{
			$photo -> view_count = new Zend_Db_Expr('view_count + 1');
			$photo -> save();
		}
	}

	public function uploadAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynbusinesspages_album'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$album = $business -> getSingletonAlbum();
		
		if (!$business -> isAllowed('album_create'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ynbusinesspages_Form_Photo_Upload();
		$session = new Zend_Session_Namespace('mobile');
		if (!$session -> mobile)
		{
			$form -> business_id -> setValue($business -> getIdentity());
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$params = array(
				'business_id' => $business -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
				'collection_id' => $album -> getIdentity(),
				'album_id' => $album -> getIdentity()
			);
			// mobile upload photos
			$arr_photo_id = array();
			if ($session -> mobile && !empty($_FILES['photos']))
			{
				$files = $_FILES['photos'];
				if(!$files['name'][0])
				{
					$form -> addError($this -> view -> translate("Please choose a photo to upload!"));
					return;
				}
				foreach ($files['name'] as $key => $value)
				{
					$type = explode('/', $files['type'][$key]);
					if ($type[0] != 'image' || !is_uploaded_file($files['tmp_name'][$key]))
					{
						continue;
					}
					try
					{
						$temp_file = array(
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'name' => $files['name'][$key]
						);
						$photoTable = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
						$photo = $photoTable -> createRow();
						$photo -> setFromArray($params);
						$photo -> save();

						$photo -> setPhoto($temp_file);

						$arr_photo_id[] = $photo -> getIdentity();
					}

					catch ( Exception $e )
					{
						throw $e;
						return;
					}
				}
			}
			else
			{
				$values = $form -> getValues();
				$arr_photo_id = explode(' ', trim($values['html5uploadfileids']));
			}
			$values = $form -> getValues();

			if ($arr_photo_id)
			{
				$values['file'] = $arr_photo_id;
			}
			// Add action and attachments
			$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $business, 'ynbusinesspages_photo_upload', null, array('count' => count($values['file'])));
			
			// send notification to followers
			$business -> sendNotificationToFollowers($this -> view -> translate(array('%s photo', '%s photos', count($values['file'])), count($values['file'])));
			
			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id)
			{
				$photo = Engine_Api::_() -> getItem("ynbusinesspages_photo", $photo_id);
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo -> save();

				if ($action instanceof Activity_Model_Action && $count < 8)
				{
					$api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}

			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if($this -> _getParam('tab', null) && !$session -> mobile)
		{
			return $this->_helper->redirector->gotoRoute(array('id' => $business -> getIdentity(), 'tab' => $this -> _getParam('tab', null)), 'ynbusinesspages_profile', true);
		}
		else
			$this -> _redirectCustom($business);
	}

	public function uploadPhotoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $_POST['business_id']);

		if (!$business -> isAllowed('album_create'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// @todo check auth
		//$business

		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $error
					)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$album = $business -> getSingletonAlbum();

			$params = array(
				// We can set them now since only one album is allowed
				'collection_id' => $album -> getIdentity(),
				'album_id' => $album -> getIdentity(),

				'business_id' => $business -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
			);

			$photoTable = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
			$photo = $photoTable -> createRow();
			$photo -> setFromArray($params);
			$photo -> save();

			$temp_file = array(
				'type' => $_FILES['files']['type'][0],
				'tmp_name' => $_FILES['files']['tmp_name'][0],
				'name' => $_FILES['files']['name'][0]
			);

			$photo -> setPhoto($temp_file);
			$db -> commit();

			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo -> photo_id;
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $name,
						'photo_id' => $photo_id
					)))));

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}
	}

	public function editAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();
		$business = $photo -> getParent('ynbusinesspages_business');
		if (!$business -> isAllowed('album_edit'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ynbusinesspages_Form_Photo_Edit();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> setFromArray($form -> getValues()) -> save();
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Changes saved')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
			'closeSmoothbox' => true,
		));
	}

	public function deleteAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();
		$business = $photo -> getParent('ynbusinesspages_business');

		if (!$business -> isAllowed('album_delete', null, $photo))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$this -> view -> form = $form = new Ynbusinesspages_Form_Photo_Delete();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Photo deleted')),
			'layout' => 'default-simple',
			'parentRedirect' => $business -> getHref(),
			'closeSmoothbox' => true,
		));
	}

	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynbusinesspages_photo', $this -> getRequest() -> getParam('photo_id'));
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		$business = $photo -> getParent('ynbusinesspages_business');
		if (!$business -> isAllowed('album_delete', null, $photo))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
}
