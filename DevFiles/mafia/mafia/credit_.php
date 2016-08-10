<?
		
	function credit_set($code, $credits){
	global $db;
			
		$old_credits = $db->getField("users", "code", $code, "credits");	
		$old_credits = $old_credits + $credits;			
		$db->updateField("users", "code", $code, "credits", $old_credits);	
	}	
	
	function award_set($code, $type){
	global $db;
	
		$current = $db->getField("users", "code", $code, $type);
		$current = $current + 1;
		$db->updateField("users", "code", $code, $type, $current);
	}
?>