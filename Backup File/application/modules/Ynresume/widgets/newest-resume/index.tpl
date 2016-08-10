<?php echo $this -> partial('_listingResume.tpl','ynresume', array(
	'idName' => $this -> identity,
	'class_mode' => $this -> class_mode, 
	'mode_enabled' => $this -> mode_enabled,
	'resumeIds' => $this -> resumeIds,
	'paginator' => $this -> paginator,
	'isWidget' => true,
));?>

