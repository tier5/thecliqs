<?php
class Mp3music_IndexController extends Core_Controller_Action_Standard
{
	public function init()
	{
		
	}

	public function browseAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'view') -> isValid())
			return;
		$request = Zend_Controller_Front::getInstance()->getRequest();
	  	$params = $request->getParams ();
		if (!empty($params['search']) && ($params['search'] == "playlists" || $params['search'] == "browse_playlists"))
			return $this -> _redirect('mp3-music/browse-playlists/1/search/' . $params['search'] . '/title/' . urlencode(htmlspecialchars($params['title'])));
		if (!empty($params['search']) && ($params['search'] == "album" || $params['search'] == "browse_new_albums"))
			return $this -> _redirect('mp3-music/browse-albums/1/search/' . $params['search'] . '/title/' . urlencode(htmlspecialchars($params['title'])));
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function browsealbumsAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'view') -> isValid())
			return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function browseplaylistsAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_playlist', null, 'view') -> isValid())
			return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function migrateAction()
	{
		// UNLIMIT EXECUTION TIME
		set_time_limit(0);
		$db_config = array(
			'host' => 'localhost',
			'username' => 'modules_sedemo',
			'password' => 'a123456',
			'db_name' => 'modules_se4demo'
		);
		// CONNECT TO OLD SE3 DATABASE
		$conn = mysql_connect($db_config['host'], $db_config['username'], $db_config['password']);
		if (mysql_select_db($db_config['db_name'], $conn))
		{
			// CONNECT SUCCESSFULLY - SET CHARSET FOR CURRENT CONNECTION
			$charset = 'utf8';
			if (mysql_set_charset($charset) == TRUE)
			{
				echo "> SET CHARSET TO $charset <br />";
			}
			else
			{
				echo "> FAILED TO SET CHARSET TO $charset <br />";
			}

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			// MIGRATE MP3 MUSIC ALBUM
			$sql = "SELECT * FROM engine4_ynmusic_albums";
			$resource = mysql_query($sql);

			$albumTable = Engine_Api::_() -> getDbTable('albums', 'mp3music');
			while (@$album_old = mysql_fetch_assoc($resource))
			{
				$albumRow = $albumTable -> createRow();
				$albumRow -> album_id = $album_old['album_id'];
				$albumRow -> title = $album_old['title'];
				$albumRow -> title_url = $album_old['title_ascii'];
				$albumRow -> description = $album_old['description'];
				$albumRow -> photo_id = $album_old['photo_id'];
				$albumRow -> user_id = $album_old['owner_id'];
				$albumRow -> search = $album_old['search'];
				$albumRow -> composer = $album_old['composer'];
				$albumRow -> creation_date = $album_old['creation_date'];
				$albumRow -> modified_date = $album_old['modified_date'];
				$albumRow -> play_count = $album_old['play_count'];
				$albumRow -> download_count = $album_old['download_count'];
				$albumRow -> is_download = $album_old['isdownload'];

				$albumRow -> save();
			}

			// MIGRATE MP3 MUSIC PLAYLIST
			$sql = "SELECT * FROM engine4_ynmusic_playlists";
			$resource = mysql_query($sql);

			$playlistTable = Engine_Api::_() -> getDbTable('playlists', 'mp3music');
			while (@$playlist_old = mysql_fetch_assoc($resource))
			{
				$playlistRow = $playlistTable -> createRow();
				$playlistRow -> playlist_id = $playlist_old['playlist_id'];
				$playlistRow -> title = $playlist_old['title'];
				$playlistRow -> title_url = $playlist_old['title_ascii'];
				$playlistRow -> description = $playlist_old['description'];
				$playlistRow -> photo_id = $playlist_old['photo_id'];
				$playlistRow -> user_id = $playlist_old['owner_id'];
				$playlistRow -> search = $playlist_old['search'];
				$playlistRow -> profile = $playlist_old['profile'];
				$playlistRow -> creation_date = $playlist_old['creation_date'];
				$playlistRow -> modified_date = $playlist_old['modified_date'];
				$playlistRow -> save();
			}

			// MIGRATE MP3 MUSIC CATEGORIES
			$sql = "SELECT * FROM engine4_ynmusic_cats";
			$resource = mysql_query($sql);

			$catTable = Engine_Api::_() -> getDbTable('cats', 'mp3music');
			while (@$cat_old = mysql_fetch_assoc($resource))
			{
				$catRow = $catTable -> createRow();
				$catRow -> cat_id = $cat_old['cat_id'];
				$catRow -> title = $cat_old['title'];
				$catRow -> title_url = locdau($cat_old['title']);
				$catRow -> save();
			}

			// MIGRATE MP3 MUSIC SINGER
			$sql = "SELECT * FROM engine4_ynmusic_singers";
			$resource = mysql_query($sql);

			$singerTable = Engine_Api::_() -> getDbTable('singers', 'mp3music');
			while (@$singer_old = mysql_fetch_assoc($resource))
			{
				$singerRow = $singerTable -> createRow();
				$singerRow -> singer_id = $singer_old['singer_id'];
				$singerRow -> title = $singer_old['title'];
				$singerRow -> title_url = $singer_old['title_ascii'];
				$singerRow -> singer_type = $singer_old['singertype_id'];
				$singerRow -> photo_id = $singer_old['photo_id'];
				$singerRow -> play_count = $singer_old['play_count'];
				$singerRow -> save();
			}

			// MIGRATE MP3 MUSIC SINGER TYPE
			$sql = "SELECT * FROM engine4_ynmusic_singertypes";
			$resource = mysql_query($sql);
			$singertTable = Engine_Api::_() -> getDbTable('singerTypes', 'mp3music');
			while (@$singert_old = mysql_fetch_assoc($resource))
			{
				$singertRow = $singertTable -> createRow();
				$singertRow -> singertype_id = $singert_old['singertype_id'];
				$singertRow -> title = $singert_old['title'];
				$singertRow -> save();
			}

			// MIGRATE MP3 MUSIC RATING
			$sql = "SELECT * FROM engine4_ynmusic_votes";
			$resource = mysql_query($sql);

			$raingTable = Engine_Api::_() -> getDbTable('ratings', 'mp3music');
			while (@$vote_old = mysql_fetch_assoc($resource))
			{
				$ratingRow = $raingTable -> createRow();
				$ratingRow -> rating_id = $vote_old['vote_id'];
				$ratingRow -> item_id = $vote_old['item_id'];
				$ratingRow -> user_id = $vote_old['owner_id'];
				$ratingRow -> rating = $vote_old['vote_value'];
				$ratingRow -> save();
			}

			// MIGRATE MP3 MUSIC PLAYLIST SONG
			$sql = "SELECT * FROM engine4_ynmusic_playlist_songs";
			$resource = mysql_query($sql);
			$songPTable = Engine_Api::_() -> getDbTable('playlistSongs', 'mp3music');
			while (@$songP_old = mysql_fetch_assoc($resource))
			{
				$songPRow = $songPTable -> createRow();
				$songPRow -> song_id = $songP_old['song_id'];
				$songPRow -> playlist_id = $songP_old['playlist_id'];
				$songPRow -> file_id = $songP_old['file_id'];
				$songPRow -> album_song_id = $songP_old['album_song_id'];
				$songPRow -> save();
			}

			// MIGRATE M3 MUSIC ALBUM SONGS
			$sql = "SELECT * FROM engine4_ynmusic_album_songs";
			$resource = mysql_query($sql);
			$songTable = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
			while (@$songA_old = mysql_fetch_assoc($resource))
			{
				$songRow = $songTable -> createRow();
				$songRow -> song_id = $songA_old['song_id'];
				$songRow -> album_id = $songA_old['album_id'];
				$songRow -> file_id = $songA_old['file_id'];
				$songRow -> title = $songA_old['title'];
				$songRow -> title_url = $songA_old['title_ascii'];
				$songRow -> play_count = $songA_old['play_count'];
				$songRow -> download_count = $songA_old['download_count'];
				$songRow -> filesize = $songA_old['filesize'];
				$songRow -> url = $songA_old['url'];
				$songRow -> ext = $songA_old['ext'];
				$songRow -> singer_id = $songA_old['singer_id'];
				$songRow -> cat_id = $songA_old['cat_id'];
				$songRow -> other_singer = $songA_old['singerother'];
				$songRow -> other_singer_title_url = locdau($songA_old['singerother']);
				$songRow -> comment_count = $songA_old['comments'];
				$songRow -> lyric = $songA_old['lyric'];
				$songRow -> save();
			}
			try
			{
				$db -> commit();
				echo "> MIGRATE SUCCESSFULLY. <br />";
			}
			catch (Exception $ex)
			{
				$db -> rollback();
				echo "> MIGRATE FAILED. <br />";
				break;
			}
		}
		mysql_close($conn);
	}

}

function locdau($str)
{
	$str = strtolower($str);
	$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
	$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
	$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
	$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
	$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
	$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
	$str = preg_replace("/(đ)/", "d", $str);
	$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
	$str = preg_replace("/(-+-)/", "-", $str);
	//thay thế 2- thành 1-
	$str = preg_replace("/(^\-+|\-+$)/", "", $str);
	$str = preg_replace("/(-)/", " ", $str);
	return $str;
}
