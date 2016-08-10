<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */

require_once GOOGLE_LIBS_PATH. '/autoload.php';
require_once GOOGLE_LIBS_PATH. '/Client.php';
require_once GOOGLE_LIBS_PATH. '/Service/YouTube.php';

class Ynultimatevideo_IndexController extends Core_Controller_Action_Standard
{
	protected $_roles;

	public function init()
	{
		$this -> _roles = array(
				'owner',
				'parent_member',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
		);
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_video', null, 'view') -> isValid())
			return;
	}

	public function indexAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function createAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_video', null, 'create') -> isValid())
		{
			return;
		}

		// Render
		$this -> _helper -> content -> setEnabled();

		$values['user_id'] = $viewer -> getIdentity();
		$paginator = Engine_Api::_() -> getItemTable('ynultimatevideo_video') -> getVideosPaginator($values);
		$this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynultimatevideo_video', 'max');
		$this->view->current_count = $paginator->getTotalItemCount();

		//get first category
		$tableCategory = Engine_Api::_() -> getItemTable('ynultimatevideo_category');
		$firstCategory = $tableCategory -> getFirstCategory();
		$category_id = $this -> _getParam('category_id', $firstCategory -> category_id);
		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynultimatevideo_category', $category_id);

		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynultimatevideo_video');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}

		$parent_type = $this -> _getParam('parent_type');
		$parent_id = $this -> _getParam('parent_id', $this -> _getParam('subject_id'));

		if (Engine_Api::_() -> hasItemType($parent_type))
		{
			$this -> view -> item = $item = Engine_Api::_() -> getItem($parent_type, $parent_id);
			if (!$this -> _helper -> requireAuth() -> setAuthParams($item, null, 'video') -> isValid())
			{
				return;
			}
		}
		else
		{
			$parent_type = 'user';
			$parent_id = $viewer -> getIdentity();
		}

		// Create form
		$this -> view -> form = $form = new Ynultimatevideo_Form_Video( array(
				'title' => 'Add New Video',
				'formArgs' => $formArgs,
				'parent_type' => $parent_type,
				'parent_id' => $parent_id
		));

