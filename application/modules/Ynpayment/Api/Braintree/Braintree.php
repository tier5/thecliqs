<?php

class Ynpayment_Api_Braintree_Braintree extends Core_Api_Abstract
{
	function includeFiles(){
		require(dirname(__FILE__) . '/lib/Braintree.php');
	}
}