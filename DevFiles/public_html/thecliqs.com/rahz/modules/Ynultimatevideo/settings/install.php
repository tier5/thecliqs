<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Installer extends Engine_Package_Installer_Module {

	public function onInstall() {
		$this -> _checkFfmpegPath();
		$this -> _addUserProfileContent();
		$this -> _addVideoIndexPage();
		$this -> _addVideoCreatePage();
		$this -> _addVideoEditPage();
		$this -> _addPlaylistCreatePage();
		$this -> _addPlaylistEditPage();
		$this -> _addMyVideosPage();
		$this -> _addManagePlaylistPage();
		$this -> _addVideoListingPage();
		$this -> _addVideoFavoritePage();
		$this -> _addVideoPlaylistsPage();
		$this -> _addVideoPlaylistDetailsPage();
		$this -> _addVideoWatchlaterPage();
		$this -> _addVideoDetailPage();
		$this -> _addHistoryPage();

		parent::onInstall();
	}

	protected function _checkFfmpegPath() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check ffmpeg path for correctness
		if (function_exists('exec') && function_exists('shell_exec')) {
			// Api is not available
			//$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
			$ffmpeg_path = $db -> select() -> from('engine4_core_settings', 'value') -> where('name = ?', 'ynultimatevideo.ffmpeg.path') -> limit(1) -> query() -> fetchColumn(0);

			$output = null;
			$return = null;
			if (!empty($ffmpeg_path)) {
				exec($ffmpeg_path . ' -version', $output, $return);
			}
			// Try to auto-guess ffmpeg path if it is not set correctly
			$ffmpeg_path_original = $ffmpeg_path;
			if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
				$ffmpeg_path = null;
				// Windows
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				}
				// Not windows
				else {
					$output = null;
					$return = null;
					@exec('which ffmpeg', $output, $return);
					if (0 == $return) {
						$ffmpeg_path = array_shift($output);
						$output = null;
						$return = null;
						exec($ffmpeg_path . ' -version', $output, $return);
						if (0 == $return) {
							$ffmpeg_path = null;
						}
					}
				}
			}
			if ($ffmpeg_path != $ffmpeg_path_original) {
				$count = $db -> update('engine4_core_settings', array('value' => $ffmpeg_path, ), array('name = ?' => 'ynultimatevideo.ffmpeg.path', ));
				if ($count === 0) {
					try {
						$db -> insert('engine4_core_settings', array('value' => $ffmpeg_path,
								'name' => 'ynultimatevideo.ffmpeg.path',
						));
					} catch (Exception $e) {

					}
				}
			}
		}
	}

	protected function _insertWidgetToProfileContent($page_id, $name, $params, $order) {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
		$container_id = $select -> query() -> fetchObject() -> content_id;

		// middle_id (will always be there)
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
		$middle_id = $select -> query() -> fetchObject() -> content_id;

		// tab_id (tab container) may not always be there
		$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
		$tab_id = $select -> query() -> fetchObject();
		if ($tab_id && @$tab_id -> content_id) {
			$tab_id = $tab_id -> content_id;
		} else {
			$tab_id = null;
		}

		// tab on profile
		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'widget',
				'name' => $name,
				'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
				'order' => $order,
				'params' => $params,
		));
	}

	protected function _addUserProfileContent() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// profile page
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		// video.profile-videos
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.profile-videos');
		$infoProfileVideos = $select -> query() -> fetch();

		if (empty($infoProfileVideos)) {
			$this -> _insertWidgetToProfileContent($page_id, 'ynultimatevideo.profile-videos', '{"title":"Ultimate Video","titleCount":true,"nomobile":"0","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}', 12);
		}

		// check if the profile video widget of SE video existed or not,
		// it it is existed, then delete it from the user profie page
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'video.profile-videos');
		$infoSEProfileVideos = $select -> query() -> fetch();

		if (!empty($infoSEProfileVideos)) {
			$db -> delete('engine4_core_content', array("content_id = {$infoSEProfileVideos['content_id']}"));
		}

		// video.profile-favorite-videos
		// Check if it's already been placed
		$select -> reset('where') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.profile-favorite-videos');
		$infoProfileFavoriteVideos = $select -> query() -> fetch();
		if (empty($infoProfileFavoriteVideos)) {
			$this -> _insertWidgetToProfileContent($page_id, 'ynultimatevideo.profile-favorite-videos', '{"title":"Favorite Videos","titleCount":true,"nomobile":"0","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}', 13);
		}

		// video.profile-video-playlists
		$select -> reset('where') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.profile-video-playlists');
		$infoProfileFavoriteVideos = $select -> query() -> fetch();
		if (empty($infoProfileFavoriteVideos)) {
			$this -> _insertWidgetToProfileContent($page_id, 'ynultimatevideo.profile-video-playlists', '{"title":"Video Playlists","titleCount":true,"mode_grid":1,"mode_list":1,"view_mode":"simple"}', 14);
		}
	}

	protected function _addVideoIndexPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_index_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array(
					'name' => 'ynultimatevideo_index_index',
					'displayname' => 'YN - Ultimate Video - Home Page',
					'title' => 'Ultimate Video',
					'description' => 'This is the home page for the ultimate Video.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '["[]"]',
			));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content',array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 1,
					'params' => '["[]"]',
			));
			$middle_top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'parent_content_id' => $middle_top_id,
					'order' => 1,
					'params' => '["[]"]',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => '["[]"]',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// insert columns : left, middle and right
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 3,
					'params' => '["[]"]',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-featured-videos',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":"","slidingDuration":"5000","nomobile":"0","name":"ynultimatevideo.list-featured-videos","slideWidth":"740","slideHeight":"400"}'
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-recommended-videos',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '{"title":"Recommended Videos","nomobile":"0","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-watch-again-videos',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"Watch it again","nomobile":"0","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.container-tabs',
					'parent_content_id' => $middle_id,
					'order' => 4,
					'params' => '{"max":"6"}',
			));
			$container_tab_id = $db -> lastInsertId('engine4_core_content');

			// insert left column
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'left',
					'parent_content_id' => $container_id,
					'order' => 1,
					'params' => '["[]"]',
			));
			$left_id = $db -> lastInsertId('engine4_core_content');

			// widgets in the container tab
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-popular-videos',
					'parent_content_id' => $container_tab_id,
					'order' => 1,
					'params' => '{"title":"Most Rated","popularType":"rating","nomobile":"0","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-popular-videos',
					'parent_content_id' => $container_tab_id,
					'order' => 2,
					'params' => '{"title":"Most Viewed","popularType":"view","nomobile":"0","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-recent-videos',
					'parent_content_id' => $container_tab_id,
					'order' => 3,
					'params' => '{"title":"Latest Videos","mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			// left column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-search',
					'parent_content_id' => $left_id,
					'order' => 1,
					'params' => '{"title":""}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-categories',
					'parent_content_id' => $left_id,
					'order' => 2,
					'params' => '{"title":"Categories"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-liked-videos',
					'parent_content_id' => $left_id,
					'order' => 3,
					'params' => '{"title":"Most Liked"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-top-members',
					'parent_content_id' => $left_id,
					'order' => 4,
					'params' => '{"title":"Top Members","name":"ynultimatevideo.list-top-members"}', ));
		}
	}

	protected function _addVideoCreatePage() {
		$db = $this -> getDb();

		// create page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynultimatevideo_index_create') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_index_create',
					'displayname' => 'YN - Ultimate Video - Video Create Page',
					'title' => 'Video Create',
					'description' => 'This page allows video to be added.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'core.content',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));
		}
	}

	protected function _addVideoEditPage() {
		$db = $this -> getDb();

		// create page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynultimatevideo_index_edit') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_index_edit',
					'displayname' => 'YN - Ultimate Video - Video Edit Page',
					'title' => 'Video Edit Page',
					'description' => 'This page allows video to be edited.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'core.content',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));
		}
	}

	protected function _addPlaylistCreatePage() {
		$db = $this -> getDb();

		// create page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynultimatevideo_index_create-playlist') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_index_create-playlist',
					'displayname' => 'YN - Ultimate Video - Playlist Create Page',
					'title' => 'Playlist Create',
					'description' => 'This page allows playlist to be added.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'core.content',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));
		}
	}

	protected function _addPlaylistEditPage() {
		$db = $this -> getDb();

		// create page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynultimatevideo_playlist_edit') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_playlist_edit',
					'displayname' => 'YN - Ultimate Video - Playlist Edit Page',
					'title' => 'Playlist Edit Page',
					'description' => 'This page allows playlist to be edited.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array('type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array('type' => 'widget',
					'name' => 'core.content',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));
		}
	}

	private function _addContentTopAndContent($page_id, $widgetContent = 'core.content', $managePage = 0) {
		$db = $this -> getDb();

		// top
		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
		));
		$top_id = $db -> lastInsertId('engine4_core_content');

		// top contents
		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $top_id,
				'order' => 1,
		));
		$top_middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynultimatevideo.browse-menu',
				'parent_content_id' => $top_middle_id,
				'order' => 1,
		));

		// main
		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
		));
		$main_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'container',
				'name' => 'right',
				'parent_content_id' => $main_id,
				'order' => 1,
		));
		$main_right_id = $db -> lastInsertId('engine4_core_content');

		if ($managePage) {
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.manage-menu',
					'parent_content_id' => $main_right_id,
					'order' => 1,
					'params' => '{"title":""}',
			));
		}

		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynultimatevideo.browse-search',
				'parent_content_id' => $main_right_id,
				'order' => 2,
				'params' => '{"title":""}',
		));

		$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $main_id,
				'order' => 2,
		));
		$main_middle_id = $db -> lastInsertId('engine4_core_content');

		if ($managePage) {
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynultimatevideo.manage-menu',
				'parent_content_id' => $main_middle_id,
				'order' => 1,
				'params' => '{"title":""}',
			));
		}

		$db -> insert('engine4_core_content', array('page_id' => $page_id,
			'type' => 'widget',
			'name' => $widgetContent,
			'parent_content_id' => $main_middle_id,
			'order' => 2,
		));

	}

	protected function _addMyVideosPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_index_manage') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_index_manage',
					'displayname' => 'YN - Ultimate Video - My Videos Page',
					'title' => 'My Videos',
					'description' => 'This is the view page for videos posted by the current user.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynultimatevideo.list-manage-videos', 1);
		}
		else
		{
			$page_id = $info['page_id'];
			$manage_menu_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.manage-menu') -> limit(1) -> query() -> fetchColumn();
			if (!$manage_menu_widget_id)
			{
				$main_middle_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.list-manage-videos') -> limit(1) -> query() -> fetchColumn();
				if ($main_middle_id)
				{
					$db -> insert('engine4_core_content', array('page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynultimatevideo.manage-menu',
						'parent_content_id' => $main_middle_id,
						'order' => 0,
						'params' => '{"title":""}',
					));
				}
			}
		}
	}

	protected function _addManagePlaylistPage() {
		$db = $this->getDb();

		$page_id = $db->select()
				->from('engine4_core_pages', 'page_id')
				->where('name = ?', 'ynultimatevideo_playlist_manage')
				->limit(1)
				->query()
				->fetchColumn();

		if(!$page_id) {
			$db->insert('engine4_core_pages', array(
					'name' => 'ynultimatevideo_playlist_manage',
					'displayname' => 'YN - Ultimate Video - My Playlists Page',
					'title' => 'Manage Playlists Page',
					'description' => 'Manage Playlists Page',
					'custom' => 0
			));
			$page_id = $db->lastInsertId();

			// Insert top
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db->lastInsertId();

			//Insert main
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db->lastInsertId();

			//Insert top-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			// Insert main-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db->lastInsertId();

			//Insert main-right
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'right',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 1,
			));
			$main_right_id = $db->lastInsertId();

			//Insert menu
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			//Insert content
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-my-playlists',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));

			//Insert manage menu widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.manage-menu',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 1,
			));

			//Insert playlist create button
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynultimatevideo.playlist-create-link',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 2,
			));

				//Insert search widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-search',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 3,
			));
		}
		else
		{
			$manage_menu_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.manage-menu') -> limit(1) -> query() -> fetchColumn();
			if (!$manage_menu_widget_id)
			{
				$main_middle_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.list-my-playlists') -> limit(1) -> query() -> fetchColumn();
				if ($main_middle_id)
				{
					$db -> insert('engine4_core_content', array('page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynultimatevideo.manage-menu',
						'parent_content_id' => $main_middle_id,
						'order' => 0,
						'params' => '{"title":""}',
					));
				}
			}
		}
	}

	protected function _addVideoListingPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_index_list') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_index_list',
					'displayname' => 'YN - Ultimate Video - Videos Browse Page',
					'title' => 'Listing Videos',
					'description' => 'This is the ultimate Video listing page.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// top
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
			));
			$top_id = $db -> lastInsertId('engine4_core_content');

			// top contents
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 1,
			));
			$top_middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			// main
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
			));
			$main_id = $db -> lastInsertId('engine4_core_content');

			// main middle
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_id,
					'order' => 2,
					'params' => '["[]"]',
			));
			$main_middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-videos',
					'parent_content_id' => $main_middle_id,
					'order' => 1,
					'params' => '{"mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			// main right
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $main_id,
					'order' => 2,
					'params' => '["[]"]',
			));
			$main_right_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-search',
					'parent_content_id' => $main_right_id,
					'order' => 1,
					'params' => '{"title":""}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-categories',
					'parent_content_id' => $main_right_id,
					'order' => 2,
					'params' => '{"title":"Categories"}',
			));
		}
	}

	protected function _addVideoFavoritePage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_favorite_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_favorite_index',
					'displayname' => 'YN - Ultimate Video - My Favorite Videos Page',
					'title' => 'View Favorite Videos',
					'description' => 'This is the view page for favorite videos.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynultimatevideo.list-my-favorite-videos', 1);
		}
		else
		{
			$page_id = $info['page_id'];
			$manage_menu_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.manage-menu') -> limit(1) -> query() -> fetchColumn();
			if (!$manage_menu_widget_id)
			{
				$main_middle_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.list-my-favorite-videos') -> limit(1) -> query() -> fetchColumn();
				if ($main_middle_id)
				{
					$db -> insert('engine4_core_content', array('page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynultimatevideo.manage-menu',
						'parent_content_id' => $main_middle_id,
						'order' => 0,
						'params' => '{"title":""}',
					));
				}
			}
		}
	}

	protected function _addVideoPlaylistsPage() {
		$db = $this->getDb();

		$page_id = $db->select()
				->from('engine4_core_pages', 'page_id')
				->where('name = ?', 'ynultimatevideo_playlist_index')
				->limit(1)
				->query()
				->fetchColumn();

		if (!$page_id) {
			$db -> insert('engine4_core_pages', array(
					'name' => 'ynultimatevideo_playlist_index',
					'displayname' => 'YN - Ultimate Video - Playlists Browse Page',
					'title' => 'Video Playlists Page',
					'description' => 'This is the view page for video playlists.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db->lastInsertId();

			//Insert main
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db->lastInsertId();

			//Insert top-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			// Insert main-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db->lastInsertId();

			//Insert main-right
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'right',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 1,
			));
			$main_right_id = $db->lastInsertId();

			//Insert menu
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			//Insert playlists listing widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-playlists',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));

			//Insert search widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-search',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 1,
			));

			//Insert playlist create button
			$db->insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynultimatevideo.playlist-create-link',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 2,
			));
		}
	}

	protected function _addVideoPlaylistDetailsPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_playlist_view') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_playlist_view',
					'displayname' => 'YN - Ultimate Video - Playlist View Page',
					'title' => 'View Video Playlist Detail',
					'description' => 'This is the view page for the video playlist detail.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// top
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
			));
			$top_id = $db -> lastInsertId('engine4_core_content');

			// top contents
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 1,
			));
			$top_middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.playlist-profile-slideshow',
					'parent_content_id' => $top_middle_id,
					'order' => 2,
			));

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => '["[]"]',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// insert columns : middle and right
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 2,
					'params' => '["[]"]',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 1,
					'params' => '["[]"]',
			));
			$right_id = $db -> lastInsertId('engine4_core_content');

			// middle column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.playlist-profile-info',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '["[]"]',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.playlist-profile-listings',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '{"mode_simple":1,"mode_list":1,"mode_casual":1,"view_mode":"simple"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.comments',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '["[]"]',
			));

			// right column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.playlist-profile-same-poster',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => '{"title":""}',
			));
		}
	}

	protected function _addVideoWatchlaterPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_watch-later_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_watch-later_index',
					'displayname' => 'YN - Ultimate Video - Watch Later Page',
					'title' => 'View Watch Later Videos',
					'description' => 'This is the view page for watch later videos.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynultimatevideo.list-my-watch-later-videos', 1);
		}
		else
		{
			$page_id = $info['page_id'];
			$manage_menu_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.manage-menu') -> limit(1) -> query() -> fetchColumn();
			if (!$manage_menu_widget_id)
			{
				$main_middle_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.list-my-watch-later-videos') -> limit(1) -> query() -> fetchColumn();
				if ($main_middle_id)
				{
					$db -> insert('engine4_core_content', array('page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynultimatevideo.manage-menu',
						'parent_content_id' => $main_middle_id,
						'order' => 0,
						'params' => '{"title":""}',
					));
				}
			}
		}
	}

	protected function _addVideoDetailPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynultimatevideo_index_view') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynultimatevideo_index_view',
					'displayname' => 'YN - Ultimate Video - Video View Page',
					'title' => 'View Video',
					'description' => 'This is the view page for a video.',
					'custom' => 0,
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '["[]"]',
			));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 1,
					'params' => '["[]"]',
			));
			$middle_top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'parent_content_id' => $middle_top_id,
					'order' => 1,
					'params' => '["[]"]',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => '["[]"]',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// insert columns : middle and right
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 2,
					'params' => '["[]"]',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 1,
					'params' => '["[]"]',
			));
			$right_id = $db -> lastInsertId('engine4_core_content');

			// middle column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '["[]"]',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.comments',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '["[]"]',
			));

			// right column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.show-same-categories',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => '{"title":"Related Videos"}',
			));

			$db -> insert('engine4_core_content', array('page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynultimatevideo.show-same-poster',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => '{"title":"From the same Member"}',
			));
		}
	}

	protected function _addHistoryPage() {
		$db = $this->getDb();

		$page_id = $db->select()
				->from('engine4_core_pages', 'page_id')
				->where('name = ?', 'ynultimatevideo_history_index')
				->limit(1)
				->query()
				->fetchColumn();

		if(!$page_id) {
			$db->insert('engine4_core_pages', array(
					'name' => 'ynultimatevideo_history_index',
					'displayname' => 'YN - Ultimate Video - My History Page',
					'title' => 'Ultimate Video History Page',
					'description' => 'Ultimate Video History Page',
					'custom' => 0,
			));
			$page_id = $db->lastInsertId();

			// Insert top
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'top',
					'page_id' => $page_id,
					'order' => 1,
			));
			$top_id = $db->lastInsertId();

			//Insert main
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 2,
			));
			$main_id = $db->lastInsertId();

			//Insert top-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $top_id,
			));
			$top_middle_id = $db->lastInsertId();

			// Insert main-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 2,
			));
			$main_middle_id = $db->lastInsertId();

			//Insert main-right
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'right',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
					'order' => 1,
			));
			$main_right_id = $db->lastInsertId();

			//Insert menu
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-menu',
					'page_id' => $page_id,
					'parent_content_id' => $top_middle_id,
					'order' => 1,
			));

			//Insert history widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.list-history',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
					'params' => '{"itemCountPerPage":"6"}',
			));

			//Insert manage menu widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.manage-menu',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 1,
			));

			//Insert search widget
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynultimatevideo.browse-search',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 2,
			));
		}
		else
		{
			$manage_menu_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.manage-menu') -> limit(1) -> query() -> fetchColumn();
			if (!$manage_menu_widget_id)
			{
				$main_middle_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynultimatevideo.list-history') -> limit(1) -> query() -> fetchColumn();
				if ($main_middle_id)
				{
					$db -> insert('engine4_core_content', array('page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynultimatevideo.manage-menu',
						'parent_content_id' => $main_middle_id,
						'order' => 0,
						'params' => '{"title":""}',
					));
				}
			}
		}
	}
}