		$categoryElement = $form -> getElement('category_id');
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this -> view -> translate($item['title']));
		}
		//populate category
		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create video require at least one category. Please contact admin for more details.');
		}

		if ($this -> _getParam('type', false))
		{
			$form -> getElement('type') -> setValue($this -> _getParam('type'));
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		//return if not click submit or save draft
		$submit_button = $this -> _getParam('upload');
		$videoType = $this -> _getParam('type');

		$code = $this -> _getParam('code');
		if (!isset($submit_button) && ($videoType != 3 || empty($code)))
		{
			return;
		}

		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
		{
			return;
		}
		// Process
		$values = $form -> getValues();
		$values['parent_type'] = $parent_type;
		$values['parent_id'] = $parent_id;
		$values['owner_type'] = 'user';
		$values['owner_id'] = $viewer -> getIdentity();

		$insert_action = false;

		$db = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create video
			$table = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo');
			if ($values['type'] == '6')
			{
				$values['code'] = base64_decode($values['code']);
				$regex = "/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/";
				preg_match($regex, $values['code'], $matches);
				if(count($matches) > 2)
				{
					$values['code'] = $matches[3];
					$values['photo'] = 0;
				}
			}
			if ($values['type'] == Ynultimatevideo_Plugin_Factory::getUploadedType())
			{
				$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $this -> _getParam('id'));
				$video -> setFromArray($values);
				$video -> save();
			}
			else
			{
				$video = $table -> createRow();
				$video -> setFromArray($values);
				if ($values['type'] == Ynultimatevideo_Plugin_Factory::getVideoURLType() || $values['type'] == 6)
				{
					$video -> status = 1;
				}
				$video -> save();

				if ($values['type'] == Ynultimatevideo_Plugin_Factory::getVideoURLType())
				{
					$adapter = Ynultimatevideo_Plugin_Factory::getPlugin((int)$values['type']);
					$adapter -> getVideoImage($video -> getIdentity());
				}

				if ($values['type'] != Ynultimatevideo_Plugin_Factory::getVideoURLType() && $values['type'] != 6)
				{
					$adapter = Ynultimatevideo_Plugin_Factory::getPlugin((int)$values['type']);
					$adapter -> setParams(array('link' => $values['url']));
					if ($adapter -> fetchLink())
					{
						$video -> setPhoto($adapter -> getVideoLargeImage());
					}
					$video -> code = $adapter -> getVideoCode();
					$video -> duration = $adapter -> getVideoDuration();
					$video -> status = 1;
					$video -> save();
				}
			}

			// Set photo
			if (!empty($values['photo']))
			{
				$video -> setPhoto($form -> photo);
			}

			// Insert new action item
			$insert_action = true;

			if ($values['ignore'] == true)
			{
				$video -> status = 1;
				$video -> save();
				$insert_action = true;
			}
			//save custom field values of category
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($video);
			$customfieldform -> saveValues();

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			if ($parent_type == 'user' || empty($parent_type))
			{
				$roles = array(
						'owner',
						'owner_member',
						'owner_member_member',
						'owner_network',
						'registered',
						'everyone'
				);
			}
			else
			{
				$roles = array(
						'owner',
						'parent_member',
						'registered',
						'everyone'
				);
			}
			if (isset($values['auth_view']))
			{
				$auth_view = $values['auth_view'];
			}
			else
			{
				$auth_view = "everyone";
			}
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}

			if ($parent_type != 'user')
			{
				$roles = array(
						'owner',
						'parent_member',
						'registered',
						'everyone'
				);
			}
			else
			{
				$roles = array(
						'owner',
						'owner_member',
						'owner_member_member',
						'owner_network',
						'registered',
						'everyone'
				);
			}
			if (isset($values['auth_comment']))
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$video -> tags() -> addTagMaps($viewer, $tags);

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$db -> beginTransaction();
		try
		{
			if ($insert_action && $video -> status == 1)
			{
				//@TODO modify other module to add ynultimate video create notification
				$owner = $video -> getOwner();
				
				if ($parent_type == 'event')
				{
					$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($owner, $item, 'ynevent_video_create');
				}
				else
				{
					$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($owner, $video, 'ynultimatevideo_video_new');
				}
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $video);
				}
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		if ($video -> type == Ynultimatevideo_Plugin_Factory::getUploadedType())
		{
			//redirect to auth youtue
			if($video -> allow_upload_channel)
			{
				return $this -> _helper -> redirector -> gotoRoute(array('action' => 'oauth2callback', 'video_id' => $video -> getIdentity()), 'ynultimatevideo_general', true);
			}
			// redirect to manage page
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'ynultimatevideo_general', true);
		}
		else
			return $this -> _helper -> redirector -> gotoRoute(array(
					'user_id' => $viewer -> getIdentity(),
					'video_id' => $video -> getIdentity()
			), 'ynultimatevideo_view', true);
	}

	public function createPlaylistAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$this->_helper->content->setEnabled();

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> form = $form = new Ynultimatevideo_Form_Playlist_Create();

		// populate category
		$categories = Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo') -> getCategories(0);
		$categoryElement = $form -> getElement('category_id');
		$categoryElement -> addMultiOption(0, '');
		foreach ($categories as $item)
		{
			$categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
		}

		// Check method/data
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process saving the new playlist
		$values = $form -> getValues();
		$values['user_id'] = $viewer -> getIdentity();
		$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynultimatevideo');
		$db = $playlistTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$playlist = $playlistTable -> createRow();
			$playlist -> setFromArray($values);
			$playlist -> save();

			if (!empty($values['photo']))
			{
				try
				{
					$playlist -> setPhoto($form -> photo);
				}
				catch (Engine_Image_Adapter_Exception $e)
				{
					Zend_Registry::get('Zend_Log') -> log($e -> __toString(), Zend_Log::WARN);
				}
			}

			// Auth
			$auth = Engine_Api::_() -> authorization() -> context;

			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $this -> _roles);
			$commentMax = array_search($values['auth_comment'], $this -> _roles);

			foreach ($this->_roles as $i => $role)
			{
				$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollback();
			throw $e;
		}

		// add activity feed for creating a new playlist
		$db -> beginTransaction();
		try
		{
			$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $playlist, 'ynultimatevideo_playlist_new');
			if ($action != null)
			{
				Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $playlist);
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($playlist) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array(), 'ynultimatevideo_playlist', true);
	}

	public function viewAction()
	{
		$video_id = $this -> _getParam('video_id');
		$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
		if ($video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		// save thumbnail
		if($video -> type == 1 && $video -> duration == 0)
		{
			$adapter = Ynultimatevideo_Plugin_Factory::getPlugin($video -> type);
			$adapter -> setParams(array(
				'code' => $video -> code,
				'video_id' => $video -> getIdentity()
			));

			if($adapter -> getVideoLargeImage())
				$video -> setPhoto($adapter -> getVideoLargeImage());
			if($adapter -> getVideoDuration())
				$video -> duration = $adapter -> getVideoDuration();
			$video -> save();
		}

		$video = Engine_Api::_() -> core() -> getSubject('ynultimatevideo_video');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Photo Url
		$photoUrl = $video -> getPhotoUrl('thumb.profile');
		$pos = strpos($photoUrl, "http");
		if ($pos === false)
		{
			$photoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $photoUrl;
		}

		//Get Video Url
		$videoUrl = $video -> getHref();
		$pos = strpos($videoUrl, "http");
		if ($pos === false)
		{
			$videoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $videoUrl;
		}

		//Adding meta tags for sharing
		$view = Zend_Registry::get('Zend_View');
		$og = '<meta property="og:image" content="' . $photoUrl . '" />';
		$og .= '<meta property="og:title" content="' . $video -> getTitle() . '" />';
		$og .= '<meta property="og:url" content="' . $videoUrl . '" />';
		$og .= '<meta property="og:updated_time" content="' . $video->creation_date . '" />';
		$og .= '<meta property="og:type" content="video" />';
		$view -> layout() -> headIncludes .= $og;

		$viewerId = $viewer->getIdentity();
		$videoId = $video->getIdentity();
		if ($viewerId) {
			// update this video to history
			Engine_Api::_()->getDbTable('history', 'ynultimatevideo')->updateItem($viewer, $video);
		} else {
			// update video and category history to cookies
			$videoHistory = (empty($_COOKIE['ynultimatevideo_video_history'])) ? array() : json_decode($_COOKIE['ynultimatevideo_video_history']);
			if (!in_array($videoId, $videoHistory)) {
				$videoHistory[] = $videoId;
				setcookie('ynultimatevideo_video_history', json_encode($videoHistory), time() + (86400*365), '/');
			}
			// not add "All category" to history
			if ($video->category_id) {
				$categoryHistory = (empty($_COOKIE['ynultimatevideo_category_history'])) ? array() : json_decode($_COOKIE['ynultimatevideo_category_history']);
				if (!in_array($video->category_id, $categoryHistory)) {
					$categoryHistory[] = $video->category_id;
				}
				setcookie('ynultimatevideo_category_history', json_encode($categoryHistory), time() + (86400*365), '/');
			}
		}

		// if this is sending a message id, the user is being directed from a conversation
		// check if member is part of the conversation
		$message_id = $this -> getRequest() -> getParam('message');
		$message_view = false;
		if ($message_id)
		{
			$conversation = Engine_Api::_() -> getItem('messages_conversation', $message_id);
			if ($conversation -> hasRecipient(Engine_Api::_() -> user() -> getViewer()))
			{
				$message_view = true;
			}
		}
		$this -> view -> message_view = $message_view;

		if (!$message_view && !$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}

		$this -> view -> videoTags = $video -> tags() -> getTagMaps();

		// Check if edit/delete is allowed
		$this -> view -> can_edit = $can_edit = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'edit') -> checkRequire();
		$this -> view -> can_delete = $can_delete = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'delete') -> checkRequire();

		// check if embedding is allowed
		$can_embed = true;
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynultimatevideo_video.embeds', 1))
		{
			$can_embed = false;
		}
		else
			if (isset($video -> allow_embed) && !$video -> allow_embed)
			{
				$can_embed = false;
			}
		$this -> view -> can_embed = $can_embed;

		$embedded = "";
		// increment count
		if ($video -> status == 1)
		{
			if (!$video -> isOwner($viewer))
			{
				$video -> view_count++;
				$video -> save();
			}
			$embedded = $video -> getRichContent(true);
		}

		if ($video -> type == Ynultimatevideo_Plugin_Factory::getUploadedType() && $video -> status == 1)
		{
			$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
			if ($storage_file)
			{
				$this -> view -> video_location = $storage_file -> map();
			}

		}
		else
			if ($video -> type == Ynultimatevideo_Plugin_Factory::getVideoURLType())
			{
				$this -> view -> video_location = $video -> code;
			}

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> numberOfEmail = $settings -> getSetting('ynultimatevideo.friend.emails', 5);
		$this -> view -> viewer_id = $viewer -> getIdentity();
		$this -> view -> rating_count = Engine_Api::_() -> ynultimatevideo() -> ratingCount($video -> getIdentity());
		$this -> view -> video = $video;
		$this -> view -> rated = Engine_Api::_() -> ynultimatevideo() -> checkRated($video -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> videoEmbedded = $embedded;

		if ($video -> category_id)
		{
			$this -> view -> categories = $categories = Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo') -> getCategories(array(
				$video -> category_id,
			));
		}
		$view = $this -> view;
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

		// Render
		$this -> _helper -> content -> setEnabled();
	}

	public function validationAction()
	{
		$video_type = $this -> _getParam('type');
		$code = $this -> _getParam('code');
		$ajax = $this -> _getParam('ajax', false);
		$valid = false;
		$title = $description = "";
		if ($video_type == '6')
		{
			$valid = true;
		}
		else
		{
			$adapter = Ynultimatevideo_Plugin_Factory::getPlugin($video_type);
			$adapter -> setParams(array('code' => $code));
			$valid = ($adapter -> isValid())?true:false;
            if($adapter -> fetchLink())
            {
                $title = strip_tags($adapter -> getVideoTitle());
                $description = $adapter -> getVideoDescription();
				$description = str_replace("<br />", "\r\n", $description);
            }
		}
		echo Zend_Json::encode(array('valid' => $valid, 'title' => $title, 'description' => $description));
		exit;
	}

	public function listAction()
	{
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function manageAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		$this -> view -> can_create = $this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_video', null, 'create') -> checkRequire();

		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function addToAction()
	{
		if (0 !== ($video_id = (int)$this -> getRequest() -> getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id)) && $video instanceof Ynultimatevideo_Model_Video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject('ynultimatevideo_video') -> isValid())
		{
			return;
		}

		$this -> view -> video = $video;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			$this -> view -> loggedIn = false;
		}
		else
		{
			$this -> view -> loggedIn = true;
			$form = new Ynultimatevideo_Form_Playlist_QuickCreate();
			if (!$this -> getRequest() -> isPost())
			{
				// if the request is not the post method, set the video_id for the form and render the form
				$this -> view -> form = $form;
				$form -> getElement('video_id') -> setValue($video -> getIdentity());
			}
			else
			{
				if (!$form -> isValid($this -> getRequest() -> getPost()))
				{
					$data = array(
							'result' => 0,
							'message' => Zend_Registry::get('Zend_Translate') -> _('The inputed value is invalid.'),
					);
					return $this -> _helper -> json($data);
				}

				if (!$this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_playlist', null, 'create') -> checkRequire())
				{
					$data = array(
							'result' => 0,
							'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have the authorization to create new playlist.'),
					);
					return $this -> _helper -> json($data);
				}

				$values = $form -> getValues();
				$values['creation_date'] = date('Y-m-d H:i:s');
				$values['modified_date'] = date('Y-m-d H:i:s');
				$values['user_id'] = $viewer -> getIdentity();
				$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynultimatevideo');
				$db = $playlistTable -> getAdapter();
				$db -> beginTransaction();
				try
				{
					$playlist = $playlistTable -> createRow();
					$playlist -> setFromArray($values);
					$playlist -> video_count = 1;
					$playlist -> save();

					$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $playlist, 'ynultimatevideo_add_video_new_playlist');
					if ($action != null)
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $video);
					}

					// Rebuild privacy
					$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
					foreach ($actionTable->getActionsByObject($playlist) as $action)
					{
						$actionTable -> resetActivityBindings($action);
					}

					$auth = Engine_Api::_() -> authorization() -> context;
					if (empty($values['auth_view']))
					{
						$values['auth_view'] = 'everyone';
					}
					$viewMax = array_search($values['auth_view'], $this -> _roles);

					foreach ($this->_roles as $i => $role)
					{
						$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
						$auth -> setAllowed($playlist, $role, 'comment', true);
					}

					$playlistAssocTable = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
					$playlistAssoc = $playlistAssocTable -> createRow();
					$playlistAssoc -> playlist_id = $playlist -> getIdentity();
					$playlistAssoc -> video_id = $values['video_id'];
					$playlistAssoc -> creation_date = date('Y-m-d H:i:s');
					$playlistAssoc -> save();

					$db -> commit();

					$data = array(
							'result' => 1,
							'message' => $this -> view -> htmlLink($playlist -> getHref(), $playlist -> title),
					);
					return $this -> _helper -> json($data);
				}
				catch (Exception $e)
				{
					$db -> rollBack();
					throw $e;
				}
			}
		}

		$this -> view -> playlists = Engine_Api::_() -> ynultimatevideo() -> getPlaylists($viewer -> getIdentity());
		$this -> _helper -> layout -> disableLayout();
	}

	public function editAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		$this->_helper->content->setEnabled();

		if (0 !== ($video_id = (int)$this -> _getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id)) && $video instanceof Ynultimatevideo_Model_Video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject('ynultimatevideo_video') -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity() != $video -> owner_id && !$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'edit') -> isValid())
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}

		// Populate category list.
		$categories = Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo') -> getCategories();
		// remove "All categories" option
		unset($categories[0]);

		//get first category
		$tableCategory = Engine_Api::_() -> getItemTable('ynultimatevideo_category');
		$firstCategory = $tableCategory -> getFirstCategory();
		$category_id = $this -> _getParam('category_id', $video -> category_id);
		if (!$category_id) {
			$category_id = $firstCategory->category_id;
		}
		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynultimatevideo_category', $category_id);

		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynultimatevideo_video');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}

		$this -> view -> video = $video;
		$this -> view -> form = $form = new Ynultimatevideo_Form_Edit( array(
				'video' => $video,
				'title' => 'Edit Video',
				'parent_type' => $video -> parent_type,
				'parent_id' => $video -> parent_id,
				'formArgs' => $formArgs,
		));
		if($video -> type != Ynultimatevideo_Plugin_Factory::getUploadedType())
		{
			$form -> removeElement('allow_upload_channel');
		}

		$categoryElement = $form -> getElement('category_id');
		foreach ($categories as $item)
		{
			$categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
		}

		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create video require at least one category. Please contact admin for more details.');
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$submit_button = $this -> _getParam('upload');
		if (!isset($submit_button))
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
			return;
		}

		$favoriteTable = Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo');
		// Process
		$db = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$values = $form -> getValues();
			if($video -> category_id != $category_id)
			{
				$old_category_id = $video -> category_id;
				$isEditCategory = true;
			}
			$video -> setFromArray($values);
			$video -> save();
			// Set photo
			if (!empty($values['photo']))
			{
				$video -> setPhoto($form -> photo);
			}

			//save custom field values of category
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($video);
			$customfieldform -> saveValues();

			// remove old custom field
			if($isEditCategory)
			{
				$old_category = Engine_Api::_()->getItem('ynultimatevideo_category', $old_category_id);
				$tableMaps = Engine_Api::_() -> getDbTable('maps','ynultimatevideo');
				$tableValues = Engine_Api::_() -> getDbTable('values','ynultimatevideo');
				if($old_category)
				{
					$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_category->option_id));
					$arr_ids = array();
					if(count($fieldIds) > 0)
					{
						foreach($fieldIds as $id)
						{
							$arr_ids[] = $id -> child_id;
						}
						//delete in values table
						if(count($arr_ids) > 0)
						{
							$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $video->getIdentity()) -> where('field_id IN (?)', $arr_ids));

							foreach($valueItems as $item)
							{

								$item -> delete();
							}
						}
					}
				}
			}

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
					'owner',
					'parent_member',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
			);
			if ($values['auth_view'])
			{
				$auth_view = $values['auth_view'];
			}
			else
			{
				$auth_view = "everyone";
			}

			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}

			if ($values['auth_comment'])
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$video -> tags() -> setTagMaps($viewer, $tags);

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$db -> beginTransaction();
		try
		{
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			if (isset($favorites))
			{
				foreach ($favorites as $favorite)
				{
					foreach ($actionTable->getActionsByObject($favorite) as $action)
					{
						$actionTable -> resetActivityBindings($action);
					}
				}
			}
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		//redirect to auth youtue
		if($video -> type == Ynultimatevideo_Plugin_Factory::getUploadedType())
		{
			if($video -> allow_upload_channel)
			{
				if($video -> status = 1 && $video -> code == 'mp4')
					return $this -> _helper -> redirector -> gotoRoute(array('action' => 'oauth2callback', 'video_id' => $video -> getIdentity()), 'ynultimatevideo_general', true);
			}
			else
				if($video -> code != 'mp4' && $video -> status = 0)
				{
					Engine_Api::_() -> getDbtable('jobs', 'core') -> addJob('ynultimatevideo_encode', array('video_id' => $video -> getIdentity()));
				}
		}

		return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => $video -> getHref(),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}

	public function deleteAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $this -> getRequest() -> getParam('video_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynultimatevideo_Form_Delete();

		if (!$video)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Video doesn't exists or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $video -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			Engine_Api::_() -> getApi('core', 'ynultimatevideo') -> deleteVideo($video);
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Video has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynultimatevideo_general', true),
				'messages' => Array($this -> view -> message)
		));
	}

	public function rateAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$video_id = (int)$this -> _getParam('video_id');
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
			if ($video)
			{
				Engine_Api::_() -> core() -> setSubject($video);
			}
		}
		if (!$this -> _helper -> requireSubject('ynultimatevideo_video') -> isValid())
		{
			return;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();

		$rating = (int)$this -> _getParam('rating');

		$table = Engine_Api::_() -> getDbtable('ratings', 'ynultimatevideo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			Engine_Api::_() -> ynultimatevideo() -> setRating($video_id, $user_id, $rating);

			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
			$video -> rating = Engine_Api::_() -> ynultimatevideo() -> getRating($video -> getIdentity());
			$video -> save();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$total = Engine_Api::_() -> ynultimatevideo() -> ratingCount($video -> getIdentity());

		$data = array();
		$data[] = array(
				'total' => $total,
				'rating' => $rating,
		);

		return $this -> _helper -> json($data);
	}

	public function uploadAction()
	{
		if (isset($_GET['ul']) || isset($_FILES['Filedata']))
			return $this -> _forward('upload-video', null, null, array('format' => 'json'));

		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$this -> view -> form = $form = new Ynultimatevideo_Form_Video();
		$this -> view -> navigation = $this -> getNavigation();

		if (!$this -> getRequest() -> isPost())
		{
			if (null !== ($album_id = $this -> _getParam('album_id')))
			{
				$form -> populate(array('album' => $album_id));
			}
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$album = $form -> saveValues();
	}

	public function uploadVideoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		if (!$_FILES['fileToUpload'])
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		if (!isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload') . print_r($_FILES, true);
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		$illegal_extensions = array(
				'php',
				'pl',
				'cgi',
				'html',
				'htm',
				'txt'
		);
		if (in_array(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION), $illegal_extensions))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Type');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		$db = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$values['owner_id'] = $viewer -> getIdentity();

			$params = array(
					'owner_type' => 'user',
					'owner_id' => $viewer -> getIdentity(),
					'allow_upload_channel' => $_POST['allow']
			);

			$video = Engine_Api::_() -> ynultimatevideo() -> createVideo($params, $_FILES['fileToUpload'], $values);
			$status = true;
			$name = $_FILES['fileToUpload']['name'];
			$code = $video -> code;
			$video_id = $video -> video_id;

			// sets up title and owner_id now just incase members switch page as soon as upload is completed
			$video -> title = $_FILES['fileToUpload']['name'];
			$video -> owner_id = $viewer -> getIdentity();
			$video -> save();
			$db -> commit();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'name'=> $name, 'code' => $code, 'video_id' => $video_id)));
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.') . $e;
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}
	}

	public function composeUploadAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			$this -> _redirect('login');
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid method.');
			return;
		}

		$video_url = $this -> _getParam('uri', null);
		$video_type = $this -> _getParam('type', $_POST['type']);
		$composer_type = $this -> _getParam('c_type', 'wall');
		$valid = false;
		// extract code
		if ($video_type != Ynultimatevideo_Plugin_Factory::getUploadedType())
		{
			$adapter = Ynultimatevideo_Plugin_Factory::getPlugin((int)$video_type);
			$adapter -> setParams(array('link' => $video_url));
			$valid = $adapter -> isValid();
		}

		// check to make sure the user has not met their quota of # of allowed video uploads
		// set up data needed to check quota
		$values['user_id'] = $viewer -> getIdentity();
		$table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$paginator = $table->getVideosPaginator($values);
		$this -> view -> quota = $quota = Engine_Api::_() -> ynultimatevideo() -> getAllowedMaxValue('ynultimatevideo_video', $viewer -> level_id, 'max');
		$current_count = $paginator -> getTotalItemCount();
		if (($current_count >= $quota) && !empty($quota))
		{
			// return error message
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.');
		}
		else
			if ($valid)
			{
				$db = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> getAdapter();
				$db -> beginTransaction();

				try
				{
					$table = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo');
					$video = $table -> createRow();
					$video -> owner_id = $viewer -> getIdentity();
					$video -> type = $video_type;
					if($video_type == 6)
					{
						$regex = '/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/';
						preg_match($regex, $video_url, $matches);
						if(count($matches) > 2)
						{
							$video_url = $matches[3];
						}
					}
					$video -> code = $video_url;

					if ($video_type == Ynultimatevideo_Plugin_Factory::getVideoURLType() || $video_type == 6)
					{
						$video -> title = Ynultimatevideo_Plugin_Adapter_VideoURL::getDefaultTitle();
					}
					else
					{
						if ($adapter -> fetchLink())
						{
							// create video
							$video -> setPhoto($adapter -> getVideoLargeImage());
							$video -> title = $adapter -> getVideoTitle();
							$video -> description = $adapter -> getVideoDescription();
							$video -> duration = $adapter -> getVideoDuration();
							$video -> code = $adapter -> getVideoCode();
							$video -> save();
						}
					}

					// If video is from the composer, keep it hidden until the post is complete
					if ($composer_type)
					{
						$video -> search = 0;
					}
					$video -> status = 1;
					$video -> save();

					$db -> commit();
				}
				catch (Exception $e)
				{
					$db -> rollBack();
					throw $e;
				}

				// make the video public
				if ($composer_type === 'wall')
				{
					// CREATE AUTH STUFF HERE
					$auth = Engine_Api::_() -> authorization() -> context;
					$roles = array(
							'owner',
							'owner_member',
							'owner_member_member',
							'owner_network',
							'registered',
							'everyone'
					);
					foreach ($roles as $i => $role)
					{
						$auth -> setAllowed($video, $role, 'view', ($i <= $roles));
						$auth -> setAllowed($video, $role, 'comment', ($i <= $roles));
					}
				}

				$this -> view -> status = true;
				$this -> view -> video_id = $video -> video_id;
				$this -> view -> photo_id = $video -> photo_id;
				$this -> view -> title = $video -> title;
				$this -> view -> type = $video -> type;
				$this -> view -> description = $video -> description;
				if ($video_type == Ynultimatevideo_Plugin_Factory::getVideoURLType() || $video_type == 6)
				{
					$this -> view -> src = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynultimatevideo/externals/images/video.png';
				}
				else
				{
					$this -> view -> src = $video -> getPhotoUrl('thumb.normal');
				}
				$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Video posted successfully.');
			}
			else
			{
				if ($video_type == 3) {
					if (!$_FILES['fileToUpload']) {
						$error = Zend_Registry::get('Zend_Translate')->_('No file');
						$this->view->message = Zend_Registry::get('Zend_Translate')->_($error);
					}

					if (!isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
						$error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
						$this->view->message = Zend_Registry::get('Zend_Translate')->_($error);
					}

					$illegal_extensions = array(
						'php',
						'pl',
						'cgi',
						'html',
						'htm',
						'txt'
					);
					if (in_array(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
						$status = false;
						$error = Zend_Registry::get('Zend_Translate')->_('Invalid Type');
						$this->view->message = Zend_Registry::get('Zend_Translate')->_($error);
					}
					$uploadAdapter = Ynultimatevideo_Plugin_Factory::getPlugin((int)$video_type);
					$db = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> getAdapter();
					$db -> beginTransaction();

					try {
						$viewer = Engine_Api::_() -> user() -> getViewer();
						$values['owner_id'] = $viewer -> getIdentity();

						$params = array(
							'owner_type' => 'user',
							'owner_id' => $viewer -> getIdentity(),
						);
						$video = Engine_Api::_() -> ynultimatevideo() -> createVideo($params, $_FILES['fileToUpload'], $values);
						$status = true;

						$video -> type = $video_type;
						$video -> parent_type = 'user';
						$video -> parent_id = $viewer -> getIdentity();

						// sets up title and owner_id now just incase members switch page as soon as upload is completed
						$video -> title = $_FILES['fileToUpload']['name'];
						$video -> owner_id = $viewer -> getIdentity();
						$video -> save();

						$uploadAdapter->setParams(array('video_id' => $video->video_id));

						// If video is from the composer, keep it hidden until the post is complete
						if ($composer_type)
						{
							$video -> search = 0;
						}
						$video->photo_id = $uploadAdapter -> getVideoLargeImage();
						$video -> save();

						$db -> commit();


						$this -> view -> status = $status;
						$this -> view -> video_id = $video -> video_id;
						$this -> view -> photo_id = $uploadAdapter -> getVideoLargeImage();
						$this -> view -> title = $video -> title;
						$this -> view -> type = $video -> type;
						$this -> view -> description = $video -> description;
						$this -> view -> src = $video->getPhotoUrl();
						$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Video posted successfully.');
					} catch (Exception $e)
					{
						$db -> rollBack();
						throw $e;
					}
				} else {
					$this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
				}
			}
	}

	public function addToGroupAction()
	{
		$video = Engine_Api::_() -> core() -> getSubject();

	}

	public function oauth2callbackAction()
	{
		$video_id = $this -> _getParam('video_id', 0);
		if(!isset($_SESSION['ynultimatevideo_youtube_video']))
		{
			$_SESSION['ynultimatevideo_youtube_video'] = $video_id;
		}
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$token = '';
		if(isset($_SESSION['ynultimatevideo_youtube_token']))
			$token = $_SESSION['ynultimatevideo_youtube_token'];
		$OAUTH2_CLIENT_ID = $settings->getSetting('ynultimatevideo_youtube_clientid', "");
		$OAUTH2_CLIENT_SECRET = $settings->getSetting('ynultimatevideo_youtube_secret', "");

		$client = new Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$client->setAccessType('offline');
		$client->setScopes('https://www.googleapis.com/auth/youtube');
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"];
		}
		$redirect = $pageURL.$this -> view -> url(array('action' => 'oauth2callback'), 'ynultimatevideo_general', true);
		$client->setRedirectUri($redirect);

		if($token)
		{
			$client->setAccessToken($token);
			if($client->isAccessTokenExpired())
			{
				unset($_SESSION['ynultimatevideo_youtube_token']);
				$state = mt_rand();
				$client->setState($state);
				$_SESSION['state'] = $state;
				$authUrl = $client->createAuthUrl();
				$this -> _redirectCustom($authUrl);
			}
		}

		if (isset($_GET['code']))
		{
			$client->authenticate($_GET['code']);
			$token = $client->getAccessToken();
		}

		if(!$token)
		{
			$state = mt_rand();
			$client->setState($state);
			$_SESSION['state'] = $state;
			$authUrl = $client->createAuthUrl();
			$this -> _redirectCustom($authUrl);
		}
		else
		{
			$_SESSION['ynultimatevideo_youtube_token'] = $token;
			if(isset($_SESSION['ynultimatevideo_youtube_video']) && !$video_id)
			{
				$video_id = $_SESSION['ynultimatevideo_youtube_video'];
			}
			unset($_SESSION['ynultimatevideo_youtube_video']);
			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $video_id);
			if($video)
			{
				$video -> user_token = $token;
				$video -> save();

				// Add to jobs
				Engine_Api::_() -> getDbtable('jobs', 'core') -> addJob('ynultimatevideo_uploadyoutube', array('item' => $video -> getGuid()));
			}

			// redirect to manage page
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'ynultimatevideo_general', true);
		}
	}
}
