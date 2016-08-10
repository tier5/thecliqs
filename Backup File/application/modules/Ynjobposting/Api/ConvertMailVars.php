<?php

class Ynjobposting_Api_ConvertMailVars extends Core_Api_Abstract 
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

	public function vars_ynjobposting_jobalert($params, $vars) {
		return $params;
	}
	
}


