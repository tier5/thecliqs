<?php

class Yncontest_View_Helper_Currencycontest extends Zend_View_Helper_Abstract {
		
	/**
	 * @var Zend_Currency
	 */
	protected static $_currency;
	
	
	/**
	 * currency option array
	 * @var   array
	 */
	protected static $_currencyOptions;
	
	protected static $_defaultCurrency;
	
	public static function getDefaultCurrency(){
		if(self::$_defaultCurrency == NULL){
			self::$_defaultCurrency =  Engine_Api::_()->getApi('core','yncontest')->getDefaultCurrency();
		}
		return self::$_defaultCurrency;
	}
	
	/**
	 * @return  Zend_Currency 
	 */
	public static function getCurrency(){
		if(null === self::$_currency){
			self::$_currency = new Zend_Currency('en_US');
		}
		return self::$_currency;
	}
	
	protected static  function _buildCurrencyOption(){
		$table = new Yncontest_Model_DbTable_Currencies;
		$select =  $table->select();
		foreach($table->fetchAll($select) as $item){
			self::$_currencyOptions[$item->code] = array(
				'symbol'=>$item->symbol,
				'currency'=>$item->code,
				'precision'=>$item->precision,
				'display'=>$item->currencyDisplay(),
				'position'=>$item->currencyPosition(),
			);
		}
	}
	
	public static function getCurrencyOption($currency= NULL){
		if($currency == NULL){
			$currency  = self::getDefaultCurrency();
		}
		if(self::$_currencyOptions == NULL){
			self::_buildCurrencyOption();
		}
		if(isset(self::$_currencyOptions[$currency])){
				//echo self::$_currencyOptions[$currency];die;
			return self::$_currencyOptions[$currency];
			
		}
		return array();
	}
	/**
	 * magic function call
	 * @param   decimal   $value
	 * @param   int       $precision
	 * @return  string    
	 * @throws  Zend_Currency_Exception   IF INVALID VALUE 
	 */
	public function currencycontest($value, $currency = NULL) {
		if($value == "")
		{
			$array = $this->getCurrencyOption($currency);
			$string = $array['symbol'];
			if($value == 0)
				$string = $string."0";
		}
		else
		{
			$string = self::getCurrency()->toCurrency($value, $this->getCurrencyOption($currency));
		}
		return $string;
	}
}