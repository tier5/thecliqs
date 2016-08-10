<?php
class Ynmobile_Api_Twitter extends Core_Api_Abstract
{
	protected $_api;
	protected $_oauth;
	
	public function getApi($key, $secret)
	{
		if( null === $this->_api ) {
			$this->_initializeApi($key, $secret);
		}
	
		return $this->_api;
	}
	
	public function getOauth($key, $secret)
	{
		if( null === $this->_oauth ) {
			$this->_initializeApi($key, $secret);
		}
	
		return $this->_oauth;
	}
	
	public function clearApi()
	{
		$this->_api = null;
		$this->_oauth = null;
		return $this;
	}
	
	public function isConnected()
	{
		// @todo make sure that info is validated
		return ( !empty($_SESSION['twitter_token2']) && !empty($_SESSION['twitter_secret2']) );
	}
	
	protected function _initializeApi($key, $secret)
	{
		// Load classes
		include_once 'Services/Twitter.php';
		include_once 'HTTP/OAuth/Consumer.php';
	
		if( !class_exists('Services_Twitter', false) || !class_exists('HTTP_OAuth_Consumer', false) ) 
		{
			throw new Core_Model_Exception('Unable to load twitter API classes');
		}
	
		$this->_api = new Services_Twitter();
	
		// Get oauth
		if( isset($_SESSION['twitter_token2'], $_SESSION['twitter_secret2']) ) {
			$this->_oauth = new HTTP_OAuth_Consumer($key, $secret,
					$_SESSION['twitter_token2'], $_SESSION['twitter_secret2']);
		} else if( isset($_SESSION['twitter_token'], $_SESSION['twitter_secret']) ) {
			$this->_oauth = new HTTP_OAuth_Consumer($key, $secret,
					$_SESSION['twitter_token'], $_SESSION['twitter_secret']);
		} else {
			$this->_oauth = new HTTP_OAuth_Consumer($key, $secret);
		}
		$this->_api->setOAuth($this->_oauth);
	}
	
}