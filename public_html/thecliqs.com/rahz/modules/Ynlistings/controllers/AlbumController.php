<?php
class Ynlistings_AlbumController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($album_id = (int)$this -> _getParam('album_id')) && null !== ($album = Engine_Api::_() -> getItem('ynlistings_album', $album_id)))
			{
				Engine_Api::_() -> core() -> setSubject($album);
			}
			else
			if (0 !== ($listing_id = (int)$this -> _getParam('listing_id')) && null !== ($listing = Engine_Api::_() -> getItem('ynlistings_listing', $listing_id)))
			{
				Engine_Api::_() -> core() -> setSubject($listing);
			}
		}
	}

	public function createAction()
	{
		//Check viewer and subject requirement
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		if (!Engine_Api::_() -> core() -> hasSubject('ynlistings_listing'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$this-> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> canUpload = $canUpload = $listing->canUploadPhotos();
        if (!$canUpload) {
            return $this -> _helper -> requireAuth -> forward();
        }
		$this -> view -> form = $form = new Ynlistings_Form_Album_Create();

		//Return if no post action
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		//Return if invalid input found
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$table = Engine_Api::_() -> getItemTable('ynlistings_album');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$album = $table -> createRow();
			$album -> listing_id = $listing -> listing_id;
			$album -> user_id = $viewer -> user_id;
			$album -> title = $values['title'];
			$album -> description = $values['description'];

			$album -> save();
			$db -> commit();
			$this -> _helper -> redirector -> gotoRoute(array(
				'controller' => 'photo',
				'action' => 'upload',
				'listing_id' => $listing -> getIdentity(),
				'album_id' => $album -> album_id
			), 'ynlistings_extended', true);
		}
		catch(Exception $e)
		{
			$db -> rollBack();
			throw ($e);
		}
	}

	public function viewAction()
	{
		$this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		$params = $this -> _getAllParams();

		$this -> view -> listing = $listing = Engine_Api::_() -> getItem('ynlistings_listing', $params['listing_id']);
		$this -> view -> album = $album = Engine_Api::_() -> getItem('ynlistings_album', $params['album_id']);

		if ($album -> user_id != 0)
			$album_owner_id = $album -> user_id;
		else
			$album_owner_id = $listing -> user_id;

		if ($viewer -> getIdentity() == 0)
		{
			$canEdit = false;
		}
		else
		{
			if ($viewer -> isAdmin() || $viewer -> getIdentity() == $album_owner_id || $listing -> isOwner($viewer))
			{
				$canEdit = true;
			}
			else
			{
				$canEdit = false;
			}
		}
		$this -> view -> canEdit = $canEdit;
		
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 24));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
	}

	public function editAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$values = $this -> _getAllParams();
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $values['listing_id']);
		$album = Engine_Api::_() -> getItem('ynlistings_album', $values['album_id']);
		$this -> view -> form = $form = new Ynlistings_Form_Album_Edit();

		if ($album -> user_id != 0)
			$album_owner_id = $album -> user_id;
		else
			$album_owner_id = $listing -> user_id;

		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		else
		{
			if (!$viewer -> isAdmin() && $viewer -> getIdentity() != $album_owner_id && !$listing -> isOwner($viewer))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate(array(
				'album_id' => $album -> album_id,
				'title' => $album -> title,
				'description' => $album -> description,
			));
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('albums', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> setFromArray($form -> getValues()) -> save();
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

	public function listAction()
	{

		//Get Viewer, Listing and Search Form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> listing = $listing = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynlistings_Form_Album_Search;

		if ($viewer -> getIdentity() == 0)
			$form -> removeElement('view');

		//Get search condition
		$params = array();
		$params['listing_id'] = $listing -> getIdentity();
		$params['user_id'] = null;
		$params['search'] = $this -> _getParam('search', '');
		$params['view'] = $this -> _getParam('view', 0);
		$params['order'] = $this -> _getParam('order', 'recent');
		if ($params['view'] == 1)
		{
			$params['user_id'] = $viewer -> getIdentity();
		}
		//Populate Search Form
		$form -> populate(array(
			'search' => $params['search'],
			'view' => $params['view'],
			'order' => $params['order'],
			'page' => $this -> _getParam('page', 1)
		));
		$this -> view -> formValues = $form -> getValues();

		//Get Album paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynlistings_album') -> getAlbumsPaginator($params);
	
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 20));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		
		$this -> view -> canUpload = $listing->canUploadPhotos();
	}

	public function deleteAction()
	{
		$params = $this -> _getAllParams();
		$listing = Engine_Api::_() -> getItem('ynlistings_listing', $params['listing_id']);
		$album = Engine_Api::_() -> getItem('ynlistings_album', $params['album_id']);
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($album -> user_id != 0)
		{
			$album_owner_id = $album -> user_id;
		}
		else
		{
			$album_owner_id = $listing -> user_id;
		}

		$this -> view -> form = $form = new Ynlistings_Form_Album_Delete();

		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		else
		{
			if (!$viewer -> isAdmin() && $viewer -> getIdentity() != $album_owner_id && !$listing -> isOwner($viewer))
			{
				return $this -> _helper -> requireAuth -> forward();
			}
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$db = Engine_Api::_() -> getDbtable('albums', 'ynlistings') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$album -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Album deleted')),
			'layout' => 'default-simple',
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'controller' => 'album',
				'action' => 'list',
				'subject' => $listing -> getGuid(),
				'album_id' => $album -> getIdentity()
			), 'ynlistings_extended', true),
			'closeSmoothbox' => true,
		));
	}
}
?>
