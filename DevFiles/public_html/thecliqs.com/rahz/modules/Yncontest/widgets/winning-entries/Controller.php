<?php
class Yncontest_Widget_WinningEntriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$table = Engine_Api::_()->getDbtable('entries', 'yncontest');
        $Name = $table->info('name');
		$select = $table->select()->from($Name,"$Name.*")->setIntegrityCheck(false);
		
		$select	-> joinLeft("engine4_yncontest_awards","engine4_yncontest_awards.award_id = $Name.award_id");
		$select	-> where("$Name.entry_status=?", 'win')
				-> where("$Name.approve_status = ?","approved")			
				-> order("$Name.modified_date desc")
				-> limit(5);
		$this -> view -> items = $items = $table -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);
		$this -> view -> title = "Winning Entries";
		
		if($totalItems <= 0) {
			$this -> setNoRender();
		}
		
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();	
				
	}	
}
