<?php
/**
 * 
 * @author MinhNC
 */
abstract class Ynpayment_Api_Paymentgateway
{
	protected $_gatewaySettings = array();
	protected $_order = array();
	//the core library for payments,
	protected $_core;
	public function __call($method, $args)
	{
		try
		{
			if ( ! method_exists($this->_core, $method))
			{
				throw new Exception('Call to undefined method %s::%s() in %s on line %s');
			}
			else if ( ! is_callable(array($this->_core, $method)))
			{
				throw new Exception('Call to private method %s::%s() in %s on line %s');
			}
		}
		catch(Exception $e)
		{
			$backtrace = $e->getTrace();
			$backtrace = $backtrace[1];
			return trigger_error(sprintf($e->getMessage(), $backtrace['class'], $backtrace['function'], $backtrace['file'], $backtrace['line']));
		}
		
		return call_user_func_array(array($this->_core, $method), $args);
	}
	public function plugin_settings($key, $default = FALSE)
	{
		$settings = $this->_gatewaySettings;
		
		if ($key === FALSE)
		{
			return ($settings) ? $settings : $default;
		}
		
		return (isset($settings[$key])) ? $settings[$key] : $default;
	}
	public function order($key = FALSE)
	{
		if ($key !== FALSE)
		{
			return (isset($this->_order[$key])) ? $this->_order[$key] : FALSE;
		}
		
		return $this->_order;
	}
	public function year_2($year)
	{
		if (strlen($year > 2))
		{
			return substr($year, -2);
		}
		return str_pad($year, 2, '0', STR_PAD_LEFT);
	}
	public function strip_punctuation($text) 
    {
        return preg_replace('/[^a-zA-Z0-9\s-_]/', ' ', $text);
    }
}