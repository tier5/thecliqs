<?php
class Mp3music_AdminCategoryController extends Core_Controller_Action_Admin
{
	protected $_paginate_params = array();
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_category');
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
	}

	public function indexAction()
	{
		$params = array_merge($this -> _paginate_params, array('parent_cat' => 0));
		$temp = new Mp3music_Api_Core();
		$this -> view -> catPaginator = $temp -> getCatPaginator($params);
		$this -> view -> params = $params;
	}

	public function removeCatAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('isGet');
			exit ;
		}
		$category = Engine_Api::_() -> getItem('mp3music_cat', $this -> getRequest() -> getParam('cat_id'));
		if (!$category)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid category');
			$this -> view -> post = $_POST;
			return;
		}
		$db = Engine_Api::_() -> getDbTable('cats', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$category -> delete();
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function renameCatAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Invalid request method');
			exit ;
		}
		$category = Engine_Api::_() -> getItem('mp3music_cat', $this -> getRequest() -> getParam('cat_id'));
		if (!$category)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid song');
			return;
		}
		$db = Engine_Api::_() -> getDbTable('cats', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$category -> setTitle(trim($this -> getRequest() -> getParam('title')));
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function addCatAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Invalid request method');
			exit ;
		}
		try
		{
			$category = Engine_Api::_() -> getDbtable('cats', 'mp3music') -> createRow();
			$category -> title = trim($this -> getRequest() -> getParam('title'));
			$category -> parent_cat = $this -> getRequest() -> getParam('parent_cat', 0);
			$category -> save();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function addSingerTypeAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Invalid request method');
			exit ;
		}
		try
		{
			$singerType = Engine_Api::_() -> getDbtable('singerTypes', 'mp3music') -> createRow();
			$singerType -> title = trim($this -> getRequest() -> getParam('title'));
			$singerType -> save();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function removeSingerTypeAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('isGet');
			exit ;
		}
		$singerType = Engine_Api::_() -> getItem('mp3music_singer_type', $this -> getRequest() -> getParam('singertype_id'));
		if (!$singerType)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid singertype');
			$this -> view -> post = $_POST;
			return;
		}
		$db = Engine_Api::_() -> getDbTable('singerTypes', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			foreach ($singerType->getSingers() as $singer)
				$singer -> delete();
			$singerType -> delete();
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function renameSingerTypeAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Invalid request method');
			exit ;
		}
		$singerType = Engine_Api::_() -> getItem('mp3music_singer_type', $this -> getRequest() -> getParam('singertype_id'));
		if (!$singerType)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid singertype');
			return;
		}
		$db = Engine_Api::_() -> getDbTable('singerTypes', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$singerType -> setTitle(trim($this -> getRequest() -> getParam('title')));
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function removeSingerAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('isGet');
			exit ;
		}
		$singer = Engine_Api::_() -> getItem('mp3music_singer', $this -> getRequest() -> getParam('singer_id'));
		if (!$singer)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid singer');
			$this -> view -> post = $_POST;
			return;
		}
		$db = Engine_Api::_() -> getDbTable('singers', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$singer -> delete();
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function editSingerAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$singer_id = $this -> getRequest() -> getParam('singer_id');
		$singer = $this -> view -> singer = Engine_Api::_() -> getItem('mp3music_singer', $singer_id);
		if (empty($singer) && $singer_id > 0)
		{
			$this -> _helper -> redirector -> gotoUrl(array(), 'mp3_music_admin_music_setting', true);
			return;
		}
		$this -> view -> form = new Mp3music_Form_EditSinger();
		$this -> view -> form -> populate($singer);
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('singers', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$this -> view -> form -> saveValues();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array('Your changes have been saved.')
				));
			}
			catch (Exception $e)
			{
				$db -> rollback();
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	public function createSingerAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$this -> view -> form = new Mp3music_Form_CreateSinger();
		$this -> view -> singertype_id = $this -> getRequest() -> getParam('singertype_id');
		$this -> view -> singer_id = $this -> _getParam('singer_id', '0');
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('singers', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$singer = $this -> view -> form -> saveValues();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array('Add singer successfully.')
				));
			}
			catch (Exception $e)
			{
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	public function removeArtistAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('isGet');
			exit ;
		}
		$artist = Engine_Api::_() -> getItem('mp3music_artist', $this -> getRequest() -> getParam('artist_id'));
		if (!$artist)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid artist');
			$this -> view -> post = $_POST;
			return;
		}
		$db = Engine_Api::_() -> getDbTable('artists', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$artist -> delete();
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function editArtistAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$artist_id = $this -> getRequest() -> getParam('artist_id');
		$artist = $this -> view -> singer = Engine_Api::_() -> getItem('mp3music_artist', $artist_id);
		if (empty($artist) && $artist_id > 0)
		{
			$this -> _helper -> redirector -> gotoUrl(array(), 'mp3music_admin_setting', true);
			return;
		}
		$this -> view -> form = new Mp3music_Form_EditArtist();
		$this -> view -> form -> populate($artist);
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('artists', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$this -> view -> form -> saveValues();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array('Your changes have been saved.')
				));
			}
			catch (Exception $e)
			{
				$db -> rollback();
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

	public function createArtistAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$this -> view -> form = new Mp3music_Form_CreateArtist();
		$this -> view -> artist_id = $this -> _getParam('artist_id', '0');
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('artists', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$artist = $this -> view -> form -> saveValues();
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array('Add artist successfully.')
				));
			}
			catch (Exception $e)
			{
				$this -> view -> success = false;
				throw $e;
			}
		}
	}

}
