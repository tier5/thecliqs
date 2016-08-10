<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
abstract class Ynultimatevideo_Plugin_Adapter_Abstract {
    protected $_params;
    protected $_information = array();

    public function setParams($options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            } else {
                $this->_params[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Fetch a link to get the video information. 
     * After executing this method, the class will contain the video information
     */
    public abstract function fetchLink();

    /**
     * @abstract
     * Check a link is valid or not. The checked link is got from the _params, which is set in the method setParams
     * @return : false if the link is invalid, ortherwise, return the link is valid
     */
    public abstract function isValid();

    /**
     * @abstract
     * Get the thumbnail link from the other video link
     * @return : string, the link
     */
    public abstract function getVideoLargeImage();

    public abstract function getVideoDuration();

    public abstract function getVideoTitle();
    
    public abstract function getVideoDescription();
    
    public abstract function compileVideo($params);
    
    public function getVideoCode() {
        if (array_key_exists('code', $this->_information) && $this->_information['code']) {
            $code = $this->_information['code'];
        } else {
            $code = $this->extractCode();
        }
        return $code;
    }

    public function __get($info) {
        if (array_key_exists($info, $this->_information)) {
            return $this->_information[$info];
        } else {
            $method = 'get' . ucfirst(strtolower($info));
            if (method_exists($this, $method)) {
                return $this->$method();
            } else {
                throw new Ynultimatevideo_Plugin_Adapter_Exception("Specified information $info is not existed");
            }
        }
    }
}