<?php

/**
 * import first class.
 *
 */
require_once YNMEDIAIMPORTER_PROVIDER_PATH . '/Abstract.php';

/**
 * this should be used as singleton class only.
 * call via static only.
 * Ynmediaimporter::getInstance();
 */
class Ynmediaimporter
{

    protected static $_log;

    /**
     * do not instance any of this class
     * ignore constructor
     */
    private function __construct()
    {

    }

    /**
     * @var Ynmediaimporter
     */
    static protected $_instance = null;

    /**
     * Zend Cache instance
     * @var Zend_Cache
     */
    static protected $_cache = null;

    /**
     * check provider.
     * list of providers.
     * @var array
     */
    static protected $_providers = array();

    /**
     * @return Ynmediaimporter
     */
    static public function getInstance()
    {
        if (null == self::$_instance)
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * get provider service.
     * name should be lowercase
     * @param string $name
     * @param bool $singleton is used to instance only one, default  = 1, strong
     * recommend to save performance.
     * @return Ynmediaimporter_Proider_Abstract
     * @throws Exception when provider not found.
     */
    static public function getProvider($name, $singleton = true)
    {
        $name = strtolower(trim($name));

        if ($singleton && isset(self::$_providers[$name]))
        {
            return self::$_providers[$name];
        }

        $file = APPLICATION_PATH . '/application/modules/Ynmediaimporter/Provider/' . ucfirst($name) . '.php';
        $class = 'Ynmediaimporter_Provider_' . ucfirst($name);

        if (file_exists($file))
        {
            require_once $file;

            // check that class is exists!
            if (class_exists($class, false))
            {
                return self::$_providers[$name] = new $class;
            }
        }

        throw new Exception("service $name  has not supported.");
    }

    /**
     * @param string|array $data
     * @param string|array $message
     * @param string $filename  a part of file name under
     * YNMEDIAIMPORTER_LOG_PATH
     * @return void.
     */
    static public function log($data, $message = null)
    {
        if (false == YNMEDIAIMPORTER_DEBUG)
        {
            return;
        }

        if (null == self::$_log)
        {
            $file = YNMEDIAIMPORTER_LOG_PATH . '/importer.log';
            self::$_log = new Zend_Log(new Zend_Log_Writer_Stream($file));
        }

        if (!is_string($data))
        {
            $data = var_export($data, 1);
        }

        if (null == $message)
        {
            $message = 'info';
        }
        else
        if (!is_string($message))
        {
            $message = var_export($message, 1);
        }

        self::$_log -> log($message . PHP_EOL . $data, Zend_Log::INFO);
    }

    static public function getCache()
    {
        if (null == self::$_cache)
        {
            $cacheDir = APPLICATION_PATH . '/temporary/cache';
            // First, set up the Cache

            $frontendOptions = array('automatic_serialization' => true);

            $backendOptions = array('cache_dir' => $cacheDir);

            self::$_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        }

        return self::$_cache;

    }

    static public function setupScheduler($photos = null, $albums = null, $album_id = 0, $params = array())
    {
        $tableScheduler = Engine_Api::_() -> getDbTable('Schedulers', 'Ynmediaimporter');
        $tableNodes = Engine_Api::_() -> getDbTable('Nodes', 'Ynmediaimporter');

        $scheduler = $tableScheduler -> fetchNew();

        $user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
        $scheduler -> owner_id = $user_id;
        $scheduler -> owner_type = 'user';
        $scheduler -> user_id = $user_id;
		$scheduler -> params = json_encode($params);
		
        $scheduler -> save();
        $schedulerId = $scheduler -> scheduler_id;

        $db = $tableNodes -> getAdapter();

        $nodeId = array();
        $album_id = intval($album_id);

        if (is_array($albums) && !empty($albums))
        {

            foreach ($albums as $id)
            {
                $nodeId[] = $db -> quote($id);
            }
        }

        if (is_array($photos) && !empty($photos))
        {
            foreach ($photos as $id)
            {
                $nodeId[] = $db -> quote($id);
            }
        }

        $nodeId = implode(',', $nodeId);

        $db -> update($tableNodes -> info('name'), array(
            'scheduler_id' => $schedulerId,
            'user_aid' => $album_id,
            'status' => 1, // in schdule
        ), "nid IN ($nodeId) and user_id = '$user_id'");

        return $schedulerId;
    }

    static public function getValidDir()
    {
        $dir = APPLICATION_PATH . '/public';

        foreach (array('media',date('Y'),date('m'), date('d')) as $sub)
        {
            $dir = $dir . '/' . $sub;
            if (!realpath($dir))
            {
                if (!mkdir($dir, 0777))
                {
                    throw new Exception("$dis is not writeable or is not exists!");
                }
            }
        }
        return $dir;
    }

    static public function processScheduler($scheduler, $user_aid = null, $limit = 10, $sendNotification = false, $sendActivity = true)
    {
        $schedulerId = $scheduler -> scheduler_id;
        $tableNode = Engine_Api::_() -> getDbTable('Nodes', 'Ynmediaimporter');
        $album_type = Engine_Api::_() -> hasItemType('advalbum_album') ? 'advalbum_album' : 'album';
        $photo_type = Engine_Api::_() -> hasItemType('advalbum_album') ? 'advalbum_photo' : 'photo';
        $tableAlbum = Engine_Api::_() -> getItemTable($album_type);
        $tablePhoto = Engine_Api::_() -> getItemTable($photo_type);

        $tableFile = Engine_Api::_() -> getItemTable('storage_file');

        $tableUser = Engine_Api::_() -> getItemTable('user');

        $userId = $scheduler -> user_id;
        $user = $tableUser -> find($userId) -> current();
        $album = null;
        // skip limit for import large of albums 
        $select = $tableNode -> select() -> where('scheduler_id=?', $schedulerId) -> where('status<3') -> where('status>0');

        if ($user_aid)
        {
            $select -> where('user_aid=?', intval($user_aid));
        }

        foreach ($tableNode->fetchAll($select) as $node)
        {
            if ('photo' == $node -> media && $node -> user_aid > 0)
            {

                $album = $tableAlbum -> find($node -> user_aid) -> current();

                if (!is_object($album))
                {
                    continue;
                }

                /**
                 * download file
                 */
                $dir = Ynmediaimporter::getValidDir();
                $file = $dir . '/' . $node -> getUUID();

                /**
                 * process bigger link
                 */
                $result = copy($node -> getDownloadFilename(), $file);

                $params = array(
                    'owner_id' => $userId,
                    'owner_type' => 'user',
                    'album_id' => $album -> getIdentity(),
                    'title' => $node -> title,
                    'description' => $node -> description,
                );

                $row = $tablePhoto -> createRow();
                $row -> setFromArray($params);
                self::__setPhoto($row, $file);
                $row -> save();

                $node -> status = 3;
                $node -> save();
            }
            else
            if (in_array($node -> media, array(
                'album',
                'photoset',
                'gallery'
            )) && 0 == $node -> user_aid)
            {
                // create new albums for this roles
                $album = self::createPhotoAlbums($scheduler, $node);

                // setup album and node.
                // update all sub node of current scheduler to this albums.

                $tableNode -> update(array(
                    'user_aid' => $album -> getIdentity(),
                    'status' => 1
                ), array(
                    'scheduler_id=?' => $schedulerId,
                    'aid' => $node -> aid
                ));

                $node -> user_aid = $album -> getIdentity();
                $node -> status = 1;
                $node -> save();
                self::processScheduler($scheduler, $album -> getIdentity(), 10, 0, 0);
                break;
                // force process this album to escape no value style.

            }
        }

        $remain = intval($tableNode -> select() -> from($tableNode -> info('name'), array('count(*)')) -> where('scheduler_id=?', $schedulerId) -> where('media=?', 'photo') -> where('status<3') -> query() -> fetchColumn(0));

        // all scheduler is completed. send notification to users
        if (is_object($album) && $remain == 0)
        {
            if ($sendNotification)
            {
                // send notification.
                Engine_Api::_() -> getDbTable('Notifications', 'Activity') -> addNotification($user, $subject = $user, $object = $album, $type = 'ynmediaimporter_imported', null);
            }

            if ($sendActivity)
            {
                $tableActivity = Engine_Api::_() -> getDbtable('actions', 'activity');
                $action = $tableActivity -> addActivity($subject = $user, $object = $album, 'ynmediaimporter_imported', null);
            }
        }

        $scheduler -> status = $remain == 0 ? 3 : 1;
        $scheduler -> last_run = time();
        $scheduler -> save();
        
        // and of process rec count all
        
        $sql = "SELECT album.node_id, (
                SELECT COUNT( * ) 
                FROM engine4_ynmediaimporter_nodes AS photo
                WHERE photo.media =  'photo'
                AND photo.aid = album.id
                AND photo.status =1
                ) as remaining
                FROM  `engine4_ynmediaimporter_nodes` album
                WHERE
                album.media <>  'photo' AND album.status =1 
                group by album.node_id
                having remaining = 0";
                
        $db = $tableNode->getAdapter();
        
        $completedList = $db->fetchCol($sql);
        
        
        if($completedList){
           $db->update($tableNode->info('name'),array('status'=>3),'node_id IN ('.implode(',',$completedList). ')');
        }
        
        return array(
            'remain' => $remain,
            'scheduler_id' => $schedulerId,
        );
    }

