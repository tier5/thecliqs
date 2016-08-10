<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Api_HeidelPay extends Ynpayment_Api_Paymentgateway 
{
    protected $_sender = '';
	protected $_login = '';
	protected $_pwd = '';
	protected $_channel = '';
	
    public function initialize(Payment_Model_Gateway $gateway) 
    {
		
		$settings = (array) $gateway->config;
		if($settings)
		{
			 $this->_sender = $settings['hp_sender'];
			 $this->_login = $settings['hp_login'];
			 $this->_pwd = $settings['hp_pwd'];
			 $this->_channel = $settings['hp_channel'];
		}
		
    }
	
	public function process_payment_recurring($package, $params)
	{
		//URL fuer Testsystem
		$tableGateWay = Engine_Api::_()->getItemTable('payment_gateway');
		$select = $tableGateWay -> select() -> where("title = 'HeidelPay'") -> limit(1);
		$HeidelPay = $tableGateWay -> fetchRow($select);
		$test_mode = $HeidelPay -> test_mode;
		if($test_mode == 1){
			//$url = "https://test-heidelpay.hpcgw.NET/TransactionCore/XML";
			$url = "https://test-heidelpay.hpcgw.net/sgw/xml";
		}	
		else {
			//$url = "https://heidelpay.hpcgw.NET/TransactionCore/XML";
			$url = "https://heidelpay.hpcgw.net/sgw/xml";
		}
		
		$parameters['SECURITY.SENDER'] = $this->_sender;
		$parameters['USER.LOGIN'] = $this->_login;
		$parameters['USER.PWD'] = $this->_pwd;
		$parameters['TRANSACTION.CHANNEL'] = $this->_channel;
		
		$parameters['ACCOUNT.HOLDER'] = $params['ACCOUNT_HOLDER'];
		$parameters['ACCOUNT.NUMBER'] = $params['ACCOUNT_NUMBER'];
		$parameters['ACCOUNT.BRAND'] = $params['ACCOUNT_BRAND'];
		$parameters['ACCOUNT.EXPIRY_MONTH'] = "";
		$parameters['ACCOUNT.EXPIRY_YEAR'] = "";
		$parameters['ACCOUNT.VERIFICATION'] = "";
		
		//Payment Code -- Auswahl Bezahlmethode und Typ
		$parameters['PAYMENT.CODE'] = "CC.SD"; 
		$parameters['PRESENTATION.CURRENCY'] = $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency');
		$view = Zend_Registry::get('Zend_View');
		//Response URL angeben
		$RESPONSE_URL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'heidelpay-callback',
								), 'ynpayment_subscription', true);
		$parameters['FRONTEND.RESPONSE_URL'] = $RESPONSE_URL;
		//CSS- und/oder Jscript-Datei angeben
		$parameters['FRONTEND.CSS_PATH'] = 'http://' . $_SERVER['HTTP_HOST']. $view->baseUrl() . '/application/modules/Ynpayment/externals/styles/onlycarddetails_new.css';
		$parameters['PRESENTATION.AMOUNT'] = $package -> price;
		$parameters['IDENTIFICATION.TRANSACTIONID'] = $params['IDENTIFICATION_TRANSACTIONID'];
		$parameters['PRESENTATION.USAGE'] = 'Testtransaktion vom '.date("d.m.Y");
		
		
		$parameters['FRONTEND.MODE'] = "DEFAULT";
		
		// Modus ausw�hlen
		if($test_mode == 1)
		{
			$parameters['TRANSACTION.MODE'] = "CONNECTOR_TEST";
		}
		else
		{
			$parameters['TRANSACTION.MODE'] = "LIVE";
		}
		
		
		$parameters['FRONTEND.ENABLED'] = "true";
		$parameters['FRONTEND.POPUP'] = "false";
		//$parameters['FRONTEND.SHOP_NAME'] = '';
		$parameters['FRONTEND.REDIRECT_TIME'] = "0";
		
		
		$parameters['FRONTEND.LANGUAGE_SELECTOR'] = "true";
		$parameters['FRONTEND.LANGUAGE'] = $params['FRONTEND_LANGUAGE'];
		
		$parameters['REQUEST.VERSION'] = $params['RESPONSE_VERSION'];
		
		
		$parameters['NAME.GIVEN'] = $params['NAME_GIVEN'];
		$parameters['NAME.FAMILY'] = $params['NAME_FAMILY'];
		$parameters['ADDRESS.STREET'] = $params['ADDRESS_STREET'];
		$parameters['ADDRESS.ZIP'] = $params['ADDRESS_ZIP'];
		$parameters['ADDRESS.CITY'] = $params['ADDRESS_CITY'];
		$parameters['ADDRESS.COUNTRY'] = $params['ADDRESS_COUNTRY'];
		$parameters['ADDRESS.STATE'] = $params['ADDRESS_STATE'];
		$parameters['CONTACT.EMAIL'] = $params['CONTACT_EMAIL'];
		
		$parameters['ACCOUNT.REGISTRATION'] = $params['IDENTIFICATION_UNIQUEID'];
		//building the postparameter string to send into the WPF
		
		$parameters['JOB.NAME'] = 'Standard Monthly Subscription';
		$parameters['ACTION.TYPE'] = 'DB';
		$parameters['DURATION.NUMBER'] = $package -> recurrence;
		$parameters['DURATION.UNIT'] = strtoupper($package -> recurrence_type);	
		$parameters['EXECUTION.DAYOFMONTH'] = date("m");
		$parameters['EXECUTION.MONTH'] = '*';
			
		$result = '';
		foreach ($parameters AS $key => $value)
			$result .= strtoupper($key).'='.urlencode($value).'&';
		$strPOST = stripslashes($result);
		
		$current_month = "";
		$current_year = "";
		$current_day = "";
		$arr_month = array();
		$arr_year = array();
		
		$recurrence = $package -> recurrence;
		switch ($package -> recurrence_type) 
		{
			/*case 'week':
				$Execution = "<Execution>"
								."<Week>".$recurrence."</Week>"
							."</Execution>";
				break;*/
			case 'month':
				$current_day = date('d');
				switch ($current_day) {
					case '29':	
					case '30':
					case '31':
						$current_day = 'L';
						break;
				}
				$current_month = date('m');
				if($recurrence != 1)
				{
					/*
					array_push($arr_month, $current_month);
										
					$temp_month = 0;
					$i = 0;
					while($temp_month != $current_month)
					{
						if($i == 0)
						{
							$temp_month = $current_month;
						}
						$temp_month = $temp_month + $recurrence;
						if($temp_month <= 12)
						{
							if($temp_month != $current_month)
							{
								array_push($arr_month, $temp_month);
							}
						}
						else 
						{
							$temp_month = $temp_month - 12;
							if($temp_month != $current_month)
							{
								array_push($arr_month, $temp_month);
							}
						}
						$i++;
					}
					$str_month = "";
					for($pos = 0; $pos < count($arr_month); $pos++)
					{
						if($pos == count($arr_month) - 1)
						{
							$str_month .= $arr_month[$pos];
						}
						else
						{
							$str_month .= $arr_month[$pos].',';
						}
					}*/
					
				}
				else 
				{
					$str_month = '*';
				}
				$Execution = "<Execution>"
								."<DayOfMonth>".$current_day."</DayOfMonth>"
								."<Month>".$str_month."</Month>"
							."</Execution>";
				break;
			/*case 'year':
				$current_month = date('m');
				$current_year = date('Y');
				$temp_year = $current_year;
				if($recurrence != 1)
				{
					array_push($arr_year, $current_year);
					$i = 0;
					while($i < 10)
					{
						$temp_year = $temp_year + $recurrence;
						array_push($arr_year, $temp_year);
						$i++;
					}
					$str_year = "";
					for($pos = 0; $pos < count($arr_year); $pos++)
					{
						if($pos == count($arr_year) - 1)
						{
							$str_year .= $arr_year[$pos];
						}
						else
						{
							$str_year .= $arr_year[$pos].',';
						}
					}
				}
				else 
				{
					$str_year = '*';
				}
				$Execution = "<Execution>"
								."<Hour>17</Hour>"
								."<DayOfMonth>10</DayOfMonth>"
								."<Month>".$current_month."</Month>"
								."<Year>".$str_year."</Year>"
							."</Execution>"
							."<Notice>"
							."<Number>3</Number>"
							."<Unit>DAY</Unit>"
							."</Notice>"
							."<Duration>"
							."<Number>3</Number>"
							."<Unit>MONTH</Unit>"
							."</Duration>"
							;
				break;*/
		}
		
		$xml = 
		"<Request version=\"".$params['RESPONSE_VERSION']."\">"
		."<Header>"
			."<Security sender=\"".$this->_sender."\"/>"
		."</Header>"
			."<Transaction mode=\"".$parameters['TRANSACTION.MODE']."\" response=\"SYNC\" channel=\"".$this->_channel."\">"
				."<User login=\"".$this->_login."\" pwd=\"".$this->_pwd."\" />"
				."<Identification>"
					."<TransactionID>".$params['IDENTIFICATION_TRANSACTIONID']."</TransactionID>"
				."</Identification>"
				."<Payment code=\"CC.SD\">"
					."<Presentation>"
						."<Amount>".$package -> price."</Amount>"
						."<Currency>".$currency."</Currency>"
						."<Usage>Order ".date("d.m.Y")."</Usage>"
					."</Presentation>"
				."</Payment>"
				."<Job name=\"Trial Subscripton\">"
					."<Action type=\"DB\" />"
					.$Execution
				."</Job>"
				."<Account registration=\"".$params['IDENTIFICATION_UNIQUEID']."\" />"
			."</Transaction>"
		."</Request>";
		
		//open the request url for the Web Payment Frontend
		
		$cpt = curl_init();
		curl_setopt($cpt, CURLOPT_URL, $url);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
		curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($cpt, CURLOPT_POST, 1);
		curl_setopt($cpt, CURLOPT_POSTFIELDS, "&load=".urlencode($xml));
		$curlresultURL = curl_exec($cpt);
		$curlerror = curl_error($cpt);
		$curlinfo = curl_getinfo($cpt);
		curl_close($cpt);
		$logFile1 = APPLICATION_PATH . '/temporary/log/result.log';
					file_put_contents($logFile1, $curlresultURL, FILE_APPEND);
		return $this -> _parse_return($curlresultURL);
	}
	
	public function _parse_return($content)
	{
		$Result = $this-> _substring_between($content,'<Result>','</Result>');
		$UniqueID = $this-> _substring_between($content,'<UniqueID>','</UniqueID>');
		$Amount = $this-> _substring_between($content,'<Amount>','</Amount>');
		$Currency = $this-> _substring_between($content,'<Currency>','</Currency>');
		$TransactionID = $this-> _substring_between($content,'<TransactionID>','</TransactionID>');
		$returnvalue = array(
			'Result' => $Result,
			'UniqueID' => $UniqueID,
			'Amount' => $Amount,
			'Currency' => $Currency,
			'TransactionID' => $TransactionID,
		);
		return $returnvalue;
	}
	
	public function _substring_between($haystack,$start,$end) 
	{
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) 
		{
			return false;
		} 
		else 
		{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
	
	public function registration($package, $order_id)
	{
		$price = $package -> price;
		//URL fuer Testsystem
		$tableGateWay = Engine_Api::_()->getItemTable('payment_gateway');
		$select = $tableGateWay -> select() -> where("title = 'HeidelPay'") -> limit(1);
		$HeidelPay = $tableGateWay -> fetchRow($select);
		$test_mode = $HeidelPay -> test_mode;
		if($test_mode == 1){
			$url = "https://test-heidelpay.hpcgw.net/sgw/gtw";
		}	
		else {
			$url = "https://heidelpay.hpcgw.net/sgw/gtw";
		}
		$parameters['SECURITY.SENDER'] = $this->_sender;
		$parameters['USER.LOGIN'] = $this->_login;
		$parameters['USER.PWD'] = $this->_pwd;
		// Channel f�r CC, OT Sofort, DC, DD, PayPal
		$parameters['TRANSACTION.CHANNEL'] = $this->_channel;
		
		$parameters['ACCOUNT.HOLDER'] = "";
		$parameters['ACCOUNT.NUMBER'] = "";
		//$parameters['ACCOUNT.BRAND'] = "PAYPAL";
		$parameters['ACCOUNT.BRAND'] = "";
		$parameters['ACCOUNT.EXPIRY_MONTH'] = "";
		$parameters['ACCOUNT.EXPIRY_YEAR'] = "";
		$parameters['ACCOUNT.VERIFICATION'] = "";
		
		//Payment Code -- Auswahl Bezahlmethode und Typ
		$parameters['PAYMENT.CODE'] = "CC.RG";  // Registrierung Lastschrift
		$parameters['PRESENTATION.CURRENCY'] = $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency');
		$view = Zend_Registry::get('Zend_View');
		//Response URL angeben
		$RESPONSE_URL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'heidelpay-registration',
								), 'ynpayment_subscription', true);
		$parameters['FRONTEND.RESPONSE_URL'] = $RESPONSE_URL;
		//CSS- und/oder Jscript-Datei angeben
		//$parameters['FRONTEND.CSS_PATH'] = 'http://' . $_SERVER['HTTP_HOST']. $view->baseUrl() . '/application/modules/Ynpayment/externals/styles/onlycarddetails_new.css';
		//$parameters['FRONTEND.JSCRIPT_PATH'] = "http://127.0.0.1/wpf/wpfui.js";
		$parameters['PRESENTATION.AMOUNT'] = $price;
		$parameters['IDENTIFICATION.TRANSACTIONID'] = $order_id;
		$parameters['PRESENTATION.USAGE'] = 'Testtransaktion vom '.date("d.m.Y");
		
		$parameters['FRONTEND.MODE'] = "DEFAULT";
		
		if($test_mode == 1)
		{
			$parameters['TRANSACTION.MODE'] = "CONNECTOR_TEST";
		}
		else
		{
			$parameters['TRANSACTION.MODE'] = "LIVE";
		}
		
		$parameters['FRONTEND.ENABLED'] = "true";
		$parameters['FRONTEND.POPUP'] = "false";
		$parameters['FRONTEND.SHOP_NAME'] = $view -> layout() -> siteinfo['title'];
		$parameters['FRONTEND.REDIRECT_TIME'] = "0";
		
		$parameters['FRONTEND.LANGUAGE_SELECTOR'] = "true";
		$parameters['FRONTEND.LANGUAGE'] = "en";
		$parameters['REQUEST.VERSION'] = "1.0";
		
		$parameters['NAME.GIVEN'] = "";
		$parameters['NAME.FAMILY'] = "";
		$parameters['ADDRESS.STREET'] = "";
		$parameters['ADDRESS.ZIP'] = "";
		$parameters['ADDRESS.CITY'] = "";
		$parameters['ADDRESS.COUNTRY'] = "";
		$parameters['ADDRESS.STATE'] = "";
		$parameters['CONTACT.EMAIL'] = "";
		
		if($test_mode == 1){
			$parameters['NAME.GIVEN'] = "Markus";
			$parameters['NAME.FAMILY'] = "Mustermann";
			$parameters['ADDRESS.STREET'] = "Musterstrasse 1";
			$parameters['ADDRESS.ZIP'] = "12345";
			$parameters['ADDRESS.CITY'] = "Musterstadt";
			$parameters['ADDRESS.COUNTRY'] = "DE";
			$parameters['ADDRESS.STATE'] = "";
			$parameters['CONTACT.EMAIL'] = "test@example.com";
		}
		//building the postparameter string to send into the WPF
		
		$result = '';
		foreach ($parameters AS $key => $value)
			$result .= strtoupper($key).'='.urlencode($value).'&';
		$strPOST = stripslashes($result);
		
		//open the request url for the Web Payment Frontend
		
		$cpt = curl_init();
		curl_setopt($cpt, CURLOPT_URL, $url);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
		curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($cpt, CURLOPT_POST, 1);
		curl_setopt($cpt, CURLOPT_POSTFIELDS, $strPOST);
		$curlresultURL = curl_exec($cpt);
		$curlerror = curl_error($cpt);
		$curlinfo = curl_getinfo($cpt);
		curl_close($cpt);
		
		// parse results
		
		$r_arr=explode("&",$curlresultURL);
		foreach($r_arr AS $buf)
		{
			$temp=urldecode($buf);
			$temp=explode("=",$temp,2);
			$postatt=$temp[0];
			$postvar=$temp[1];
			$returnvalue[$postatt]=$postvar;
		}
		return $returnvalue;
	}
	
    public function process_payment($package, $order_id, $notificationURL = NULL, $response_url = NULL) 
    {
		$price = $package -> price;
		//URL fuer Testsystem
		$tableGateWay = Engine_Api::_()->getItemTable('payment_gateway');
		$select = $tableGateWay -> select() -> where("title = 'HeidelPay'") -> limit(1);
		$HeidelPay = $tableGateWay -> fetchRow($select);
		$test_mode = $HeidelPay -> test_mode;
		if($test_mode == 1){
			$url = "https://test-heidelpay.hpcgw.net/sgw/gtw";
		}	
		else {
			$url = "https://heidelpay.hpcgw.net/sgw/gtw";
		}
		
		$parameters['SECURITY.SENDER'] = $this->_sender;
		$parameters['USER.LOGIN'] = $this->_login;
		$parameters['USER.PWD'] = $this->_pwd;
		$parameters['TRANSACTION.CHANNEL'] = $this->_channel;
		
		$parameters['ACCOUNT.HOLDER'] = "";
		$parameters['ACCOUNT.NUMBER'] = "";
		$parameters['ACCOUNT.BRAND'] = "";
		$parameters['ACCOUNT.EXPIRY_MONTH'] = "";
		$parameters['ACCOUNT.EXPIRY_YEAR'] = "";
		$parameters['ACCOUNT.VERIFICATION'] = "";
		
		//$parameters['PAYMENT.CODE'] = "CC.DB";  // Direkte Belastung
		$parameters['PRESENTATION.CURRENCY'] = $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency');
		$view = Zend_Registry::get('Zend_View');
		//Response URL angeben
		if(empty($response_url))
		{
			$response_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'heidelpay-callback',
								), 'ynpayment_subscription', true);
		}						
		$parameters['FRONTEND.RESPONSE_URL'] = $response_url;
		//CSS- und/oder Jscript-Datei angeben
		//$parameters['FRONTEND.CSS_PATH'] = 'http://' . $_SERVER['HTTP_HOST']. $view->baseUrl() . '/application/modules/Ynpayment/externals/styles/onlycarddetails_new.css';
		//$parameters['FRONTEND.JSCRIPT_PATH'] = "http://127.0.0.1/wpf/wpfui.js";
		$parameters['PRESENTATION.AMOUNT'] = $price;
		$parameters['IDENTIFICATION.TRANSACTIONID'] = $order_id;
		$parameters['PRESENTATION.USAGE'] = 'Testtransaktion vom '.date("d.m.Y");
		
		
		$parameters['FRONTEND.MODE'] = "DEFAULT";
		
		if($test_mode == 1)
		{
			$parameters['TRANSACTION.MODE'] = "CONNECTOR_TEST";
		}
		else 
		{
			$parameters['TRANSACTION.MODE'] = "LIVE";
		}
		
		$parameters['FRONTEND.ENABLED'] = "true";
		$parameters['FRONTEND.POPUP'] = "false";
		$parameters['FRONTEND.SHOP_NAME'] = $view -> layout() -> siteinfo['title'];
		$parameters['FRONTEND.REDIRECT_TIME'] = "0";
		
		$parameters['FRONTEND.LANGUAGE_SELECTOR'] = "true";
		$parameters['FRONTEND.LANGUAGE'] = "en";
		$parameters['REQUEST.VERSION'] = "1.0";
		
		$parameters['NAME.GIVEN'] = "";
		$parameters['NAME.FAMILY'] = "";
		$parameters['ADDRESS.STREET'] = "";
		$parameters['ADDRESS.ZIP'] = "";
		$parameters['ADDRESS.CITY'] = "";
		$parameters['ADDRESS.COUNTRY'] = "";
		$parameters['ADDRESS.STATE'] = "";
		$parameters['CONTACT.EMAIL'] = "";
		
		$parameters['NAME.GIVEN'] = "Markus";
		$parameters['NAME.FAMILY'] = "Mustermann";
		$parameters['ADDRESS.STREET'] = "Musterstrasse 1";
		$parameters['ADDRESS.ZIP'] = "12345";
		$parameters['ADDRESS.CITY'] = "Musterstadt";
		$parameters['ADDRESS.COUNTRY'] = "DE";
		$parameters['ADDRESS.STATE'] = "";
		$parameters['CONTACT.EMAIL'] = "test@example.com";
		
		//building the postparameter string to send into the WPF
		
		$result = '';
		foreach ($parameters AS $key => $value)
			$result .= strtoupper($key).'='.urlencode($value).'&';
		$strPOST = stripslashes($result);
		
		//open the request url for the Web Payment Frontend
		
		$cpt = curl_init();
		curl_setopt($cpt, CURLOPT_URL, $url);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
		curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($cpt, CURLOPT_POST, 1);
		curl_setopt($cpt, CURLOPT_POSTFIELDS, $strPOST);
		$curlresultURL = curl_exec($cpt);
		$curlerror = curl_error($cpt);
		$curlinfo = curl_getinfo($cpt);
		curl_close($cpt);
		
		// parse results
		$returnvalue = array();
		$r_arr=explode("&",$curlresultURL);
		foreach($r_arr AS $buf)
		{
			$temp=urldecode($buf);
			$temp=explode("=",$temp,2);
			$postatt=$temp[0];
			$postvar=$temp[1];
			$returnvalue[$postatt]=$postvar;
		}
		
		return $returnvalue;
    }
}

?>