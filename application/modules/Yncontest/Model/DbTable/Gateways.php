<?php

class Yncontest_Model_DbTable_Gateways extends Engine_Db_Table
{
	
	protected $_rowClass = 'Yncontest_Model_Gateway';
	
	static public function getConfig($gateway){
		
		$self =  new self;
		$gateway =  strtolower($gateway);
		$item =  $self->find($gateway)->current();
		
		if(!is_object($item)){
			throw new Exception("gateway $gateway not found!");
		}
				
		return $item->getConfig();
	}
	
	static public function getSupportedGateways(){
		$self = new self;
		
		$select = $self->select()->where('enabled=1');
		$result  = array();
		
		foreach($self->fetchAll($select) as $item){
			$result[$item->gateway_id] = $item->title;
		}
		return $result;		
	}

}