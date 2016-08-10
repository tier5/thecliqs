<?php

class Yncontest_Model_Gateway extends Core_Model_Item_Abstract {

	public function getConfig(){
		if(is_string($this->config)){
			return Zend_Json::decode($this->config);	
		}
		return array();
	}	
	
	public function setConfig(array $config){
		$this->config =  Zend_Json::encode($config);
		return $this;
	}
	
	public function getAdminGatewayForm(){
		$form_class = $this->admin_form;
		return new $form_class;
	}
	
	public function test(){
		return true;
	}

}
