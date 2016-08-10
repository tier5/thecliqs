<?php

class Yncontest_View_Helper_FormMaxEntries extends Zend_View_Helper_Abstract{
	
	public function formMaxEntries($name, $value = null, $attributes = array()){		
		// CODE HERE
		
		
		
		$nameTextMaxEntries = sprintf("%s[%s]",$name, 'text_max_entries');
		
		$html =  
		"
			<td>Maximum entries which a member can submit 
				<input width='20px' height='20px' type='text' name='$nameTextMaxEntries' > <span style='color:LightGray'>0 means no limit</span>
			</td>			
		
		";
		Zend_Registry::get('Zend_Translate')->_('Maximum entries which a member can submit');
		Zend_Registry::get('Zend_Translate')->_('0 means no limit');
				return $html;
	}
}
?>

