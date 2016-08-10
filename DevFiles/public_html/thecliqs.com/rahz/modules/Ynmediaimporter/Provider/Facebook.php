<?php

/**
 * @package Social media importer
 * @subpackage provider
 * @author nam nguyen
 * @license YouNet Company.
 */
class Ynmediaimporter_Provider_Facebook extends Ynmediaimporeter_Provider_Abstract
{

    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'facebook';
	
	/**
     * 
     * @see Socialbridge_Api_Facebook
     * 
     */
     
     protected $_facebookAPI = NULL;

    /**
     * overwrite from abstract class
     *
     * @var int
     */
    protected $_maxPhotoLimit = 100;

    public function _getPhotos($params, $cache = 0)
    {
        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'after' => '',
            'uid' => 'me',
            'aid' => '',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }
		list($photos, $after) = $this->_facebookAPI->getPhotos($params);
		$params['after'] = $after;
        $data = array(
            'result' => $photos,
            'params' => $params,
            'media' => 'photo',
        );

        $this -> saveToCache($data, $this -> createCacheKey($params));

        return array(
            $this -> correctNodesStatus($data['result']),
            $data['params'],
            $data['media'],
        );

    }

    public function _getAlbums($params, $cache = false)
    {
        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'after' => '',
            'uid' => 'me',
            'aid' => '',
        ), $params);
        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }

        if ($cache && ($result = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($result),
                $params,
                'album',
            );
        }
		list($albums, $after) = $this->_facebookAPI->getAlbums($params);
		$params['after'] = $after;

        $data = array(
            'result' => $albums,
            'params' => $params,
            'media' => 'album',
        );

        $this -> saveToCache($data, $this -> createCacheKey($params));

        return array(
            $this -> correctNodesStatus($data['result']),
            $data['params'],
            $data['media'],
        );
    }

    /**
     * constructor.
     */
    public function __construct()
    {
    	if(!Engine_Api::_() -> getApi('Core', 'Ynmediaimporter')->checkSocialBridgePlugin())
		{
			return;
		}
        $this->_facebookAPI = Engine_Api::_()->socialbridge()->getInstance('facebook');
    }

    /**
     * override method from abstract class.
     * @see ./Abstract.php
     * @param string $callback_url
     * @return string
     */
    public function getAuthUrl($callback_url, $params = array())
    {
        /**
         * add more permission
         */
        $params['scope'] = "user_photos,read_stream";
		$params['redirect_uri'] = $callback_url;

        /**
         * @var string
         */
        $url = $this -> _facebookAPI -> getLoginUrl($params);
		
        return $url;
    }

    public function getDisconnectUrl()
    {
        $front =  Zend_Controller_Front::getInstance();
        $request =  $front->getRequest();
        $router = $front->getRouter();
        return $router->assemble(array('action'=>'disconnect-facebook'),'ynmediaimporter_general',true);
    }

    /**
     * do connect to this service.
     * call this method to this method in callback functions. when auth process
     * is done.
     * @param array $data , this data post form from remote server
     * @return Ynmediaimporeter_Provider_Abstract
     * @throws Exception
     */
    public function doConnect($post)
    {

        $data = array();

        if (isset($post['ssid']) && !empty($post['ssid']))
        {
            $_SESSION[YNMEDIAIMPORTER_SSID] = $post['ssid'];
        }

        if (isset($post['code']))
        {
        	$this->_facebookAPI->saveToken();
         	$token  = $this->_facebookAPI->_accessToken;
		   	if($token)
		    {
		    	$data['connect_data'] = $token;
		    	$data['connect_data_time'] =  time();
		   	}
        }
        $data['is_connected'] = 1;
		
        /**
         * fetch only 4 information.
         */
        $me = $this -> _facebookAPI -> getOwnerInfo();

        $data['user'] = (array)$me;

        /**
         * set to session.
         */
        $this -> setSession($data);

        return $this;
    }

    /**
     * return photos array
     */
    public function getAllPhoto($params)
    {
    	$count = $params['photo_count'];
        $limit = $count < 21 ? 20 : $this -> getMaxPhotoLimit();
        $total = ceil($count / $limit);
		$page = 0;
        $media = $params['media'];
        $aid = $params['aid'];
        $result = array();

        do
        {
            $photos = $this -> _getPhotos(array(
                'limit' => $limit,
                'extra' => 'aid',
                'aid' => $aid,
                'media' => $media,
            ), 1);
			
			foreach ($photos[0] as $photo)
            {
                $result[$photo['nid']] = $photo;
            }
            ++ $page;
        }
        while($page < $total);

        return $result;
    }

	public function getUserAvatarUrl()
    {
    	$data = parent::getUserAvatarUrl();
    	while (is_array($data)) {
    		$data = current($data);
    	}
    	return $data;
    }
	
    public function getUserSquareAvatarUrl()
    {
        return sprintf("https://graph.facebook.com/%s/picture/?type=large", $this -> getUserUId());
    }

    public function getUserUId()
    {
        if (null == $this -> _uid)
        {
            $user = $this -> getUser();
            $this -> _uid = $user['id'];
        }

        return $this -> _uid;
    }
    
    /**
     * check logout.
     * do disconnect to service
     * 1. clear peristant data related to this object
     * 2. call remote process to clear persitent dat on remote server.
     * 3. WARING: DO NOT UNSET SESSION ID OF SSID VALUE. IT IS SHARED TO ALL
     * SERVICE.
     * @return Ynmediaimporeter_Provider_Abstract
     * @throws Exceptions
     */
    public function doDisconnect()
    {
        $this -> unsetSession();
        return $this;
    }
   
}

