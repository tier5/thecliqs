<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Factory {

    private static $_uploadedType = 3;
    private static $_videoUrlType = 5;
    private static $_supportTypes = NULL;

    /**
     * @param string|int $videoPlugin values: 1,2,3,.., youtube, vimeo
     * @return Ynultimatevideo_Plugin_Adapter_Abstract
     * @throws Exception 
     */
    public static function getPlugin($videoPlugin) {

        $pluginName = self::getSupportType($videoPlugin);

        $class = 'Ynultimatevideo_Plugin_Adapter_' . ucfirst($pluginName);
        if (class_exists($class, true)) {
            return new $class;
        }
        throw new Exception('video plugin ' . $videoPlugin . ' does not found');
    }

    /**
     * get all video support type, ex. array(1=>youtube, 2=>vimeo)
     * @param string $return_key choose: name or title
     * @return array [id=>name]
     * 
     */
    public static function getAllSupportTypes($return_key = 'title') {

        if (NULL === self::$_supportTypes) {
            self::$_supportTypes = array(
                1 => array('name' => 'youtube', 'title' => 'YouTube Video'),
                2 => array('name' => 'vimeo', 'title' => 'Vimeo Video'),
                self::$_uploadedType => array('name' => 'uploaded', 'title' => 'Uploaded Video'), // mean '3'
                4 => array('name' => 'dailymotion', 'title' => 'Dailymotion Video'),
                self::$_videoUrlType => array('name' => 'videoURL', 'title' => 'URL Video'), // mean '5'
                6 => array('name' => 'embed', 'title' => 'Embed Video'),
                7 => array('name' => 'facebook', 'title' => 'Facebook Video')
            );
        }

        $ret = array();
        foreach (self::$_supportTypes as $key => $row) {
            $ret[$key] = $row[$return_key];
        }
        return $ret;
    }

    /**
     * get the type of uploaded video
     * @return int
     */
    public static function getUploadedType() {
        return self::$_uploadedType;
    }
    
	/**
     * get the type of Video URL type
     * @return int
     */
    public static function getVideoURLType() {
        return self::$_videoUrlType;
    }

    /**
     * @param int $type
     * @param string $return_key choose: name or title
     * @return string|null
     */
    public static function getSupportType($type, $returnKey = 'name') {
        $types = self::getAllSupportTypes($returnKey);

        if (array_key_exists($type, $types)) {
            return $types[$type];
        }

        if (isset($types[$type]) && is_string($types[$type])) {
            return $types[$type];
        }

        return NULL;
    }

}

?>
