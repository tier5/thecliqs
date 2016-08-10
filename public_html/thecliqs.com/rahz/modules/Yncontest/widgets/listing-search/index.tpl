
<?php	           		
	echo $this->partial('_contest-large-list.tpl', 'yncontest', array(
		'items'     => $this->paginator,	
		'height'     => $this->height,
		'width'     => $this->width, 		
		'flag'		=> $this->flag,      		
	));
?>		 

<?php echo $this->paginationControl($this->paginator, null, null, array(
	'pageAsQuery' => true,
	'params' => $this->values,
  )); ?>



