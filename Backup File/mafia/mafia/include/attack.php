<?
	
function getGuns($men_type, $gun_type){
		
	if ($men_type <= $gun_type) {
	    $men_type_guns = $men_type;
		$gun_type = $gun_type - $men_type;
		$men_type = 0;
	}else {
		$men_type = $men_type - $gun_type;
		$men_type_guns = $gun_type;
		$gun_type = 0;
	}
	
	return array($men_type_guns, $men_type, $gun_type);
}
	
?>