    static public function createPhotoAlbums($scheduler, $node)
    {
        $set_cover = false;
        $params = json_decode($scheduler -> params, 1);
        $params['owner_id'] = $node -> owner_id;
        $params['owner_type'] = $node -> owner_type;
        $params['title'] = empty($node -> title) ? 'Untitled Album' : $node -> title;
		
        $album_type = Engine_Api::_() -> hasItemType('advalbum_album') ? 'advalbum_album' : 'album';

        $album = Engine_Api::_() -> getItemTable($album_type) -> createRow();

        $album -> setFromArray($params);
        $album -> save();
        $set_cover = true;

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

        if (empty($params['auth_view']))
        {
            $params['auth_view'] = 'everyone';
        }
        if (empty($params['auth_comment']))
        {
            $params['auth_comment'] = 'owner_member';
        }
        if (empty($params['auth_tag']))
        {
            $params['auth_tag'] = 'owner_member';
        }
        
        $viewMax = array_search($params['auth_view'], $roles);
        $commentMax = array_search($params['auth_comment'], $roles);
        $tagMax = array_search($params['auth_tag'], $roles);

        foreach ($roles as $i => $role)
        {
            $auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
            $auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
            $auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
        }
        return $album;
    }

    /**
     * clear cache for current session
     */
    static public function clearCache()
    {
        try
        {
            $ssid = session_id();
            if ($ssid)
            {
                self::getCache() -> remove($ssid);
            }
            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
        return 1;
    }

    static public function resetAll()
    {
        $params = array();

        if (isset($_SESSION['YNMEDIAIMPORTER']))
        {
            unset($_SESSION['YNMEDIAIMPORTER']);
        }

        if (isset($_SESSION[YNMEDIAIMPORTER_SSID]) && !empty($_SESSION[YNMEDIAIMPORTER_SSID]))
        {
            $params['ssid'] = $_SESSION[YNMEDIAIMPORTER_SSID];
            file_get_contents(YNMEDIAIMPORTER_CENTRALIZE_HOST . '/index/reset?' . http_build_query($params));
        }
        return;
    }

    static public function __setPhoto($item, $photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo -> getFileName();
            $fileName = $file;
        }
        else
        if ($photo instanceof Storage_Model_File)
        {
            $file = $photo -> temporary();
            $fileName = $photo -> name;
        }
        else
        if ($photo instanceof Core_Model_Item_Abstract && !empty($photo -> file_id))
        {
            $tmpRow = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
            $file = $tmpRow -> temporary();
            $fileName = $tmpRow -> name;
        }
        else
        if (is_array($photo) && !empty($photo['tmp_name']))
        {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        }
        else
        if (is_string($photo) && file_exists($photo))
        {
            $file = $photo;
            $fileName = $photo;
        }
        else
        {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName)
        {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $item -> getType(),
            'parent_id' => $item -> getIdentity(),
            'user_id' => $item -> owner_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_() -> getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(720, 720) -> write($mainPath) -> destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(140, 160) -> write($normalPath) -> destroy();

        // Store
        try
        {
            $iMain = $filesTable -> createFile($mainPath, $params);
            $iIconNormal = $filesTable -> createFile($normalPath, $params);

            $iMain -> bridge($iIconNormal, 'thumb.normal');
        }
        catch( Exception $e )
        {
            // Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            // Throw
            if ($e -> getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE)
            {
                throw new Album_Model_Exception($e -> getMessage(), $e -> getCode());
            }
            else
            {
                throw $e;
            }
        }

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);

        // Update row
        $item -> modified_date = date('Y-m-d H:i:s');
        $item -> file_id = $iMain -> file_id;
        $item -> save();

        // Delete the old file?
        if (!empty($tmpRow))
        {
            $tmpRow -> delete();
        }

        return $item;
    }

}
