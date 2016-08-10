<?php
class Yncontest_Plugin_Task_Timeout extends Core_Plugin_Task_Abstract {
	public function execute() {
		
		$log = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/event.txt'));
		
		$table = Engine_Api::_() -> getDbtable('contests', 'yncontest');
		$Name = $table->info('name');
		$select = $table->select()->where("$Name.contest_status = 'published'") -> where("$Name.end_date < ?", date('Y-m-d H:i:s'));
		
		$contests = $table->fetchAll($select);
		 
		foreach ($contests as $contest){
				$admin = Engine_Api::_() -> user() -> getUser(1);
				$contest->closeContest($admin);
				$log->log(print_r($contest->contest_id,true),Zend_Log::DEBUG);
			
		}
	}
}
?>
