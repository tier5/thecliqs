<?php

/**
 *
 * XXX: longlt continue implement 
 */
class Yncontest_Api_ConvertMailVars extends Core_Api_Abstract 
{
	
	protected static $_baseUrl;
	
	public static function getBaseUrl()
	{
		$request =  Zend_Controller_Front::getInstance()->getRequest();
		if(self::$_baseUrl == NULL && $request)
		{
			self::$_baseUrl = sprintf('%s://%s', $request->getScheme(), $request->getHttpHost());
			
		}
		return self::$_baseUrl;
	}
	/**
	 * @param   string $type
	 * @return  string
	 */
	public function selfURL() 
    {
      return self::getBaseUrl();
    }

	public function inflect($type) {
		return sprintf('vars_%s', $type);
	}

	public function vars_default($params, $vars) {
		return $params;
	}

	/**
	 * call from api
	 */
	public function process($params, $vars, $type) {
		$method_name = $this->inflect($type);
		if(method_exists($this, $method_name)) {
			return $this -> {$method_name}($params, $vars);
		}
		return $this -> vars_default($params, $vars);
	}

	/**
	 *
	 */
	public function vars_yncontest_dealbought($params, $vars) {
		return $params;
	}
		
	public function vars_ban_participant($params, $vars) {
			
		$rparams = array();
		//$rparams['deal_title'] = "\"".$params['title']."\"";
		//$deal = Engine_Api::_()->getItem('deal', $params['deal_id']);  
		$url = Engine_Api::_()->getApi('settings','core')->getSetting('yncontest.baseUrl','http://');
		//$rparams['contest_link'] = $this->getBaseUrl().$deal->deal_href;
		$rparams['contest_link'] = $this->getBaseUrl().$params['contest_link'];
		
		return $rparams;
	}	
	public function vars_active_contest($params, $vars) {
			
		$rparams = array();
		$rparams['contest_link'] = $this->getBaseUrl().$params['contest_link'];
		
		return $rparams;
	}	
	public function vars_contest_denied($params, $vars) {
			
		$rparams = array();
		$rparams['contest_link'] = $this->getBaseUrl().$params['contest_link'];
	
		return $rparams;
	}
	public function vars_expired_contest($params, $vars) {
			
		$rparams = array();
		$rparams['contest_link'] = $this->getBaseUrl().$params['contest_link'];
	
		return $rparams;
	}
	
	
	
}


