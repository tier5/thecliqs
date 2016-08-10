<?php	           		
	echo $this->partial('_contest-large-list.tpl', 'yncontest', array(
		'items'     => $this->paginator,	
		'height'     => $this->height,
		'width'     => $this->width, 
		'follow'	=> true,
		'flag'		=> $this->flag,      		
	));
?>

<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>
